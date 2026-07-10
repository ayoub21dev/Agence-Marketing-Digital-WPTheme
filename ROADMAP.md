
# Roadmap & Ideas

Backlog of upcoming features, editor-UX improvements, and use cases to explore.
Not committed work — a place to capture ideas before they become tasks.

---

## Page builder — editor UX

### 1. Live preview when selecting a section

Give editors a visual preview of each section as they pick it in the page
builder, so they see the result *before* saving.

- **Problem:** section names alone (`hero_section`, `picks_section`…) don't
  tell an editor what the block looks like; assembly is guesswork.
- **Goal:** thumbnail or inline preview per layout in the flexible-content
  picker → faster page assembly, fewer save-and-check loops.
- **Notes:** ACF flexible-content has no native preview; likely needs a small
  admin script + a preview image per `template-parts/layouts/*` file.

### 2. Conditional fields in the section selector

Show/hide fields based on context so only relevant settings appear.

- **Duplicate-section warning:** if a section is added that already exists on
  the page, show a dismissible warning ("this section is already used — you
  can continue anyway").
- **Category-filter dependency:** tie the category filter to the display
  choice. Example: if the block is set to show articles from a *single*
  category, the "enable category filter" option is disabled; if set to show
  *multiple* categories, the on-page filter only lists *those* selected
  categories (not every category on the site).
- **Notes:** partly maps to ACF conditional logic; the cross-field rules
  (duplicate detection, filter-follows-selection) need custom admin JS.
  Related to the existing `display_categories` / `show_filters` sub-fields in
  `blog_posts_grid.php`.

### 3. Page-scoped sections

Make each section (component) belong to a single page rather than a shared,
reusable block.

- **Goal:** a section instance is unique to the page it's placed on and bound
  to it — editing or deleting it affects only that page, never another.
- **Why:** prevents accidental cross-page edits and keeps each page's content
  self-contained and predictable.
- **Notes:** ACF flexible-content rows in `page_layouts` are already stored
  per-page (in the page's post meta), so a section is inherently local to its
  page today. This item is about making that guarantee explicit — and, if any
  shared/global blocks are ever introduced, keeping page sections cleanly
  separate from them.

### 4. Global options page

Add a central admin screen for site-wide info that isn't tied to any single
page.

- **Goal:** one place to manage global content — contact details (email,
  phone, address), social links, default logo, footer text, tracking IDs,
  and any recurring CTA copy — editable once and reused everywhere.
- **Why:** these values are currently scattered or hardcoded across templates;
  a single source stops the same data being re-entered per page and keeps it
  consistent site-wide.
- **Notes:** use ACF's `acf_add_options_page()` (register in `functions.php`);
  read values with `get_field('name', 'option')` via the existing
  `v5_digital_get_field()` wrapper (pass `'option'` as the post ID). Contrast
  with item 3 — options-page fields are intentionally global, page sections
  are intentionally local.

### 5. Back-office language: French by default, switchable to English per user

Ship the wp-admin back-office in French by default, while letting each
individual editor switch their own admin interface to English — independent
of which market/sub-site they're working in.

- **Goal:** the dashboard stays French out of the box (matches the team's
  day-to-day language), but any editor who prefers English — e.g. someone
  collaborating on the US or UAE market — can switch their own admin UI
  without changing it for anyone else.
- **Why:** this is a per-person convenience setting, not a per-market one —
  it shouldn't be tied to the Multisite front-end language split (`/fr-fr/`,
  `/en-us/`, `/ar-ae/`), which is about site content, not admin UI.
- **Notes:** WordPress core supports this natively — set the network's
  default site locale to `fr_FR`, install the `en_US` language pack, and
  each user picks their own admin language from their profile's "Language"
  field. Likely no custom development needed, just confirming the language
  packs are installed and available network-wide.

---

## Code & naming conventions

### 6. Namespaced prefix for CSS classes, IDs, and variables

Adopt one consistent prefix across custom CSS classes, HTML element IDs, and
CSS/JS variable names in the theme, rather than leaving them bare.

- **Goal:** make it obvious at a glance which classes/IDs/variables belong to
  this theme versus Tailwind utilities, ACF-generated markup, or whatever
  plugins a given market's sub-site happens to have active.
- **Why:** Tailwind utilities are unprefixed by design and that's fine, but
  custom one-off classes, JS-hook element IDs, and CSS custom properties can
  silently collide with WP-core or plugin class names — more likely in a
  Multisite network where the same theme runs across sub-sites that may each
  activate different plugins.
- **Notes:** `functions.php`-side code already sets a namespace precedent —
  `v5_digital_get_field()`, `v5_digital_get_primary_menu_items()` — worth
  reusing that same `v5-digital` root for CSS/JS instead of inventing a
  second convention. Decide and document this before Phase 2 of
  `REBUILD-PLAN.md` starts producing layout markup that would need
  retrofitting later.

---

## Front-end features

### 7. Mega menu in the header

Replace the flat header nav with a mega menu.

- **Goal:** surface more of the site (services, cities, top agencies, recent
  guides) directly from the header.
- **Notes:** header nav is DB-driven (`v5_digital_get_primary_menu_items()`);
  a mega menu means a richer markup layer on top of the WP menu, plus mobile
  behaviour. Touches `header.php` + `theme-scripts.js`.

### 8. Exit-intent pop-up modal

Show a modal when a visitor is about to leave the page.

- **Trigger:** cursor exits toward the browser chrome (desktop `mouseout` at
  `clientY <= 0`), or a back-gesture / rapid scroll-up on mobile. Fire once per
  session (`sessionStorage` flag), never on the same page twice.
- **Content:** last-chance CTA — e.g. "Trouvez votre agence en 2 min"
  (matchmaker wizard), newsletter capture, or a link to the guides.
- **Rules:** don't fire on form pages or if the visitor already converted;
  respect a global on/off switch (ACF option or theme option).
- **Notes:** must be dismissible (Escape, backdrop click, close button),
  focus-trapped, `aria-modal`, and respect reduced-motion — same constraints
  as item 11. Lives in `theme-scripts.js` + a small markup partial in
  `footer.php`.

### 9. Lazy-loading and loading effect for images

Load images lazily and show a loading transition while they resolve, instead
of a blank box or an abrupt pop-in.

- **Goal:** images across the site (client-logo bands, case-study visuals,
  guides/blog thumbnails, hero media) defer off-screen loading and show a
  placeholder effect — blur-up, skeleton, or fade-in — while loading, rather
  than an empty space that shifts the layout.
- **Why:** a marketing site this image-heavy directly affects both perceived
  speed and actual Core Web Vitals (LCP/CLS) — unmanaged image loading works
  against the "vitesse extrême" performance goal already stated in the
  README.
- **Notes:** native `loading="lazy"` plus explicit `width`/`height` (or
  `aspect-ratio`) covers layout-shift for free; the blur-up/skeleton
  transition on top needs a small addition to `theme-scripts.js` (or a
  CSS-only placeholder background). Applies to `logos_band.php`,
  `blog_posts_grid.php`, `guides.php`, and any hero/media field once those
  layouts are built.

### 10. Conditional image handling and mobile-menu behaviour

Two related "same component, different device" decisions to make once,
rather than solving ad hoc per layout: how images swap between mobile and
desktop, and how the header nav behaves as a distinct mobile menu.

- **Images:** where art direction matters (e.g. a wide hero crop on desktop
  vs. a tighter portrait crop on mobile), serve a genuinely different image
  per breakpoint rather than relying only on responsive scaling of the same
  crop.
- **Mobile menu:** the header nav needs its own conditional mobile behaviour
  (collapsed/hamburger menu) distinct from the desktop markup — on top of
  whatever the mega-menu work in item 7 produces.
- **Why:** both are the same underlying problem — a component that must
  render differently by device — worth a single documented pattern instead
  of a one-off fix each time it comes up.
- **Notes:** touches `header.php` (nav markup) and `theme-scripts.js` (menu
  toggle behaviour), plus `<picture>`/art-direction breakpoints for any
  hero/media ACF field once defined. Coordinate with item 7 so the mobile
  menu isn't designed twice.

---

## Research / exploration

### 11. Engagement & accessibility patterns

Investigate UI patterns that raise on-page engagement and improve
accessibility, then recommend which to implement and how.

- **Candidates to evaluate:** pop-ups / slide-ins, tooltips, sticky CTAs,
  reading progress, related-articles rails.
- **Accessibility angle:** ensure any pattern chosen is keyboard-navigable,
  screen-reader friendly, and respects reduced-motion (the theme already
  gates GSAP on reduced-motion).
- **Deliverable:** a short recommendation of which patterns to build and where.

---

## Ideas parking lot

_Add loose ideas here before they're fleshed out._
