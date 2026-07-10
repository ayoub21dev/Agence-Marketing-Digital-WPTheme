# Agence Marketing Digital — Theme Guide

WordPress theme for a **French-language digital-agency directory / marketplace** (Morocco-focused). Tailwind + ACF-driven flexible content, GSAP motion, Polylang-ready. This folder is the **git repo root**; pushing to `main` deploys it.

---

## Local dev, build & deploy (read first)

- **Local environment:** WordPress Studio, SQLite DB. To run WP-CLI against the local site, see the WP-CLI section in `../../../AGENTS.md` (the wrapper: `bash "/c/Users/Ayoub JALYTA/wp-cli/wp-studio.sh" <args>`). Use it to verify edits against the real DB (menus, options, posts).
- **Edit → preview:** PHP/CSS/JS edits show on the local site on refresh (no build for PHP). **Content** (pages, posts, menus, images) lives in the DB, not in files — it does NOT move with git.
- **Tailwind is compiled AND committed.** Source `src/tailwind.css` → output `assets/css/tailwind.css`. There is **no build step on the server**. If you add/change Tailwind classes in any `.php`, you MUST rebuild before committing or the styles won't appear in production:
  - `npm run build` (minified, one-shot) or `npm run watch` (dev).
  - Tailwind scans `./*.php`, `./template-parts/**/*.php`, `./assets/js/**/*.js` (see `tailwind.config.js`).
- **Deploy:** GitHub Actions FTP-deploy (`.github/workflows/deploy1.yml`) on push to `main` → Hostinger `/public_html/wp-content/themes/agence-marketing-digital/`. Commit/push only when asked.
- **`.gitignore`:** `node_modules/` ignored; compiled `assets/css/tailwind.css` IS committed on purpose.

---

## Architecture

### functions.php (~5,600 lines) — the engine

Organized in banner-commented sections. **Navigate by banner, not by line number** —
the numbers below drift with every change (they were ~1,200 lines out of date before
2026-07-10). Grep: `grep -nE "^// [0-9]" functions.php`.

| Banner | ~line | What |
|---|---|---|
| *(no banner)* | 1–108 | **ACF safety wrappers**: `v5_digital_get_field()`, `v5_digital_have_rows()`, `v5_digital_the_row()`, `v5_digital_get_row_layout()`, `v5_digital_get_sub_field()` — all no-op safely if ACF is inactive. **Templates must use these, not raw ACF functions.** |
| `1.` | 109 | **CPT/taxonomy registration** — hooked to `init` priority 20, but **early-returns whenever `acf-json/post_type_*.json` exists**, which it always does. So this is a fallback that never runs; the JSON is the source of truth. (Its `__()` labels are therefore dead code — see `changes/2026-07-09-acf-admin-translation.md`.) |
| `2.` | 286 | **ACF field groups in PHP** — same story: a fallback used only if `acf-json/group_*.json` is absent. |
| `2b.` | 2281 | **Page builder — live section preview** (admin only): the eye-button modal, the grouped "Add Section" picker, `v5_digital_layout_categories()`, the `wp_ajax_v5_section_preview` endpoint. |
| `3.` | 2740 | **Asset enqueue** — `v5_digital_enqueue_assets()`: tailwind.css, theme-styles.css, theme-scripts.js, versioned by `filemtime`. |
| `3b.` | 2783 | **Exit-intent newsletter popup** — `v5_digital_exit_intent_enabled()` gate (`is_singular('post')`). |
| `4.` | 2802 | **Activation seeding** — `v5_digital_setup_theme_content()` on `after_switch_theme`: creates pages, the primary + 4 footer menus, seeds demo CPT content. ⚠ Its nav-menu **titles are written to the database**, which is why those 6 strings are excluded from `languages/en_US.mo`. |
| `5.` | 4115 | **Theme setup / menus** — `load_theme_textdomain()` **must stay the first statement** (see below), then `register_nav_menus()`, title-tag, post-thumbnails. |
| `5b.` | 4148 | **Polylang string integration** — `v5_t()` + `v5_digital_ui_strings()`. **Front end only.** |
| `5c.` | 4174 | **ACF admin language** — `acf/settings/l10n_textdomain` + the guard that turns translation **off** on ACF's structure editors, so an English-profile admin cannot save English labels back into `acf-json/`. |
| `5c-bis.` | 4603 | **One-shot rewrite flush** — `v5_digital_flush_rewrites_once()`, gated by `V5_DIGITAL_REWRITE_VERSION`, on `init` so a visitor never hits stale rules. **Bump that constant whenever you touch CPT/taxonomy rewrite args.** |
| `5c-ter.` | 4631 | **Keep the data-container CPTs out of `?s=`** — `pre_get_posts`; required because `agency` must keep `exclude_from_search: false`. |
| `5d.` | 4652 | **Legacy taxonomy 301s** — `/blog/service/<t>/` → `/service/<t>/`. WordPress does *not* redirect after a rewrite-structure change. |
| `6.` | 4698 | **Dynamic XML sitemap** — custom `/sitemap.xml`; core's `/wp-sitemap.xml` is disabled. Discovers published pages dynamically (skips the front page, protected pages and `annuaire`). |
| *(no banner)* | ~5359+ | **Data migrations & self-healing** — `v5_digital_run_data_migrations()` on `admin_init`, gated by `V5_DIGITAL_MIGRATION_VERSION`: blog CPT→posts, category backfill, author sync, menu-name fixes, footer-menu setup. |

