# Agence Marketing Digital — Theme Guide

WordPress theme for a **French-language digital-agency directory / marketplace** (Morocco-focused). Tailwind + ACF-driven flexible content, GSAP motion, Polylang-ready. This folder is the **git repo root**; pushing to `main` deploys it.

---

## Local dev, build & deploy (read first)

- **Local environment:** WordPress Studio, SQLite DB. To run WP-CLI against the local site, see the WP-CLI section in `../../../AGENTS.md` (the wrapper: `bash "/c/Users/Ayoub JALYTA/wp-cli/wp-studio.sh" <args>`). Use it to verify edits against the real DB (menus, options, posts).
- **Edit → preview:** PHP/CSS/JS edits show on the local site on refresh (no build for PHP). **Content** (pages, posts, menus, images) lives in the DB, not in files — it does NOT move with git.
- **Tailwind is compiled AND committed.** Source `src/tailwind.css` → output `assets/css/tailwind.css`. There is **no build step on the server**. If you add/change Tailwind classes in any `.php`, you MUST rebuild before committing or the styles won't appear in production:
  - `npm run build` (minified, one-shot) or `npm run watch` (dev).
  - Tailwind scans `./*.php`, `./template-parts/**/*.php`, `./assets/js/**/*.js` (see `tailwind.config.js`).
- **Deploy:** GitHub Actions FTP-deploy (`.github/workflows/deploy1.yml`) on push to `main` → Hostinger `/public_html/wp-content/themes/agence-marketing-digital/`. Commit/push only when asked.
- **`.gitignore`:** `node_modules/` ignored; compiled `assets/css/tailwind.css` IS committed on purpose.

---

## Architecture

### functions.php (~4,350 lines) — the engine
Organized in numbered sections:
1. **ACF safety wrappers** (lines ~1–99): `v5_digital_get_field()`, `v5_digital_have_rows()`, `v5_digital_the_row()`, `v5_digital_get_row_layout()`, `v5_digital_get_sub_field()`, etc. — all no-op safely if ACF is inactive. **Templates should use these wrappers, not raw ACF functions.**
2. **CPT/taxonomy registration** (~102–276): hooked to `init` priority 20.
3. **ACF field groups in PHP** (~278–1966): a *fallback* only used if the matching `acf-json/group_*.json` is absent. The JSON is the source of truth.
4. **Asset enqueue** (~1968–2009): `v5_digital_enqueue_assets()` — tailwind.css, theme-styles.css, theme-scripts.js, all versioned by `filemtime`.
5. **Activation seeding** (~2011–3312): `v5_digital_setup_theme_content()` on `after_switch_theme` — creates pages, the primary + 4 footer menus, and seeds demo CPT content (agencies, testimonials, specialties, stats, partner logos, blog posts).
6. **Theme setup / menus / i18n** (~3314–3612): register menus, title-tag, translations, primary-menu resolution.
7. **Dynamic XML sitemap** (~3614–3691): custom `/sitemap.xml`.
8. **Data migrations & self-healing** (~3693–4351): run once per version via `admin_init`; blog CPT→posts migration, category backfill, author sync, menu name fixes, footer-menu setup.

### Conventions
- **Function prefix:** `v5_digital_*` (and a few `v5_*`). Match this when adding functions.
- **Translation:** wrap user-facing strings in `v5_t('French text')`. The string map is `v5_digital_ui_strings()` (~line 3350); strings are registered with Polylang on `init`. Falls back to the French literal if Polylang is off.
- **Graceful degradation:** everything checks for ACF/Polylang before using them.
- **Known platform gotcha:** never use `GLOB_BRACE` — it's undefined on some PHP builds (incl. local Studio). Use two `glob()` calls merged instead (see `v5_digital_acf_json_manages_content_types()`).

---

## Content model (ACF)

CPTs and taxonomies are defined as JSON in `acf-json/` (ACF "local JSON" sync — edits in the ACF admin UI save back to these files, so they version in git).

