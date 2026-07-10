# CPT URLs, `/blog/` prefix, and crawlability (2026-07-10)

<!-- changelog: Fixed -->

Found during a full audit in which every route was rendered through a real
WordPress boot (PHP 8.4, `E_ALL`) rather than reasoned about statically.

## What was wrong

**1. Five CPTs had public URLs but no template.** `agency`, `partner_logo`,
`specialty_hub`, `stat_metric` and `testimonial` were all
`public: true, publicly_queryable: true`. The theme has no `single-<cpt>.php`, and
`single.php` unconditionally `require`s `single-blog.php` — the *blog article*
layout. So **22 URLs** rendered an article page with "retour aux articles", a
fabricated read-time, an author byline and the recent-articles rail, while showing
none of the agency's own data (rating, website). A `stat_metric` — literally a
number and a label — got a full article layout.

They returned **HTTP 200 and were indexable**: the theme emits no `noindex`
anywhere on the front end. They were saved only by being unlinked and absent from
the sitemap. `SITEMAP.md` already described them as *"blocs de contenu … pas des
pages"* — the code disagreed with the doc.

**2. Every CPT and taxonomy was nested under `/blog/`.** The blog permalink base is
`/blog/%postname%/`, and `with_front` defaults to `true`, so WordPress served
`/blog/agency/…`, `/blog/service/…`, `/blog/city/…`. **CLAUDE.md and SITEMAP.md
both documented `/service/` and `/city/`.** Both were wrong.

**3. `Sample Page` was in the sitemap.** WordPress's install-time placeholder
(post ID 2, English boilerplate) was still `publish`, and the sitemap generator
discovers pages dynamically — it only skips the front page, protected pages and
`annuaire`.

## What changed

### `acf-json/post_type_*.json` (all five)

```
publicly_queryable            : true  -> false
rewrite.permalink_rewrite     : post_type_key -> no_permalink   (=> rewrite: false)
query_var                     : post_type_key -> none           (=> query_var: false)
rewrite.with_front            : true  -> false
has_archive (agency only)     : true  -> false
exclude_from_search           : false -> true   … EXCEPT agency, see below
```

### `acf-json/taxonomy_*.json` (both)

`rewrite.with_front: true -> false`. They stay `public: true` and keep listing
agencies. URLs are now `/service/<term>/` and `/city/<term>/`, matching the docs.

### `functions.php` — three additions, no existing code touched

- **§5c-bis `v5_digital_flush_rewrites_once()`** — `rewrite_rules` is a stored
  snapshot; changing these args does not update it. Gated by
  `V5_DIGITAL_REWRITE_VERSION`, hooked on **`init`** (not `admin_init`) so a
  *visitor* never hits stale rules waiting for an admin to log in. Soft flush:
  CPT/taxonomy rules live in the DB option, never in `.htaccess`.
- **§5c-ter `v5_digital_exclude_cpts_from_search()`** — strips the CPTs from the
  main `?s=` query.
- **§5d legacy redirect** — 301s `/blog/service/<t>/` → `/service/<t>/` and
  `/blog/city/<t>/` → `/city/<t>/`. Deliberately does **not** redirect the old CPT
  singles (see below).

### Content (in the database — does NOT travel with the deploy)

`Sample Page` set to **draft**. Sitemap: 10 → 9 URLs.

## The fix I got wrong first

Setting only `publicly_queryable: false` did **not** produce a 404. It produced a
**301 to the homepage**, and I initially shipped that.

Traced in core: the rewrite rules survive (`WP_Post_Type::add_rewrite_rules()`
gates the *query var* on `is_post_type_viewable()`, not the rules), so
`WP::parse_request()` matches the rule, then discards the now-non-public query
var. The request degrades to empty query vars → the static front page →
`redirect_canonical()` sees the path isn't `/` and issues
`wp_redirect( home_url('/'), 301 )`. It is emergent, undocumented behaviour,
contingent on a static front page.

Google names that pattern explicitly:

> "Don't redirect many old URLs to one irrelevant single URL destination, such as
> the home page… This can confuse users and might be treated as a `soft 404`."
> — [Search Central, site moves](https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes)

John Mueller: *"we mostly treat them as 404s anyway (they're soft-404s), so
there's no upside… make a better 404 page instead."*

So `rewrite: false` was added, which stops the rules being generated at all.
**`query_var: false` alone is not a substitute and is actively harmful**: with
`rewrite` still on, `add_rewrite_tag()` substitutes `post_type=…&name=…` — both
*always-public* query vars — and the single renders again.

