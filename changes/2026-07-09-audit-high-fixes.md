# Audit fixes: the remaining High severity findings (2026-07-09)

Follow-up to `2026-07-09-audit-medium-low-fixes.md`. Fixes findings #2–#7 from
the full-theme audit's High severity list, in the order given. #1 (the
`icon_svg` XSS in `specialties.php`/`challenge.php`) was deliberately excluded
from this batch and remains open.

## #2 — Search palette / matchmaker double-open (`theme-scripts.js`)

`openSearchPalette()` and `openMatchmaker()` called `modal.showModal()`
unconditionally. Holding Ctrl/Cmd+K (keyboard auto-repeat) re-invoked
`openSearchPalette()` while already open: it wiped the search input, re-ran
an empty search, then `showModal()` threw `InvalidStateError` on an
already-open `<dialog>`. Same gap in `openMatchmaker()`. Fixed with the same
`!modal.open` guard `openExitIntent()` already used.

## #4 — Matchmaker close button missing `aria-label` (`footer.php`)

Added `aria-label="<?php echo esc_attr(v5_t('Fermer')); ?>"` — reuses the
`Fermer` string already registered (shared with the exit-intent close
button), no new registry entry needed.

## #3 — Custom `<select>` fully keyboard-inoperable (`theme-scripts.js`)

The biggest fix in this batch. `initCustomSelects()` hid the real `<select>`
and rebuilt it as a trigger `<button>` + a menu of plain `<div>`s with only
`onclick` — a keyboard user could Tab to and open the trigger (it's a real
button), but had no way to choose an option once the menu was open: no
`role`, no way to move a "current" option, Tab just left the menu.

Implemented the standard ARIA "combobox with active-descendant" pattern —
the trigger button keeps focus the entire time, never the options:

- Trigger: `role="combobox"`, `aria-haspopup="listbox"`, `aria-expanded`
  (kept in sync in `openCustomSelectMenu`/`closeCustomSelectMenu`),
  `aria-controls` → the menu's id, `aria-activedescendant` → the currently
  highlighted option's id.
- Menu: `role="listbox"`, a stable id derived from the underlying `<select>`'s
  own id (`home-filter-service` → `home-filter-service-listbox`; falls back
  to a counter for the rare select with no id).
- Each option: `role="option"`, a stable id, `aria-selected`.
- New keydown handler on the trigger: `ArrowDown`/`ArrowUp` open the menu (if
  closed) or move the highlighted option (if open, via `highlightOption()`);
  `Home`/`End` jump to the first/last option; `Enter`/`Space` commit the
  highlighted option via `commitOption()` **only when the menu is open**
  (when closed, `preventDefault()` is intentionally skipped so the native
  button click — which already opens the menu — still fires, rather than
  double-handling it); `Escape` closes without committing and returns focus
  to the trigger.
