# Fix: icon_svg stored XSS — sanitize, don't remove the feature (2026-07-09)

<!-- changelog: Security -->

Closes the last open item from the full-theme audit: `specialties.php:48` and
`challenge.php:54` echoed the `icon_svg` ACF textarea field (`specialty_hub`
posts, and the challenge section's card repeater) as raw, unsanitized HTML.
Any editor who can set that field could inject `<script>`/`onload=`/etc. that
runs in every visitor's browser.

Per instruction, the feature itself (editors paste hand-authored SVG icon
markup — Lucide/Feather/Heroicons-style — into a plain textarea) stays
exactly as it was. Only the output is now sanitized.

## The fix

New `v5_digital_sanitize_svg_icon($svg)` in `functions.php`, right after the
ACF safety wrappers. Runs the value through `wp_kses()` with a hand-built
allowlist scoped to the actual vocabulary a simple icon needs — `svg`, `path`,
`circle`, `ellipse`, `rect`, `line`, `polyline`, `polygon`, `g`, `defs`,
`title`, `desc` — plus their presentation attributes (`fill`, `stroke`,
`stroke-width`, `d`, `viewbox`, `class`, ...).

Deliberately **excluded** from the allowlist, because each is a real,
documented way to execute code or load external content from inside an SVG:
`<script>`, `<foreignObject>`, `<use>`/`<image>` (both can fetch external
content via `href`/`xlink:href`), `<a>` (a `javascript:` href), `<style>` and
any inline `style` attribute (CSS-based injection), and the SMIL `<animate*>`
family (`onbegin`/`onend` event attributes, a known SVG-specific vector in
some browsers). Event-handler attributes (`onload`, `onclick`, `onerror`,
...) don't need a special case at all: `wp_kses()` only keeps attributes
explicitly present in the allowlist for a given tag, so anything not
listed — every `on*` attribute, on every tag — is stripped automatically.

Applied at both echo sites:
- `specialties.php:48` — `echo $icon;` → `echo v5_digital_sanitize_svg_icon($icon);`
- `challenge.php:54` — `echo $card['icon_svg'];` → `echo v5_digital_sanitize_svg_icon($card['icon_svg']);`

Confirmed via a full-codebase grep that these are the only two places
`icon_svg` is ever echoed. The other `icon_svg` references in `functions.php`
are ACF field-definition config, and one `update_field()` call that seeds a
hardcoded, developer-authored default SVG during theme activation (not
user-controllable, not an output site) — none needed changing.

## Verification performed — against real WordPress core, not a simulation

Earlier sessions on this project repeatedly hit "no PHP runtime on this
machine" and fell back to a hand-written Python structural checker or a
Python `re` stand-in for PCRE. This time: **WP Studio bundles a real PHP 8.4
CLI binary** (`C:\Users\Anouar BENYEKHLEF\.studio\php-bin\8.4.21\php.exe`) and
the full WordPress core source is on disk. Built an isolated test harness —
copied the real `kses.php`, `plugin.php`, `l10n.php`, `formatting.php`, the
`html-api/` subsystem, and their transitive dependencies (`wp_allowed_protocols()`,
`WP_Token_Map`, `WP_HTML_Tag_Processor`, ...) into a scratch directory and
required them directly, with no DB connection and no plugins loaded — so
nothing here touched the live site. Ran the actual `v5_digital_sanitize_svg_icon()`
body (copied verbatim) against the real `wp_kses()`:

| Test | Result |
| --- | --- |
| A benign Lucide-style icon passes through with all its attributes intact | pass |
| `<script>` tag is stripped (its text content survives as inert, unexecuted plain text) | pass |
| `onerror`/`onclick` stripped from an otherwise-allowed `<path>` | pass |
| `<use href=.../>` (external SVG sprite loading) stripped entirely | pass |
| `<foreignObject>` with an embedded `<script>` stripped | pass |
| `<a href="javascript:...">` stripped | pass |
| `<style>` tag stripped | pass |
| Inline `style="..."` attribute stripped from an allowed tag | pass |
| SMIL `<animate onbegin="...">` stripped | pass |
| Empty/non-string input returns an empty string | pass |

All 10 pass. Saved this PHP-runtime discovery to project memory
(`real-php-runtime-available.md`) — it removes a limitation that shaped how
several earlier fixes in this project were verified.

## How to revert

1. In `functions.php`, delete `v5_digital_sanitize_svg_icon()`.
2. In `specialties.php`, change `echo v5_digital_sanitize_svg_icon($icon);`
   back to `echo $icon;`.
3. In `challenge.php`, change
   `echo v5_digital_sanitize_svg_icon($card['icon_svg']);` back to
   `echo $card['icon_svg'];`.

## Files touched

`functions.php`, `template-parts/layouts/specialties.php`,
`template-parts/layouts/challenge.php`.
