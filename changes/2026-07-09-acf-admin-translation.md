# Translate the whole ACF admin, dynamically (2026-07-09)

Follows `2026-07-09-admin-language.md`. That change shipped `languages/en_US.mo`
and an English admin correctly saw "SECTIONS ON THIS PAGE" — but the CPT menu
still said **Agences**, and every layout card in the picker was still French.

## Why 68 of the first catalogue's entries were dead code

`v5_digital_register_cpts()` wraps its labels in `__()`, but it **early-returns**
when `acf-json/post_type_*.json` exists — which it does:

```php
function v5_digital_register_cpts() {
    if (v5_digital_acf_json_manages_content_types()) {
        return;                     // <-- always, in practice
    }
```

So the CPT/taxonomy labels actually rendered come from the **JSON**, as French
literals that gettext never sees. Same for field-group titles, field labels,
field instructions, and the flexible-content layout labels. Measured surface:

| Source | Strings |
| --- | --- |
| CPT labels (`acf-json/post_type_*.json`) | 50 |
| Taxonomy labels (`acf-json/taxonomy_*.json`) | 18 |
| ACF field-group titles | 9 |
| ACF field labels + instructions | 205 |
| Flexible-content layout labels | 22 |
| Layout descriptions (`functions.php`, unwrapped) | 21 |
| Code msgids (already handled) | 99 |
| **Total distinct** | **332** |

## ACF already solves this — one setting

`acf_translate()` (`includes/api/api-helpers.php:3139`) ends in
`__( $string, $textdomain )`. It is applied by ACF to:

- field `label` and `instructions` — `acf_translate_field()`, hooked to `acf/validate_field`
- flexible-content `layouts[].label` **and** `button_label` — `class-acf-field-flexible-content.php:1517`
- CPT `title`, `description` and every `labels[...]` — `class-acf-post-type.php:780`
- taxonomy ditto — `class-acf-taxonomy.php:654`
- field-group `title` — `acf_translate_internal_post_type()`

…all gated behind two settings, both read through `acf_get_setting()`, which
applies `acf/settings/{name}` on **every call** — so they are live, not boot-time.

```php
add_filter('acf/settings/l10n_textdomain', 'v5_digital_acf_textdomain');
```

Because `__()` resolves via `determine_locale()` per request, the entire ACF
admin now follows **each editor's own profile language**. That is the "dynamic"
part, and it costs one filter.

## ⚠ The trap: this would have destroyed acf-json/

`acf_update_field()` runs the *submitted* values back through
`acf_validate_field()` → `acf/validate_field` → `acf_translate_field()`.

With translation on, an English-profile admin who opened a field group and hit
Save would write the **English labels straight into `acf-json/`**, wiping the
French source strings — and, since acf-json is the source of truth, wiping them
for everyone. Exactly the hazard that got the six seeded nav-menu titles excluded
from the catalogue in the previous change.

So translation is switched **off** on ACF's structure editors:

```php
add_filter('acf/settings/l10n', 'v5_digital_acf_l10n_enabled');   // -> false there
```