- `commitOption()`/mouse-click option selection now share the same code path
  (previously the click handler duplicated the "set value + dispatch change +
  close" logic inline).
- Added `.custom-select-option.is-active` in `theme-styles.css` — the
  keyboard-highlighted-but-not-yet-committed option, same visual treatment as
  the existing mouse `:hover` state.

This affects all four `<select>`s the theme replaces: the home search
filter's Service/Ville/Note minimale, and the contact form's Sujet field —
all four were equally broken, all four now work.

## #6 — Sitemap has zero caching (`functions.php`)

The `/sitemap.xml` handler ran two unbounded `get_posts()` calls (pages,
posts) on every single request — including repeat crawler hits, which don't
carry a session/cookie to trigger normal page caching. Wrapped the generated
XML in a transient (`v5_digital_sitemap_xml`, `DAY_IN_SECONDS` safety-net
TTL), built the output into a `$xml` string instead of echoing piece by
piece so it can be cached before being sent. Invalidated immediately (not
just on the daily TTL) via `save_post`/`deleted_post`/`trashed_post`/
`untrashed_post`, so a new or edited page/post appears in the sitemap right
away rather than waiting out the day — matches the existing
get_transient/set_transient pattern already used for the changelog
(`v5_digital_changelog_path()` et al.). The invalidation hooks fire for
every post type, not just pages/posts (the two that actually appear in the
sitemap) — broader than strictly necessary, but harmless: worst case is an
extra cache rebuild on the next sitemap request, never worse than the
original always-uncached behavior.

## #5 — Blog listing has no pagination (`template-parts/layouts/blog_posts_grid.php`)

`posts_per_page` defaulted to `-1` (every published post, unbounded) when the
ACF field was empty, rendered into one grid with client-side category
filtering (`blgFilterTopic()` shows/hides `.blg-post-item` cards already in
the DOM — no server round-trip).

- Changed the **empty-field fallback** from `-1` to `12`. An editor can still
  type `-1` explicitly into the field to opt into "show everything on one
  page" for a genuinely small blog — that remains a respected, intentional
  choice; only the silent default changed.
- Added a real `paged` query var (checks both `paged` and `page` — the
  standard WordPress pattern, since which one a static Page populates
  depends on whether it's also the site's front page) to the query, and
  rendered pagination controls via core's `paginate_links()` (`type =>
  'array'`, styled to match the theme's mono/pill aesthetic already used by
  the topic-filter buttons, scoped under `.blg-pagination` so it doesn't leak
  the generic `.page-numbers` class other plugins may also use).
- Left `no_found_rows` unset (default `false`) here specifically — unlike
  `picks.php`/`guides.php` fixed in the previous pass, this query genuinely
  needs `$blog_query->found_posts`/`->max_num_pages` now that it's paginated.

**Known, disclosed limitation:** the category-filter pills still only
filter within the *currently loaded page* of results — clicking "Tout voir"
vs. a specific topic on page 2 shows/hides only that page's dozen posts, not
every matching post site-wide. Making the topic filter re-query the server
(so it's correct across the full result set at any post count) would mean
converting the pills from instant client-side toggling into real
navigation/reload — a bigger UX change than "add pagination" asked for, so
it wasn't done here. Flagging it as a deliberate scope boundary, not an
oversight.

## #7 — Exit-intent popup and matchmaker modal strings not wrapped (`footer.php`, `functions.php`)

Wrapped every visible string in both modals in `v5_t()` and registered them
in `v5_digital_ui_strings()`:

- **Exit-intent popup:** `Newsletter`, `Avant de partir…`, the description,
  the body paragraph, `S'abonner`, the "Aucun spam" footer note, and both
  success-state strings. (`Fermer` on the close button was already wrapped
  from when the popup was first built.)
- **Matchmaker modal:** `Matchmaker`, `Trouvez votre agence idéale`,
  `2 questions · 30 secondes`, both step questions, `Étape suivante`,
  `Trouver mon agence`, and both success-state strings.

**Deliberately NOT wrapped:** the budget range option labels ("5 000 MAD –
15 000 MAD" etc.) and the fallback service labels (`$matchmaker_services` in
`footer.php`, used only when the `agency_service` taxonomy has zero terms) —
these are catalog/content data, not theme UI chrome, consistent with the
project's existing convention of only wrapping the theme's own hardcoded
interface text (the same reasoning that keeps ACF field values and taxonomy
term names out of `v5_t()` elsewhere).

**Found but not fixed, flagging for visibility:** `theme-scripts.js`'s
`updateWizardHeader()` is called from JS with three more hardcoded strings —
`"Trouvez votre agence idéale"`/`"4 questions · 60 secondes"` (on reset),
`"Quel est votre budget ?"`/`"Étape 2 sur 2"` (step 2), and `"Mise en
relation réussie !"`/`"Terminé"` (success) — that overwrite the PHP-rendered,
now-translatable header text at runtime. `v5_t()` is PHP-only; the theme has
no existing JS-side translation bridge (the only precedent,
`window.wpThemeSettings`, carries `homeUrl`/`exitIntentEnabled`, not UI
strings). Building one was out of scope for "wrap these strings" — noting it
as a real gap for whoever picks this up next. Separately, and unrelated to
i18n: the reset-state subtitle JS sets (`"4 questions · 60 secondes"`)
doesn't match the initial PHP-rendered subtitle (`"2 questions · 30
secondes"`) — a pre-existing inconsistency, not introduced or fixed here.

## Verification performed

Structural balance check (the same PHP-mode/string/heredoc-aware checker
from the previous pass — no PHP runtime on this machine) on every edited PHP
file: `footer.php`, `functions.php`, `template-parts/layouts/blog_posts_grid.php`
— all pass.

The JS was executed in headless Chrome:

| Check | Result |
| --- | --- |
| First Ctrl+K opens the search palette | pass |
| Repeated Ctrl+K (simulating auto-repeat) does not throw | pass |
| Repeated Ctrl+K does not wipe the typed search input | pass |
| Repeated Ctrl+K leaves the palette open, doesn't toggle it shut | pass |
| Calling `openMatchmaker()` twice does not throw | pass |
| Custom select: ARIA wiring present at init (`combobox`/`listbox`/`aria-expanded`/`aria-controls`) | pass |
| ArrowDown on a closed trigger opens the menu and highlights the current option | pass |
| ArrowDown navigates the highlight without changing the real `<select>`'s value | pass |
| Enter commits the highlighted option to the real `<select>` and closes the menu | pass |
| Escape closes without changing the committed value | pass |
| Mouse click still works and keeps `aria-selected` in sync across options | pass |
| No uncaught JS errors, either test | none |

Not verifiable here (no PHP runtime, no live WP install): the sitemap
transient actually caching across requests, the pagination query/URLs
against a real WordPress permalink structure, and Polylang's string screen
listing the newly-registered strings.

## Files touched

`assets/js/theme-scripts.js`, `assets/css/theme-styles.css`, `footer.php`,
`functions.php`, `template-parts/layouts/blog_posts_grid.php`.