For URLs that should never have existed and have no equivalent, Google wants a
**404 or 410**, never a redirect to an unrelated page. The old
`/blog/agency/<slug>/` URLs therefore 404 too. Only the taxonomy archives get a
301, because there a true equivalent exists — and WordPress does **not** redirect
after a rewrite-structure change (`redirect_canonical()` only enforces the current
rules plus per-post `_wp_old_slug` history).

## A regression I introduced and caught

Setting `exclude_from_search: true` on `agency` **silently emptied
`/service/<term>/` and `/city/<term>/`** — `found_posts` went to 0, with no error.

A taxonomy archive runs a `WP_Query` with no explicit `post_type`. `WP_Query::get_posts()`
resolves it through `get_post_types(array('exclude_from_search' => false))` twice —
once to map the taxonomy to its post types, once to expand `'any'`. Excluding
`agency` removes it from both.

`agency` therefore keeps **`exclude_from_search: false`**, and the CPTs are kept
out of site search by `pre_get_posts` instead — the documented remedy. This is a
trap: setting that flag "for tidiness" breaks the browse pages with no visible
symptom. It is called out in `CLAUDE.md` and `SITEMAP.md`.

## Verification

Nothing here was assumed. A baseline render of every route was captured *before*
the change and diffed against the result.

| Check | Result |
| --- | --- |
| Every page/post route byte-identical to the pre-change baseline | pass (9 routes) |
| Theme diagnostics (`E_ALL`) across all routes | **0** |
| 5 CPT singles + `agency` archive | native **404** (was 200, then 301) |
| `?post_type=agency&name=…`, `?post_type=agency&p=…`, `?p=<agency id>` | 404 |
| `?agency=<slug>` | renders the homepage, not the agency |
| `/service/web-design/` | 200, **3 agencies**, `<article>` × 3 |
| `/city/casablanca/` | 200, **2 agencies** |
| `?s=nexamedia` | 0 results (was 1) |
| `?s=hello` | still finds the post |
| `/blog/service/<t>/`, `/blog/city/<t>/` | **301** to the new URL |
| `/blog/agency/<slug>/` | 404 (no redirect — correct) |
| Rewrite rules | 191 → **106**; zero `^agency/`, `^testimonial/`, … rules |
| Sitemap | 9 entries, valid XML, no `sample-page` |
| Admin: `show_ui`, ACF field groups, layout queries | unchanged (6 agencies, 3 testimonials, 6 logos, 4 stats, 3 specialties) |
| i18n (guard, `l10n`, `Agencies`, `[Home] Banner`) | unchanged |
| `php -l` | 32/32 clean |

**Flush proven, not assumed:** `rewrite_rules` and `v5_digital_rewrite_version`
were deleted from the database, then a single anonymous front-end request was
issued. The rules rebuilt (9857 bytes), `service`/`city` present, zero CPT rules,
version stamped. That is exactly what production will do on deploy.

## Crawl map after the change

BFS from `/`, rendering each page through WordPress and following its links:

```
/                              depth 0   200   [sitemap]
├── /about/                    depth 1   200   [sitemap]
├── /blog/                     depth 1   200   [sitemap]
│   └── 4 posts                depth 2   200   [sitemap]
├── /contact/                  depth 1   200   [sitemap]   (?subject=… canonicalises to /contact/)
├── /methodologie/             depth 1   200   [sitemap]
└── /annuaire/                 depth 1   200   (excluded from the sitemap on purpose)
```

**12 crawlable URLs, maximum depth 2.** Nothing in the sitemap is unreachable.
Depth is not a problem; the content surface is small (4 posts, 5 pages).

`/service/<term>/` and `/city/<term>/` are **orphans**: 200, real content, but no
internal links, no sitemap entry, and **no canonical** (WordPress adds none to
archives). Invisible to Google today. See the `SITEMAP.md` checklist — the moment
`/annuaire/` links them they become indexable pages rendered by the 18-line
`index.php` fallback, and they will need either a real archive template or
`noindex, follow`.

## Known latent issue

`v5_digital_register_cpts()` — the PHP fallback that only runs if `acf-json/` is
ever missing — still registers these CPTs with `public => true`. It never fires in
practice (the JSON always wins), but if it did, the 22 URLs would return. Not
aligned here because it means editing five `register_post_type()` calls that no
request currently executes.

## How to revert

Restore the seven `acf-json/*.json` files, delete sections 5c-bis / 5c-ter / 5d
from `functions.php`, then flush rewrite rules once (Settings → Permalinks → Save).
Set `Sample Page` back to `publish` if you want it in the sitemap again.