`v5_digital_editing_acf_structure()` returns true while rendering or saving
`acf-field-group`, `acf-post-type`, `acf-taxonomy`, `acf-ui-options-page`
(via `$_GET['post_type']`, `$_POST['post_type']`, `$_GET['post']`/`$_POST['post_ID']`
→ `get_post_type()`, and ACF's `acf/field_group/*` AJAX actions), and on
**Tools → Export/Import** (`?page=acf-tools*`), where a PHP export must emit the
untranslated source strings.

Translation is *also* off whenever `!is_admin()`. ACF labels are never rendered by
a template, so nothing is lost — and it closes the whole class of hazard where an
`acf_update_field()` run from WP-CLI, a migration or the front end would translate
labels before persisting them. (`admin-ajax.php` counts as admin, so the section
preview endpoint is unaffected.)

Consequence, by design: **field groups are always authored and saved in French**,
whatever language the editor's profile is in. That is the correct trade — the
JSON is source code.

### Two traps inside the guard itself

- **`sanitize_key()` must not touch the AJAX action.** It strips `/`, turning
  `acf/field_group/move_field` into `acffield_groupmove_field`, so the prefix test
  silently never matched — and `move_field` is precisely the action that calls
  `acf_update_field()`. Uses `sanitize_text_field()` instead; the value is only
  ever compared, never emitted. Caught by auditing the guard against ACF's real
  registered action names.
- **The guard is memoised.** `acf_translate()` calls it once *per string*, and a
  field group has hundreds. The superglobals cannot change mid-request.

`v5_digital_layout_descriptions()` builds its array with an explicit `foreach`
rather than `array_map()`. To be clear: `array_map()` with a single array **does**
preserve string keys — executed and confirmed in PHP 8.4 — so this is defensive,
not a bug fix. The layout-name keys are load-bearing (looked up by
`v5_digital_layout_preview_data()`) and the loop says so plainly.

## Files

### Edited: `functions.php`

- New section **5c. ACF ADMIN LANGUAGE**: `v5_digital_acf_textdomain()`,
  `v5_digital_acf_l10n_enabled()`, `v5_digital_editing_acf_structure()`.
- `v5_digital_translate_admin_string()` — a named `__()` wrapper usable as an
  `array_map` callback.
- `v5_digital_layout_descriptions()` split: the French literals moved to
  `v5_digital_layout_descriptions_source()`, and the public function now maps
  them through `__()` at call time, so they follow the admin's language too.

### Edited: `languages/`

Catalogue grew **95 → 309** entries. `.pot` now lists all 332 source strings with
their origin (`code`, `cpt`, `tax`, `group`, `field`, `layout`, `desc`).

23 strings are deliberately left untranslated because they are **already English
or identical in both languages** (`Site Settings`, `Contact email`, `Service`,
`Description`, `Facebook URL`, …). gettext falls back to the msgid.

## Prerequisite this depended on

Translating the layout labels turns `[Accueil] Bannière` into `[Home] Banner`.
Before `2026-07-09-page-builder-section-groups.md` made the picker derive its
categories from **stable layout names**, that would have silently collapsed the
whole section picker — 15 of 21 cards falling into "other". Verified: with
English labels the grouping is identical; with the old label-parsing code it
breaks. That fix was a hard prerequisite for this one.

The chip **text** follows the translated label (`SHARED` instead of `COMMUN`);
the chip **colour** and the grouping follow the stable slug.

## Gotcha: the local Studio theme is a COPY, not a symlink

`~/Studio/agence-marketing-digital-wptheme/wp-content/themes/Agence-Marketing-Digital-WPTheme/`
is a **separate copy** of this repo (different inodes, different file hashes), not
a link to it. When this change was first probed, WordPress was still running a
`functions.php` from 50 minutes earlier: `v5_digital_editing_acf_structure()`
did not exist, `acf_get_setting('l10n_textdomain')` was empty, and every ACF label
was French — while the repo copy was correct all along.

**Editing the repo does not necessarily update the running site.** Before testing
anything locally, confirm the two are in sync:

```bash
diff -rq --exclude=.git --exclude=node_modules \
  <repo> ~/Studio/agence-marketing-digital-wptheme/wp-content/themes/Agence-Marketing-Digital-WPTheme
```

Note the direction of danger: ACF's `save_json` writes to
`get_stylesheet_directory()/acf-json`, i.e. the **live copy**. Edit a field group
in wp-admin and the JSON lands there, not in the repo. Copy repo→live blindly and
you would destroy it. (At the time of writing the two `acf-json/` trees were
identical, and the live copy had no unique files.)

## The standing cost: msgid drift

The msgids are French *and* the labels are editable in the ACF UI. Rename a field
label and its msgid changes — the translation is silently lost and the label
falls back to the new French. There is no error.

After editing any ACF label, the catalogue must be regenerated. `wp i18n make-pot`
will **not** find these strings (they live in JSON, and the layout descriptions go
through a dynamic `__($var)`). The `.pot` in `languages/` is the record of what
exists; regeneration reads `acf-json/*.json` plus
`v5_digital_layout_descriptions_source()`.

## Verification performed

No PHP runtime here. The three new functions pass a string/comment-aware brace
balance check, and every filter/guard string was asserted present.

`en_US.mo` was parsed with Python's own `gettext.GNUTranslations` — independent of
the library that wrote it:

| Check | Result |
| --- | --- |
| 309 catalogue entries | pass |
| `Agences → Agencies` (CPT menu, from acf-json) | pass |
| `Spécialités → Specialties` | pass |
| `[Accueil] Bannière — accroche principale → [Home] Banner — main headline` | pass |
| `Valeur / Nombre → Value / Number` (field label) | pass |
| `Mises en page de la Page → Page Layouts` (group title) | pass |
| `Ajouter une Section → Add a Section` (`button_label`) | pass |
| Layout description (dynamic `__()`) resolves | pass |
| All six DB-persisted nav titles pass through **untranslated** | pass |
| Every msgstr preserves its msgid's printf placeholders | pass |
| No empty msgstr (a blank one renders a blank label) | pass |
| Every layout still has a language-independent category | pass |
| Every translated layout label keeps its `[Category]` prefix (the chip text) | pass |
| No stale msgids (every catalogue entry still exists in code or acf-json) | pass |
| The 23 untranslated leftovers contain no French (they are already English) | pass |

The guard was then tested against **ACF's real registered AJAX action names**,
scraped from the plugin rather than assumed — this is what exposed the
`sanitize_key()` slash bug:

| Action | Guard | Why |
| --- | --- | --- |
| `acf/field_group/move_field` | trips | calls `acf_update_field()` — persists |
| `acf/field_group/render_field_settings` | trips | field-group editor |
| `acf/field_group/render_location_rule` | trips | field-group editor |
| `acf/create_options_page` | trips | structure editor |
| `acf/ajax/fetch` | ignores | value fetch — must stay translated |
| `acf/fields/flexible_content/layout_title` | ignores | renders a row header — must stay translated |

Full picker suite re-run after the change: 196 + 110 + 70 + 13 + 22 + 11 = **422
assertions, 0 unexpected failures**, including the negative control that proves
the label-parsing bug is really detected.

### Executed end-to-end in a real WordPress

A PHP 8.4 binary ships with Studio
(`%LOCALAPPDATA%\studio_app\app-*\resources\php-bin\8.4.21\php.exe`). Every theme
file passes `php -l`. More usefully, WordPress + ACF Pro + this theme were booted
from the CLI (`auto_prepend_file` defines `WP_ADMIN`, so `determine_locale()`
takes the `get_user_locale()` branch; `WPLANG` is empty, so the locale is
`en_US`). The database was snapshotted beforehand and restored afterwards —
WordPress writes transients on boot — and verified byte-identical.

| Probe | normal admin | field-group editor | AJAX `move_field` | AJAX `acf/ajax/fetch` |
| --- | --- | --- | --- | --- |
| `v5_digital_editing_acf_structure()` | false | **true** | **true** | false |
| `acf_get_setting('l10n')` | on | **off** | **off** | on |
| `acf_translate('Agences')` | `Agencies` | `Agences` | `Agences` | `Agencies` |
| `get_post_type_object('agency')->labels->name` | **`Agencies`** | `Agences` | — | — |
| `…->labels->all_items` | **`All Agencies`** | — | — | — |
| field-group title | **`Page Layouts`** | `Mises en page de la Page` | — | — |
| `hero_section` layout label | **`[Home] Banner — main headline`** | `[Accueil] Bannière…` | `[Accueil] Bannière…` | `[Home] Banner…` |
| `button_label` | **`Add a Section`** | `Ajouter une Section` | `Ajouter une Section` | — |
| `v5_digital_layout_preview_data()['hero_section']['cat']` | **`accueil`** | — | — | — |
| `__('accueil')`, `__('à propos')` | unchanged | unchanged | — | — |

`get_post_type_object('agency')->labels->name` is the string WordPress actually
prints in the admin menu, so this is the real thing, not a catalogue lookup. And
`['cat'] === 'accueil'` while the label reads `[Home] Banner` is the picker's
language-independent category proving itself against a genuinely translated label.

**A harness bug this exposed.** The first AJAX probe reported `guard: false` for
`move_field`. Not a code defect: the probe wrote `$_REQUEST['action']` directly,
and `wp_magic_quotes()` (`wp-settings.php:628`) does
`$_REQUEST = array_merge($_GET, $_POST)`, discarding it. A real admin-ajax request
carries `action` in `$_POST`. Corrected, the guard trips.

**Still not verified:** the rendering in a browser (fonts, spacing, chip colours)
and behaviour under a *user profile* set to English rather than an empty
`WPLANG`. After deploying, set Site Language to Français, set one profile to
English, and confirm the CPT menu reads "Agencies" while **Field Groups still
shows French labels** — that second half is the guard doing its job.

## How to revert

Delete section 5c from `functions.php`, re-inline
`v5_digital_layout_descriptions_source()` into `v5_digital_layout_descriptions()`,
and regenerate `en_US.mo` from the code msgids only. ACF falls back to the raw
JSON literals — i.e. everything French, as before.
