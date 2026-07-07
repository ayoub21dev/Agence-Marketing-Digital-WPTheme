# Article page redesign — image hero card (2026-07-07)

## What changed

**One file only: `single-blog.php`** (the template used by all blog posts).
The exact diff is saved next to this file: [`2026-07-07-article-hero.patch`](2026-07-07-article-hero.patch).

### 1. Hero with the featured image (the main change)
- The cover image now renders as a **hero card** at the top of the article:
  rounded-2xl, theme border + shadow, 420px tall (520px on desktop), in the
  same `max-w-4xl` column as the article text.
- Image source priority (same chain as the blog grid and 404 page):
  ACF `cover_image_media` → ACF `cover_image_url` → native featured image.
- A slate-950 gradient scrim sits over the image; the category badge,
  the title (white, Space Grotesk) and the author/date/read-time line are
  pinned to the bottom-left of the card. Badge and back-button become
  frosted-glass chips on top of the image.
- Posts **without** a cover image get a plain heading block instead
  (badge, title, meta, bottom border) — close to the original design.

### 2. Bigger page & typography
- Containers widened `max-w-3xl` → `max-w-4xl` (hero + content).
- Title: `1.75/2.25rem` → `2rem/2.75rem`.
- Body text (`.article-prose p`): 14.5px → 16px, line-height 1.7 → 1.8.
- `h2`: 18px → 22px, `h3`: 16px → 18px, spacing scaled up accordingly.

### 3. CSS shim in the template's `<style>` block
Node.js is not installed on this machine, so `npm run build` could not be
run and the committed `assets/css/tailwind.css` is missing a few utilities
used by the new markup (`pt-10`, `text-[2rem]`, `leading-[1.15]`,
`md:pt-8`, `md:p-10`, `md:text-[2.75rem]`). Equivalent CSS was added
inline in `single-blog.php`'s `<style>` block, plus the hand-written
`article-hero-*` classes (media, overlay, tall, title, dim, chip).

> After running `npm run build` on a machine with Node, the 6 utility
> shims can be deleted (the `article-hero-*` classes must stay).

## How to revert

The working tree was clean before these edits, so any of these works:

```bash
# Option A — if the change is NOT committed yet:
git checkout -- single-blog.php

# Option B — if it was committed, revert the commit:
git revert <commit-hash>

# Option C — apply the saved patch in reverse (works either way):
git apply -R changes/2026-07-07-article-hero.patch
```

`assets/css/tailwind.css` was **not** modified; no other file was touched.
