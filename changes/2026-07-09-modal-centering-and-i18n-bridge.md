# Fix: search/matchmaker modal centering, JS i18n bridge, subtitle mismatch (2026-07-09)

<!-- changelog: Fixed -->

Closes the three remaining items flagged after the audit fix passes.

## 1. `#search-modal` / `#matchmaker-modal` GSAP-centering bug

Same root cause as the exit-intent popup (`changes/2026-07-08-exit-intent-popup.md`):
`openSearchPalette()`/`openMatchmaker()` called `animateModalOpen(modal)` —
GSAP's tween — directly on the `<dialog>` element, which breaks its native
top-layer centering. Scroll far down a page, open either modal, and it would
render off-screen, offset by roughly the scroll position.

Applied the identical fix: wrapped each dialog's content in an inner
`<div>` (`#search-modal-inner`, `#matchmaker-modal-inner`) in `footer.php`,
and updated `theme-scripts.js` so `animateModalOpen`/`animateModalClose`
target the inner wrapper — never the `<dialog>` itself. `showModal()`/
`.close()` still operate on the real dialog as before.

**Verified against the exact conditions that caused it**: real GSAP (fetched
from the same CDN the site uses), a simulated WP admin bar
(`html { margin-top: 32px }` + a fixed `#wpadminbar`), the theme's real
sticky header, page scrolled 700px down — both modals now measure fully
visible and correctly centered (`search`: top 209.5/bottom 494.5;
`matchmaker`: top 266.6/bottom 437.4, viewport height 704), with **zero**
inline `transform` on either `<dialog>` element, confirming GSAP is animating
the inner wrapper as intended.

## 2. JS-side translation bridge for `updateWizardHeader()`

`theme-scripts.js` sets the matchmaker modal's header text from JS at three
points — wizard reset, advancing to step 2, and the success state — bypassing
`v5_t()` entirely since it's a PHP-only function with no existing JS-side
counterpart.

Added `window.wpThemeSettings.matchmakerStrings` in `header.php`, built via
`wp_json_encode()` over `v5_t()`-wrapped strings (`resetTitle`,
`resetSubtitle`, `step2Title`, `step2Subtitle`, `successTitle`,
`successSubtitle`). Verified the actual JSON `wp_json_encode()` produces
(delegates straight to `json_encode()` for a plain array of clean UTF-8
strings — no exotic fallback path involved) using the real PHP binary this
project now has access to (see below) — valid, round-trips correctly, no
`</script>`-breakout risk (forward slashes are escaped by default since
`JSON_UNESCAPED_SLASHES` isn't passed).

`theme-scripts.js` gained a small `mmStr(key, fallback)` helper that reads
from the bridge and falls back to the existing French literal if it's ever
missing (mirrors `v5_t()`'s own fallback-to-French behavior when Polylang is
inactive). All three `updateWizardHeader()` call sites now go through it. The
four strings that weren't already registered (`Quel est votre budget ?`,
`Étape 2 sur 2`, `Mise en relation réussie !`, `Terminé`) were added to
`v5_digital_ui_strings()`; `resetTitle`/`resetSubtitle` reuse the two strings
already registered for the modal's initial server-rendered state.

## 3. Matchmaker subtitle mismatch

The wizard-reset call set the header to `"4 questions · 60 secondes"` —
stale, and wrong on two counts: the wizard only ever has 2 steps (service,
then budget), and the modal's own PHP-rendered initial state already said
`"2 questions · 30 secondes"`. Fixed the reset call's fallback (and thus, via
the bridge, the translated string actually shown) to `"2 questions · 30
secondes"`, matching both the real step count and the initial markup.

## A note on verification: real PHP is available on this machine

Discovered while fixing the `icon_svg` XSS (previous change) that WP Studio
bundles a working PHP 8.4 CLI binary and the full WordPress core source is on
disk — this project is not actually PHP-runtime-less, despite `php`/`node`
being absent from `PATH`. Used it here to confirm `wp_json_encode()`'s actual
output for the exact `matchmakerStrings` payload rather than assuming it.
Saved to project memory (`real-php-runtime-available.md`) so this isn't
rediscovered from scratch next time.

## Verification performed

Structural balance check (PHP-mode/string/heredoc-aware) on every edited PHP
file — `footer.php`, `header.php`, `functions.php` — all pass. Manual and
automated `<div>` open/close balance check on the restructured modal markup
in `footer.php` (both dialogs land at depth 0 exactly before their closing
`</dialog>`).

Headless Chrome, real GSAP, real conditions (detailed above) — both modals
fully visible and correctly centered, zero inline transform on either
`<dialog>`.

Headless Chrome, the bridge + subtitle fix specifically:

| Check | Result |
| --- | --- |
| Modal opens; reset title read from the bridge | pass |
| Reset subtitle reads "2 questions · 30 secondes" (fixed, not the stale "4 questions · 60 secondes") | pass |
| Dialog element itself carries no inline transform | pass |
| Step 2 title/subtitle read from the bridge | pass |
| Success title/subtitle read from the bridge | pass |
| `mmStr()` falls back to the French literal if the bridge is ever missing | pass |
| Modal still closes correctly | pass |

Re-ran the three earlier regression suites (search/matchmaker double-open
guard, custom-select keyboard accessibility, exit-intent back-link flow) —
all still pass, zero failures, no errors, confirming nothing in this pass
disturbed the fixes from the previous two.

## Files touched

`footer.php`, `header.php`, `functions.php`, `assets/js/theme-scripts.js`.