### Conventions
- **Function prefix:** `v5_digital_*` (and a few `v5_*`). Match this when adding functions.
- **Translation:** wrap user-facing strings in `v5_t('French text')`. The string map is `v5_digital_ui_strings()` (~line 3350); strings are registered with Polylang on `init`. Falls back to the French literal if Polylang is off.
- **Graceful degradation:** everything checks for ACF/Polylang before using them.
- **Known platform gotcha:** never use `GLOB_BRACE` — it's undefined on some PHP builds (incl. local Studio). Use two `glob()` calls merged instead (see `v5_digital_acf_json_manages_content_types()`).

---

## Content model (ACF)

CPTs and taxonomies are defined as JSON in `acf-json/` (ACF "local JSON" sync — edits in the ACF admin UI save back to these files, so they version in git).

**CPTs:** `agency` (title/editor/excerpt; taxonomies below), `partner_logo`, `specialty_hub`, `stat_metric`, `testimonial`.
All five have **no public front-end URL**: `publicly_queryable: false` **and**
`rewrite.permalink_rewrite: "no_permalink"` (→ `rewrite: false`) **and**
`query_var: "none"`. They exist to be queried by the layout templates
(picks → `agency`, outcomes → `testimonial`, …), not to be visited. Re-enable on
`agency` only once a real `single-agency.php` exists — `single.php` currently
renders the *blog article* layout for anything.

**Taxonomies:** `agency_service` (`/service/`), `agency_city` (`/city/`) — both on `agency`,
both public. Their archives render through the `index.php` fallback.

> **Four load-bearing flags in `acf-json/`. All were wrong before 2026-07-10.**
>
> - **`rewrite: false`, not just `publicly_queryable: false`.** With rewrite rules
>   still generated, `WP::parse_request()` drops the (now non-public) query var,
>   the request degrades to the front page, and `redirect_canonical()` **301s to
>   the homepage**. Google calls that pattern a soft 404
>   ([Search Central](https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes)).
>   With `rewrite: false` no rule matches and WordPress returns a clean 404.
> - **`query_var: "none"` is not a substitute.** With `rewrite` still on, a false
>   `query_var` makes `add_rewrite_tag()` emit `post_type=…&name=…` — both public
>   query vars — and the single *renders again*. Set both.
> - **`with_front: false`** on every CPT and taxonomy. The blog permalink base is
>   `/blog/%postname%/`, so `with_front: true` silently nests everything under it
>   (`/blog/service/seo/`).
> - **`exclude_from_search: false` on `agency`.** A taxonomy archive runs a
>   `WP_Query` with no `post_type`, which resolves through
>   `get_post_types(['exclude_from_search' => false])`. Set it to `true` and
>   `/service/…` and `/city/…` return **zero agencies**, silently. The CPTs are
>   kept out of site search by `v5_digital_exclude_cpts_from_search()` instead.
>
> Changing any of these requires a rewrite flush. `v5_digital_flush_rewrites_once()`
> (§5c-bis) does it once per `V5_DIGITAL_REWRITE_VERSION`, on `init` so a **visitor**
> never hits stale rules. Bump that constant whenever you touch these flags.

