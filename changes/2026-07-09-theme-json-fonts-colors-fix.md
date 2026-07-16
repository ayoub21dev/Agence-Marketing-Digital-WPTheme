# Fix: blank white screen on Appearance → Patterns/Fonts (2026-07-09)

Reported: clicking both "Patterns" and "Fonts" under Appearance showed a
blank white screen.

## Root cause

Confirmed directly against this install's actual WordPress core (7.0.1, not
from memory) — this theme is a classic PHP theme with **no `theme.json`**.
`wp-admin/menu.php`:

```php
if ( wp_is_block_theme() ) {
    // "Editor" -> site-editor.php
} else {
    $supports_stylebook = ( current_theme_supports( 'editor-styles' ) || wp_theme_has_theme_json() );
    if ( $supports_stylebook ) {
        // "Design" -> site-editor.php   (the fonts/colors screen)
    } else {
        // "Patterns" -> site-editor.php?p=/pattern
    }
}
```

Neither condition was true (no `theme.json`, and `functions.php` never calls
`add_theme_support('editor-styles')` — confirmed by grep, and no plugin does
either), so WordPress showed "Patterns" instead of "Design", plus the
separate "Fonts" (Font Library) link that's always present regardless of
theme type. Both ultimately load the same Site Editor / Font Library React
apps, which assume the active theme provides Global Styles data via
`theme.json` — without it, that app fails to initialize and the content pane
renders blank. This is a WordPress core limitation for classic themes
lacking `theme.json`, not a bug introduced by any code in this theme or by
anything changed this session (verified: nothing here touches
`add_theme_support`, and no `theme.json` existed before).

## Fix

Added a minimal `theme.json` at the theme root — **`settings` only, no
`styles` key**. This is the standard, widely-used pattern for classic themes
that want the modern Fonts/Colors screen without becoming a block theme:

- **Colors:** the theme's actual brand scale from `tailwind.config.js`
  (`brand-50` through `brand-900`) plus the slate grays used throughout the
  templates. `defaultPalette`/`defaultGradients`/`defaultDuotone` set to
  `false` so the picker shows only this theme's real colors, not WordPress's
  generic defaults.
- **Fonts:** the same three families already loaded via Google Fonts in
  `header.php` — Inter, Space Grotesk, JetBrains Mono.

## Why this can't become a full block theme (verified, not assumed)

Checked `WP_Theme::is_block_theme()` directly: it checks **only** for the
physical existence of `templates/index.html` or `block-templates/index.html`
— nothing else, and does not look at `theme.json` at all. Neither directory
exists in this theme, so `wp_is_block_theme()` stays `false`. The site
continues to render entirely through the classic template hierarchy
(`header.php`, `footer.php`, `front-page.php`, `page.php`,
`single-blog.php`, the ACF flexible-content page builder) exactly as before
— nothing about how pages render changes.

## Why this can't visually change the front end

A theme.json's `settings` block only *registers presets* (they become
available as `--wp--preset--color--{slug}` / `--wp--preset--font-family--{slug}`
CSS custom properties and as options in block editor color/font pickers). It
does **not** apply any style to any element by itself — that only happens
via a `styles` key (deliberately omitted here) or when a block's own markup
explicitly references a preset (e.g. `"style":{"color":{"text":"var:preset|color|brand-600"}}`),
which nothing in this theme's hand-written Tailwind/`theme-styles.css`
front-end does. The only front-end-visible effect is a small block of inert,
unused CSS custom properties added to the page — the same mechanism already
runs on every WordPress site today (core ships a baseline global-styles
output even for classic themes with no theme.json of their own), just with
this theme's own values instead of WordPress's defaults.

**Practical benefit beyond fixing the blank screen:** blog posts are written
in the block editor (confirmed — `single-blog.php` renders via
`the_content()`), so this also gives post authors the theme's actual brand
colors and fonts in the block editor's own color/font pickers, instead of
WordPress's generic default palette.

## Verification performed

- `theme.json` validated as well-formed JSON (Python's `json` module).
- `WP_Theme::is_block_theme()` read directly from this install's core
  (`wp-includes/class-wp-theme.php`) — confirmed it checks only for
  `templates/index.html` / `block-templates/index.html`, neither of which
  this theme has or now has.
- `wp_theme_has_theme_json()` read directly (`wp-includes/global-styles-and-settings.php`)
  — confirmed it only checks for the file's existence, nothing more exotic.
- Confirmed via grep: no `add_theme_support('editor-styles')` anywhere in
  this theme or its two active plugins, and no plugin filters
  `theme_file_path` to redirect the theme.json lookup elsewhere.

**Not verifiable here:** the actual rendered result in a browser — the local
Studio server wasn't running at the time of this fix (confirmed via
`netstat`; none of the locally listening ports belonged to a PHP/WP
process). Start the site and check **Appearance → Design** (it should now
show a Styles screen with this color palette and the three font families
instead of a blank pane), and do a quick visual pass on the front end to
confirm nothing shifted — expected to be a no-op given the reasoning above,
but a real visual check is worth doing once since no browser was available
to confirm directly.

## How to revert

Delete `theme.json`. `wp_theme_has_theme_json()` returns to `false`,
Appearance reverts to showing "Patterns" instead of "Design", and the block
editor's color/font pickers revert to WordPress's generic defaults.

## Files touched

`theme.json` (new file).
