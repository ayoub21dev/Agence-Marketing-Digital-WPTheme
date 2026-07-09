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

---

## Front-end features

### 5. Mega menu in the header

Replace the flat header nav with a mega menu.

- **Goal:** surface more of the site (services, cities, top agencies, recent
  guides) directly from the header.
- **Notes:** header nav is DB-driven (`v5_digital_get_primary_menu_items()`);
  a mega menu means a richer markup layer on top of the WP menu, plus mobile
  behaviour. Touches `header.php` + `theme-scripts.js`.

### 6. Exit-intent pop-up modal

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
  as item 7. Lives in `theme-scripts.js` + a small markup partial in
  `footer.php`.

---

## Research / exploration

### 7. Engagement & accessibility patterns

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
