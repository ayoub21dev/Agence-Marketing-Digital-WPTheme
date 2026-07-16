# Final audit: re-review the whole session's diff, fix what it found (2026-07-09)

A full re-audit of every file touched this session (18 modified files + the
new `theme.json`), run fresh against the actual current diff rather than
relying on earlier descriptions — four parallel reviews: security, JS
correctness/regressions, accessibility/i18n, performance/theme.json
integration. Two came back completely clean; two found small, real gaps,
fixed below.

## Clean passes (no changes needed)

- **Security** — the SVG sanitizer allowlist, sitemap transient caching, the
  `hero.php` color regex, and the `wp_json_encode()` output in `header.php`
  were all re-verified against the actual code and found sound. One
  theoretical, non-exploitable note (U+2028/2029 inside `JSON_UNESCAPED_UNICODE`
  output could theoretically break a `<script>` block, but every value
  passed through is a `v5_t()`-translated, admin-authored string — not
  reachable by a visitor).
- **JS correctness** — the modal GSAP-retargeting, the double-open guards,
  the custom-select `activeIndex` sync (both mouse and keyboard open paths
  call `highlightOption()`), the `section-preview.js` debounce (guarded by
  `buildModal()`'s own singleton check), and the `mmStr()`/`updateWizardHeader()`
  call sites were all re-verified — no regressions found.

## Fixed

**1. Five new `aria-label` strings weren't wrapped in `v5_t()`** — inconsistent
with the rest of the same accessibility pass, which correctly wrapped
sibling strings added at the same time. Wrapped and registered in
`v5_digital_ui_strings()`:
- `guides.php:112/128/146` — the three static fallback cards' `aria-label`s
  (`Top Agences de Marketing Digital au Maroc`, `Meilleures Agences SEO à
  Casablanca`, `Comparatif des Agences Social Media`). Left the matching
  visible `<h3>` headings in the same cards untouched (out of scope — the
  audit flagged the `aria-label` specifically, not the heading text).
- `footer.php:297` (exit-intent email field) and
  `template-parts/layouts/newsletter_cta.php:58` (footer newsletter email
  field) — both used `aria-label="Adresse email"`; wrapped both, sharing one
  registry entry.

**2. Sitemap cache invalidation didn't skip autosaves/revisions** — both
fire `save_post` too, so every autosave tick while an author is actively
editing a draft was busting the cache, undermining much of the point of
caching it. `v5_digital_invalidate_sitemap_cache()` (`functions.php`) now
checks `wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)` first
and returns early for either. Verified both functions accept a plain
integer post ID directly (not requiring a `WP_Post` object) by reading their
real definitions in this install's `wp-includes/revision.php`.

**3. The blog listing's "N articles" count showed the site-wide total, not
what's visible on the current page** — `$post_count` was
`$blog_query->found_posts` (the total across every page, now that pagination
exists), but `blgFilterTopic()`'s own JS overwrites that same element with a
page-local count the moment a visitor clicks a category pill. The number
shown silently changed *meaning* between page load and the first filter
interaction. Changed to `count($blog_query->posts)` — the actual count of
cards on the current page — so the initial render already matches what
filtering produces, no more mismatch. (The underlying limitation this
doesn't change — category filtering only searches the current page's dozen
posts, not the full site-wide set — is a disclosed, deliberate scope
boundary from the pagination fix itself, not something this count change
addresses or was meant to.)

**4. `theme.json`'s `$schema` referenced a version-specific URL
(`.../wp/7.0/theme.json`) that likely isn't a real published schema** —
changed to `https://schemas.wp.org/trunk/theme.json`, the standard
always-current reference. Purely cosmetic (IDE autocomplete only; WP core
never fetches `$schema` at runtime), but a small correctness fix.

## Verification performed

Real PHP syntax lint (`php -l`) on every file touched in this fix round —
`guides.php`, `footer.php`, `newsletter_cta.php`, `functions.php`,
`blog_posts_grid.php` — all pass. `theme.json` re-validated as well-formed
JSON after the schema URL edit. Cross-checked all four newly-wrapped strings
directly against `v5_digital_ui_strings()` — each has exactly one registry
entry, exactly matching every call site character-for-character (confirmed
by direct `grep`, not the aggregate count which turned out to mis-handle
accented characters in a bash loop — caught and re-verified directly rather
than trusted at face value).

## Files touched

`functions.php`, `footer.php`, `theme.json`,
`template-parts/layouts/guides.php`,
`template-parts/layouts/newsletter_cta.php`,
`template-parts/layouts/blog_posts_grid.php`.
