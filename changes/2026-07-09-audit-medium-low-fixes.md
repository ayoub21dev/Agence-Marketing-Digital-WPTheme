# Audit fixes: all Medium and Low severity findings (2026-07-09)

<!-- changelog: Fixed -->

Follows the full-theme audit (six parallel reviews: security, template
XSS/escaping, JS, performance/queries, SEO config consistency,
accessibility/i18n). This covers every Medium and Low finding from that
audit, in that order, per instruction. High severity findings (the known
`icon_svg` XSS in `specialties.php`/`challenge.php`, the search-palette
double-open bug, the keyboard-inoperable custom `<select>`, the matchmaker
modal's missing close-button `aria-label`) were intentionally left for a
later pass.

## Security / correctness

- **`assets/js/theme-scripts.js` — search-palette results via `innerHTML`.**
  `runPaletteSearch()` built each result `<li>` from a template literal
  assigned to `innerHTML`. `SEARCH_INDEX` is static today, but its own
  comment says it's meant to hold real WordPress data — the moment it does,
  an agency/post title containing `<`/`"` becomes stored XSS. Rebuilt via
  `createElement`/`textContent` instead; no behavior change, same visual
  output, no longer parses its content as markup.
- **`assets/js/theme-scripts.js` — `shortcutSearch()` missing
  `encodeURIComponent` on `type`.** Only `value` was encoded; `type` is
  currently always a literal at every call site (no live exploit today), but
  fixed for defense-in-depth since it's a one-line change.

## Performance

- **`template-parts/layouts/picks.php` / `guides.php` — missing
  `no_found_rows`.** Both queries have a real `posts_per_page` limit but
  never read `->found_posts`/`->max_num_pages` (confirmed via grep before
  changing), so `no_found_rows => true` removes an unnecessary
  `SQL_CALC_FOUND_ROWS` count query on every load. `picks.php`'s default
  branch also sorts by `meta_value_num` on `agency_rank` — an unindexed
  filesort inherent to `wp_postmeta`'s EAV design; fine at today's agency
  count, documented in a comment rather than "fixed" with unrequested
  caching infrastructure that isn't needed yet.
- **Inconsistent `loading="lazy"`.** Added to `guides.php` (4 images: the
  dynamic card + 3 static fallback cards), `outcomes.php` (2 testimonial
  avatars), `challenge.php` (1 quote-author photo), and `logos_band.php` (the
  marquee's logo `<img>`, which can render 24+ instances via its duplicated
  loop). All confirmed below-the-fold before changing. `404.php`'s card
  image already had `loading="lazy"`.
- **Unthrottled `resize` handlers forcing layout reads.** The logo marquee's
  `measure()` (reads `track.scrollWidth`) and the admin section-preview's
  `fitFrame()` (reads `clip.clientWidth`) both ran on every `resize` event.
  Both now debounce via a 150ms `setTimeout`, cleared on each new event.
- **Unconditional asset enqueue — assessed, not changed.** The theme
  compiles one `tailwind.css`, one `theme-styles.css`, one `theme-scripts.js`
  with no server build step (a documented hard constraint). Splitting these
  per-layout-block would mean a different build pipeline, not a quick
  conditional-`wp_enqueue_script` tweak — too large a change for a Low
  finding with modest real-world impact. Left as-is.

## Accessibility

- **Search filter + contact-form `<select>` replacement** — not touched
  (this specific bug — the custom dropdown being fully keyboard-inoperable —
  is the High-severity finding, left for the later pass per instruction).
- **Keyboard-inoperable "card" pattern** (`<div>`/`<article onclick=...>`
  with no keyboard equivalent) — found in **five** places via
  `grep -r 'onclick="window\.location\.href='`, not just the one the audit
  named:
  - `404.php` (recent-article cards)
  - `template-parts/layouts/blog_posts_grid.php` (the `<article
    class="blg-post-item">` cards — kept the class/data-attributes the
    filter JS depends on untouched)
  - `template-parts/layouts/guides.php` (the dynamic card **and** all 3
    static fallback cards)

  Added a shared `handleCardKeydown(event)` in `theme-scripts.js` that
  forwards Enter/Space to the card's own `click` (so the existing
  `onclick`-based navigation logic isn't duplicated), plus `tabindex="0"`,
  `role="link"`, and an `aria-label` (the post title, or the static card's
  own heading text for the fallback cards) on each card.
- **Logo wordmark keyboard-inoperable** (`header.php`, `footer.php`) — both
  were a `<div onclick=...>`. Converted to a real `<a href="...">` — the
  correct semantic element for a home-link, not a tabindex/keydown
  workaround. No nested interactive elements inside either, confirmed before
  changing. Tailwind's preflight (`a{color:inherit;text-decoration:inherit}`)
  means this doesn't introduce unwanted link styling.
- **Placeholder-only inputs with no accessible name** — added `aria-label` to
  the search-modal input (`footer.php`), the exit-intent email field
  (`footer.php`), and the footer newsletter email field
  (`newsletter_cta.php`). Also gave `search_filter.php`'s three `<label>`s a
  matching `for=` (they had visible label text but weren't programmatically
  associated with their `<select>`) — same underlying "no accessible name"
  issue, one line away from the string I was already touching there.
- **`dialog:focus-visible { outline: none; }` with no replacement** —
  added a `box-shadow` focus ring matching the existing
  `.search-focus:focus` treatment used elsewhere in the theme, so keyboard
  focus on a dialog root is visible again.
- **`hero.php` — `esc_attr()` used in a CSS-property context.** Correct for
  blocking attribute breakout, wrong primitive for CSS injection (doesn't
  validate the value is actually a color). Added a regex allowlist
  (hex or `rgb()`/`rgba()` only) before the `bg_color`/`text_color` ACF
  values are used in the inline `style` attribute — anything else is
  silently dropped rather than echoed. Verified the pattern against 10 cases
  (valid hex/rgb/rgba pass; `expression()`, `url()`, and attribute/CSS
  breakout attempts are all rejected) via Python's `re` (PCRE-equivalent for
  this pattern — no PHP runtime on this machine).