**Field groups (`acf-json/group_*.json`):**
- `group_agency_fields` — logo_text/image/url, rating_value, review_count, agency_rank, website
- `group_specialty_fields` — icon_svg, direct_link_parameter, sub_services (repeater)
- `group_stat_fields` — stat_number, stat_label
- `group_testimonial_fields` — rating, author_role/image, hired_agency_*, project, result
- `group_partner_logo_fields` — logo_image_media, logo_image_url
- `group_blog_meta` (post) — badge, read_time, author_name, cover_image_url/media
- `group_blog_content` (post) — `blog_layouts` flexible content → `agency_reviews_block` (repeater of agency reviews)
- `group_homepage_fields` (pages) — **`page_layouts` flexible content**, the page builder (see below)

---

## Template system

WordPress hierarchy + an ACF flexible-content page builder.

| Context | Template | Notes |
|---|---|---|
| Front page `/` | `front-page.php` | loops `page_layouts` |
| Generic page | `page.php` | native editor content OR `page_layouts` |
| Single post | `single.php` → `single-blog.php` | article + optional `blog_layouts` |
| 404 | `404.php` | animated hero + recent posts |
| Fallback | `index.php` | minimal |
| Header/Footer | `header.php` / `footer.php` | nav from DB menus |

### Flexible-content dispatch
`front-page.php` / `page.php` loop the `page_layouts` rows and dispatch by layout name:
```php
while (v5_digital_have_rows('page_layouts')) : v5_digital_the_row();
    $layout = v5_digital_get_row_layout();          // e.g. "hero_section"
    $clean  = str_replace('_section', '', $layout); // "hero"
    get_template_part('template-parts/layouts/' . $clean); // hero.php
endwhile;
```
So **ACF layout `foo_section` ⇒ file `template-parts/layouts/foo.php`.** To add a section: add the layout to the `page_layouts` flexible-content group (in ACF UI / JSON) AND create the matching `template-parts/layouts/<name>.php`.

**Layout files** (`template-parts/layouts/`): hero, common_hero, stats_band, search_filter, logos_band, challenge, approach, outcomes, picks, specialties, guides, blog_posts_grid, footer_cta, contact_form, newsletter_cta, methodology_process, methodology_evidence, methodology_monitor, about_grid, about_cta. Many query CPTs directly (picks→agency, specialties→specialty_hub, stats_band→stat_metric, logos_band→partner_logo, outcomes→testimonial) and have static fallbacks when empty.

### Navigation
- **Header menu:** `v5_digital_get_primary_menu_items()` reads the WP menu on the `primary` location (Polylang-aware), with `v5_digital_nav_fallback_links()` as a hardcoded fallback (accueil, blog, à propos, méthodologie, contact). Menus are **DB content**, so local ≠ production unless each site's menu matches.
- **Footer:** 4 columns by location (`footer_explore`, `footer_resources`, `footer_villes`, `footer_legal`) via `v5_digital_render_footer_column()`. No hardcoded fallback — an unassigned location renders nothing.

---

## Frontend assets
- `assets/js/theme-scripts.js` — GSAP motion system (respects reduced-motion), logo marquee, custom selects, Ctrl/Cmd+K search palette, matchmaker wizard, mobile menu, FAQ accordions, filter redirects to `/annuaire/`.
- `assets/css/theme-styles.css` — animations, nav active underline, accordion, motion reveals (hand-written; loaded after tailwind.css).
- Icons: Lucide (deferred). Fonts: Inter (body), Space Grotesk (`font-display`), JetBrains Mono. Brand color = `brand-600` (`#2563eb`).
