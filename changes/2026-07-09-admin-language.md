# Admin language: French backoffice + English for admins who need it (2026-07-09)

<!-- changelog: Added -->

## The actual starting state (not what it looked like)

The theme's strings are French, so the backoffice *felt* French. It wasn't:

```
Site Language (WPLANG) = ''      -> en_US
per-user 'locale' meta  = all ''  -> everyone inherits the site language
languages/              = did not exist
Polylang                = NOT INSTALLED
```

| Layer | Was | Why |
| --- | --- | --- |
| WP core admin chrome | **English** | `WPLANG` empty |
| Theme admin strings | **French** | 105 `__()` calls whose *msgid* is French, with no `.mo` to translate them |
| ACF field / layout labels | **French** | raw literals in `acf-json/*.json`, never passed through `__()` |
| Front end | **French** | hardcoded; `v5_t()` falls back to the French literal because Polylang is absent |

So the backoffice was **mixed** — English chrome, French theme — and the French-speaking
admins it was built for got the worst of it. `load_theme_textdomain()` was already
called correctly on `after_setup_theme`; it just had no `languages/` folder to read.

**Polylang was never going to fix this.** `pll__()` / `pll_register_string()` are
front-end string translation only; the admin UI locale is core's business
(`determine_locale()`). The two are orthogonal.

## What was decided

