# Structured data: Organization + ItemList (2026-07-07)

## What changed (2 files)

### 1. `functions.php` — Organization JSON-LD, site-wide
`v5_digital_organization_schema()` (hooked on `wp_head`, placed just above
the sitemap section) emits on every page:

- `@type: Organization` with `@id: <home>/#organization`
- `name` / `url` / `description` from Settings → General (site title & tagline)
- `email` from `v5_digital_get_dynamic_email()` (contact@<domain>)
- `logo` only if a custom logo is ever set in the Customizer (none today —
  the wordmark is text)

### 2. `single-blog.php` — ItemList JSON-LD on ranked articles
Emitted only when the article has an `agency_reviews_block` (the "Analyses
Éditoriales" ranked list). Mirrors exactly what the article displays:

- `@type: ItemList` named after the article, with `numberOfItems`
- one `ListItem` per reviewed agency: `position` = its RANK badge
  (falls back to list order), `name` = agency title, `url` = agency website
  when present
- items sorted by position, as Google expects
- articles without a ranked list emit nothing

## How to verify

View source of a "Classement" article (e.g. `/blog/top-agencies/`) — two
`<script type="application/ld+json">` blocks should be present. Paste the
URL into Google's Rich Results Test / validator.schema.org once deployed.

## How to revert

No `.patch` — both files carry other uncommitted same-day work. Manual:

1. Remove `v5_digital_organization_schema()` + its `add_action('wp_head', …)`
   from `functions.php`.
2. In `single-blog.php`, remove the schema block between
   `if (!empty($agency_reviews)) :` and the `?>` before
   `<div class="mt-10 pt-8 border-t border-slate-200">`
   (from the `// ItemList JSON-LD` comment through the closing `}` of the
   `if (!empty($schema_items))` block).

Purely additive — no data or display changes either way.