- **`AMD_CF_Form::render()` escaping — investigated, not a live issue.** The
  audit flagged this as an unverifiable plugin-boundary trust question.
  Traced it further: `AMD_CF_Form`/`AMD_CF_Forms` are **not defined anywhere**
  in this WordPress install (not in the theme, not in either installed
  plugin — `advanced-custom-fields-pro` or `amd-chatbot`). `form.php` already
  gates the call behind `class_exists('AMD_CF_Form')` and falls back to
  `do_shortcode()` otherwise, so this code path is currently dead — nothing
  to fix or verify without the plugin's source, and no defensive change was
  made on the theme side that risks breaking a plugin integration that isn't
  actually installed.
- **Agency logo `alt` text hardcoded English word order** (`single-blog.php`)
  — `"{$agency_post->post_title} Logo"` → wrapped and reordered to the
  natural French phrasing `sprintf(v5_t('Logo de %s'), ...)`.

## i18n (hardcoded strings not wrapped in `v5_t()`)

Wrapped and registered in `v5_digital_ui_strings()` (`functions.php`) so
each appears in Polylang's string-translation screen:

- Footer column titles: `Découvrir`, `Ressources`, `Villes`, `Légal`.
- Search-modal footer hint: `Appuyez sur ÉCHAP pour fermer`, `recherche`.
- `search_filter.php`: `Service`, `Tous les services`, `Ville`,
  `Toutes les villes`, `Note minimale`, `Toutes les notes`, `4.5+ Étoiles`,
  `4.0+ Étoiles`, `Rechercher`.
- `404.php`: `Cette page s'est égarée.`, the description paragraph beneath
  it (same hero block, not separately named by the audit but identical in
  kind), `Retour à l'accueil`, `Blog`, `Méthodologie`, `Contact`,
  `Continuer la lecture` (`Articles récents`/`Tout le blog` were **already**
  registered — reused as-is, not duplicated).
- `single-blog.php`: `retour aux articles`, `Analyses Éditoriales`,
  `Visiter le Site` (a UI-chrome fallback default, not editor content — same
  category as the `Par` string already wrapped nearby), and the new
  `Logo de %s` format string above.

Deliberately did **not** change any visible capitalization to reuse an
already-registered string (e.g. `404.php`'s "Blog"/"Méthodologie"/"Contact"
stayed capitalized, as three new registry entries, rather than silently
lower-casing them to match the header nav's existing `blog`/`méthodologie`/
`contact` fallback labels — that would have been an unrequested visible
text change).

## Verification performed

No PHP runtime on this machine — rebuilt a structural balance checker
(PHP-mode/string/heredoc-aware delimiter matching; the one from the
previous session's scratchpad was gone) and ran it against all 14 edited
PHP files: `functions.php`, `header.php`, `footer.php`, `404.php`,
`single-blog.php`, and the `hero`/`guides`/`picks`/`outcomes`/`challenge`/
`logos_band`/`blog_posts_grid`/`search_filter`/`newsletter_cta` layouts —
all pass.

The JS was executed in headless Chrome:

| Check | Result |
| --- | --- |
| A malicious `SEARCH_INDEX` title renders as literal text, not parsed HTML | pass |
| No stray `<img>`/element gets created from a search-result title | pass |
| `handleCardKeydown` activates a card on Enter | pass |
| `handleCardKeydown` activates a card on Space | pass |
| `shortcutSearch`-style encoding escapes special characters in both params | pass |
| No uncaught JS errors | none |

The `hero.php` color-validation regex was verified against 10 cases (valid
hex/rgb/rgba pass; `expression()`, `url()`, and injection attempts are all
rejected) using Python's `re` module as a PCRE-equivalent stand-in.

Not verifiable here (no live WP/browser): the visual result of the lazy-load/
keyboard/label/focus-ring changes in the real theme, and Polylang's string
screen actually listing the newly-registered strings (requires a live
Polylang install).

## Files touched

`functions.php`, `header.php`, `footer.php`, `404.php`, `single-blog.php`,
`assets/js/theme-scripts.js`, `assets/admin/section-preview.js`,
`assets/css/theme-styles.css`, `template-parts/layouts/hero.php`,
`template-parts/layouts/guides.php`, `template-parts/layouts/picks.php`,
`template-parts/layouts/outcomes.php`, `template-parts/layouts/challenge.php`,
`template-parts/layouts/logos_band.php`,
`template-parts/layouts/blog_posts_grid.php`,
`template-parts/layouts/search_filter.php`,
`template-parts/layouts/newsletter_cta.php`.

Purely additive/corrective — no data touched, no Tailwind rebuild needed (no
new utility classes introduced; `for=`/`aria-label`/`tabindex`/`role`/
`loading` are plain HTML attributes, not Tailwind classes).