French-as-msgid is strong WordPress convention, not a rule — the
[i18n guidelines](https://developer.wordpress.org/apis/internationalization/internationalization-guidelines/)
assume `msgid` = English source. The downsides (translate.wordpress.org, plural
rules) only bite a theme that is distributed; this one is bespoke and FTP-deployed.

So rather than rewrite 105 msgids, we ship **`languages/en_US.mo` mapping French → English**.
Non-idiomatic (inverted gettext), but correct for a closed single-market build, and
reversible.

## ⚠ Deployment order matters — do this first

**Set Settings → General → Site Language to `Français` BEFORE (or with) this deploy.**

Verified by reading WP 7.0 core: `_load_textdomain_just_in_time()` calls
`determine_locale()`, and for a theme's custom path builds `"{$path}{$locale}.mo"`.

- `determine_locale()` returns **`get_user_locale()` in wp-admin** and `get_locale()` on the front end.
- With `WPLANG` still empty, every admin's locale is `en_US`, so `en_US.mo` loads for
  **everyone** and the whole backoffice flips to English — the opposite of the intent.
- With Site Language = `fr_FR`: WP looks for `languages/fr_FR.mo`, doesn't find it, and
  falls back to the msgid, which is already French. Nothing to ship for French.
  An admin who then picks **English** in their profile gets `en_US.mo`.

Note the profile Language selector is **hidden while Site Language is `en_US`** — which
is why nobody could pick a language before.

## Files

### New: `languages/`

| File | Deployed? | Purpose |
| --- | --- | --- |
| `en_US.mo` | **yes** | the compiled catalogue WordPress actually reads |
| `en_US.po` | no | editable source (Poedit / Loco Translate) |
| `agence-marketing-digital.pot` | no | template listing all 105 source strings + code references |

`.po`/`.pot` are excluded in `deploy1.yml` (they are build sources, like `src/`).

**Naming is not a style choice.** A *theme* `.mo` is named by locale only —
`en_US.mo`. `agence-marketing-digital-en_US.mo` is the *plugin* convention and would
silently fail to load. Confirmed against core (`{$path}{$locale}.mo`).

### Edited: `functions.php` — `load_theme_textdomain()` moved to the top of setup

Found by audit, after the catalogue existed. `v5_digital_theme_setup()` called
`register_nav_menus()` — five `__()` calls — **before** `load_theme_textdomain()`.

At that moment the theme's `/languages` path is not yet registered, so those
`__()` calls fall through to just-in-time loading, which only looks in
`WP_LANG_DIR/themes/`, finds nothing, and then
`get_translations_for_domain()` **caches a `NOOP_Translations` for the whole
domain** (verified in WP 7.0 `wp-includes/l10n.php`).

`load_theme_textdomain()` clears that NOOP, so every *later* `__()` recovers —
which is why the bug is nearly invisible. But the five strings already evaluated
keep their untranslated French msgid. Result: **Appearance → Menus would have
shown French location names to an English admin, forever**, while the rest of the
admin translated correctly.

`load_theme_textdomain()` is now the first statement in the function. It is safe
to call there: in WP 7.0 it only registers a path (it does not resolve a locale),
and core's own `_doing_it_wrong` guard explicitly permits translating during
`after_setup_theme`. `pluggable.php` is loaded at wp-settings.php:604, before
`after_setup_theme` (749), so `get_user_locale()` can resolve the logged-in user
even though `$GLOBALS['wp']->init()` does not run until line 758.

### New: `.gitattributes`

This repo is developed on Windows with `core.autocrlf=true` and had no
`.gitattributes`. `en_US.mo` contains **23 CR bytes**; it survives today only
because git's NUL-byte heuristic classifies it as binary. That is a heuristic,
not a guarantee — a future `* text=auto` would corrupt the catalogue, and
WordPress would silently fall back to the French msgid with no error anywhere.

`*.mo` (and images/fonts) are now explicitly `binary`; `*.po`/`*.pot` are `text
eol=lf`.

### Edited: `style.css`

Added `Domain Path: /languages`. (It defaults to `/languages`, but being explicit
documents where the catalogue lives.)

### Edited: `.github/workflows/deploy1.yml`

`languages/*.po`, `languages/*.pot` and `.gitattributes` added to all three
exclude lists (17 → 20 patterns each; the three lists must stay identical — they
are separate retry steps). `en_US.mo` is deliberately **not** excluded.

## Six strings are deliberately NOT translated

`v5_digital_setup_theme_content()` seeds the primary nav menu, and it also runs from
the `admin_init` self-healing migrations. Its menu **titles** go through `__()` and are
written into the database:

```php
array('title' => __('accueil', 'agence-marketing-digital'), 'slug' => 'accueil'),
array('title' => __('annuaire',  ...), 'slug' => 'annuaire'),
array('title' => __('blog',      ...), 'slug' => 'blog'),
array('title' => __('à propos',  ...), 'slug' => 'about'),
array('title' => __('méthodologie', ...), 'slug' => 'methodologie'),
array('title' => __('contact',   ...), 'slug' => 'contact'),
```

Because `determine_locale()` returns the **editing user's** locale in wp-admin, an
English-profile admin who triggered that seeding would persist **English menu titles**
into content that every visitor sees. So these six msgids are excluded from `en_US.mo`
and pass through as French.

(The `slug` values are separate hardcoded literals, so URLs were never at risk.)

The generator asserts this: `do_not_translate.json` is derived by scanning for `__()`
calls inside functions that call `wp_insert_post`/`wp_update_nav_menu_item`/
`update_option`/etc., and the check is re-run against the compiled `.mo`.

## Coverage

```
msgids total      : 105
translated -> en  :  95
skipped           :  10   (6 DB-persisted nav titles, 2 already-English, 2 identity)
```

> **Superseded.** These numbers describe the catalogue as this change left it.
> `2026-07-09-acf-admin-translation.md` grows it to **309 entries / 332 source
> strings** by adding the ACF JSON labels. The six DB-persisted nav titles stay
> excluded.

All 105 live in `functions.php` — CPT/taxonomy labels, menu-location names, the ACF
inactive notice, and the page-builder/preview UI. **No template calls `__()`**, so the
front end is untouched by this change either way.

## Not covered by this — now handled separately

ACF field labels, CPT/taxonomy labels and flexible-content layout labels
(`[Commun] Bandeau — logos partenaires`) live as literals in `acf-json/*.json`
and never pass through `__()`, so this change left them French.

It also turned out that **68 of this catalogue's 95 entries were dead code**: the
CPT labels wrapped in `__()` inside `v5_digital_register_cpts()` never execute,
because that function early-returns whenever `acf-json/post_type_*.json` exists.

Both are fixed in `2026-07-09-acf-admin-translation.md`, which enables ACF's own
`l10n_textdomain` support and grows the catalogue to 309 entries.

## Related fix

Because ACF labels *could* one day be translated, the picker no longer derives a
section's category by matching French words in its label. See
`2026-07-09-page-builder-section-groups.md` → "Translation-proof categories".

## Regenerating after adding or changing a string

The `.pot`/`.po`/`.mo` were generated with a script (no PHP/Node on the dev machine, so
`wp i18n make-pot` was unavailable). With WP-CLI available the normal flow is:

```bash
wp i18n make-pot . languages/agence-marketing-digital.pot
# translate languages/en_US.po in Poedit / Loco Translate
wp i18n make-mo languages/
```

Whatever the tool: keep the six nav titles untranslated, and never rename
`en_US.mo`.

## Verification performed

- The compiled `en_US.mo` was parsed with Python's own `gettext.GNUTranslations`
  (independent of the library that wrote it): **95 catalogue entries** at the time
  of this change, loads clean. (Now 309 — see the follow-up doc.)
- Spot-checked lookups: `Sections de cette page → Sections on this page`,
  `Utilisée → Used`, `Logos Partenaires → Partner Logos`, `Rafraîchir → Refresh`.
- The `%d` placeholder survives: `Déjà utilisée %d fois sur cette page →
  Already used %d times on this page`.
- **All six DB-persisted nav titles return unchanged** through the catalogue.
- `deploy1.yml` still parses as valid YAML; all three exclude lists identical at 19
  patterns; `en_US.mo` is not excluded.
- WP 7.0 core read directly to confirm `{locale}.mo` naming and `determine_locale()`
  usage, rather than trusting secondary sources (which disagreed on whether `en_US.mo`
  loads at all).
- Confirmed **zero** `__()` calls execute before `init` (checked every one of the 109
  occurrences at brace-depth 0), so the WP 6.7 `_load_textdomain_just_in_time`
  "triggered too early" notice does not apply, despite this site running WP 7.0.

**Not verified here** (no PHP runtime): the strings rendering in a real wp-admin with a
user profile set to English. After deploying, set Site Language to Français, then set
one user's profile Language to English and confirm the page-builder picker reads
"Sections on this page".

## How to revert

Delete `languages/`, remove `Domain Path` from `style.css`, and drop the two
`languages/*.po*` lines from the three exclude lists. Everything falls back to the
French msgid — i.e. exactly today's behaviour.
