# Roadmap & Ideas — Agence Marketing Digital

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

---

## Front-end features

### 3. Mega menu in the header
Replace the flat header nav with a mega menu.

- **Goal:** surface more of the site (services, cities, top agencies, recent
  guides) directly from the header.
- **Notes:** header nav is DB-driven (`v5_digital_get_primary_menu_items()`);
  a mega menu means a richer markup layer on top of the WP menu, plus mobile
  behaviour. Touches `header.php` + `theme-scripts.js`.

---

## Research / exploration

### 4. Engagement & accessibility patterns
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

-
