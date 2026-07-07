# Native featured images enabled (2026-07-07)

## Why

The editor had no "Image mise en avant" sidebar panel because the theme never
declared `add_theme_support('post-thumbnails')` — cover images could only be
set through the theme's ACF meta-box fields at the bottom of the editor.

## What changed (6 files)

1. **`functions.php`** — `add_theme_support('post-thumbnails')` added in
   `v5_digital_theme_setup()`. The standard sidebar panel now appears.
2. **Cover-image priority flipped** from
   `ACF media → ACF URL → featured image` to
   `featured image → ACF media → ACF URL` in the 4 templates that render
   covers:
   - `single-blog.php` (article hero)
   - `template-parts/layouts/blog_posts_grid.php`
   - `template-parts/layouts/guides.php`
   - `404.php`
   Existing articles that only have ACF images keep working (fallback).
3. **`acf-json/group_blog_meta.json`** — both cover-image fields' descriptions
   now say they are legacy fallbacks and point editors to the sidebar panel
   (fields kept so old articles' images and external-URL covers still work).

## New editorial workflow

Set article covers via the standard **Image mise en avant** panel. The ACF
fields remain only for old posts and for hotlinking external images.

**No conflict when both are set** — it's a strict priority chain, not a
merge: featured image wins and the ACF fields are simply ignored (they're
never even read). Worst case is cosmetic: an ACF image that appears filled in
the meta box but isn't displayed. Full order:

1. Featured image (Image mise en avant) → used if set, stop.
2. ACF « Image de couverture (Médiathèque) » → fallback.
3. ACF « Image de couverture (URL externe) » → fallback.
4. Nothing set → article renders without a hero image.

## How to revert

Patch saved: [`2026-07-07-featured-image.patch`](2026-07-07-featured-image.patch)
— covers 5 of the 6 files (the 4 templates + the ACF JSON):

```bash
git apply -R changes/2026-07-07-featured-image.patch
```

The 6th file (`functions.php`) is entangled with the same-day sitemap and
permalink work, so its one line is reverted manually:

1. Remove the `add_theme_support('post-thumbnails');` line (+ its comment)
   from `v5_digital_theme_setup()` in `functions.php`.

Data is unaffected either way — featured images and ACF values are stored
independently.