**CPTs:** `agency` (title/editor/excerpt; taxonomies below), `partner_logo`, `specialty_hub`, `stat_metric`, `testimonial`.
**Taxonomies:** `agency_service` (`/service/`), `agency_city` (`/city/`) — both on `agency`.

**Field groups (`acf-json/group_*.json`):**
- `group_agency_fields` — logo_text/image/url, rating_value, review_count, agency_rank, website
- `group_specialty_fields` — icon_svg, direct_link_parameter, sub_services (repeater)
- `group_stat_fields` — stat_number, stat_label
- `group_testimonial_fields` — rating, author_role/image, hired_agency_*, project, result
- `group_partner_logo_fields` — logo_image_media, logo_image_url
- `group_blog_meta` (post) — badge, read_time, author_name, cover_image_url/media
- `group_blog_content` (post) — `blog_layouts` flexible content → `agency_reviews_block` (repeater of agency reviews)
- `group_homepage_fields` (pages) — **`page_layouts` flexible content**, the page builder (see below)

---

## Template system

WordPress hierarchy + an ACF flexible-content page builder.

| Context | Template | Notes |
|---|---|---|
| Front page `/` | `front-page.php` | loops `page_layouts` |
| Generic page | `page.php` | native editor content OR `page_layouts` |
| Single post | `single.php` → `single-blog.php` | article + optional `blog_layouts` |
| 404 | `404.php` | animated hero + recent posts |
| Fallback | `index.php` | minimal |
| Header/Footer | `header.php` / `footer.php` | nav from DB menus |

### Flexible-content dispatch
`front-page.php` / `page.php` loop the `page_layouts` rows and dispatch by layout name:
```php
while (v5_digital_have_rows('page_layouts')) : v5_digital_the_row();
    $layout = v5_digital_get_row_layout();          // e.g. "hero_section"
    $clean  = str_replace('_section', '', $layout); // "hero"
    get_template_part('template-parts/layouts/' . $clean); // hero.php
endwhile;
```
So **ACF layout `foo_section` ⇒ file `template-parts/layouts/foo.php`.** To add a section: add the layout to the `page_layouts` flexible-content group (in ACF UI / JSON) AND create the matching `template-parts/layouts/<name>.php`.

**Layout files** (`template-parts/layouts/`): hero, common_hero, stats_band, search_filter, logos_band, challenge, approach, outcomes, picks, specialties, guides, blog_posts_grid, footer_cta, contact_form, newsletter_cta, methodology_process, methodology_evidence, methodology_monitor, about_grid, about_cta. Many query CPTs directly (picks→agency, specialties→specialty_hub, stats_band→stat_metric, logos_band→partner_logo, outcomes→testimonial) and have static fallbacks when empty.

### Navigation
- **Header menu:** `v5_digital_get_primary_menu_items()` reads the WP menu on the `primary` location (Polylang-aware), with `v5_digital_nav_fallback_links()` as a hardcoded fallback (accueil, blog, à propos, méthodologie, contact). Menus are **DB content**, so local ≠ production unless each site's menu matches.
- **Footer:** 4 columns by location (`footer_explore`, `footer_resources`, `footer_villes`, `footer_legal`) via `v5_digital_render_footer_column()`. No hardcoded fallback — an unassigned location renders nothing.

---

## Frontend assets
- `assets/js/theme-scripts.js` — GSAP motion system (respects reduced-motion), logo marquee, custom selects, Ctrl/Cmd+K search palette, matchmaker wizard, mobile menu, FAQ accordions, filter redirects to `/annuaire/`.
- `assets/css/theme-styles.css` — animations, nav active underline, accordion, motion reveals (hand-written; loaded after tailwind.css).
- Icons: Lucide (deferred). Fonts: Inter (body), Space Grotesk (`font-display`), JetBrains Mono. Brand color = `brand-600` (`#2563eb`).
