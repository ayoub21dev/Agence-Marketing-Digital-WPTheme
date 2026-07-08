# Section audits — ACF page builder

Audit of the 21 `page_layouts` sections (`template-parts/layouts/*.php`), one report per section.
Goal: find **silent logical failures** (the category-filter bug, the Site Settings email bug were this class)
before an editor finds them in production. **Reports only — no fixes are applied by this effort.**

## Method (applied identically to every section)

Each section is checked against 7 lenses:

| Lens | Question |
|---|---|
| **A. Data flow** | Is every JSON field read by the PHP? Is every PHP read defined somewhere? Same value from two sources → who wins, and is it obvious? |
| **B. Use-case matrix** | Every toggle/select/repeater combination → what actually renders? (0/1/2/many rows, empty CPT, cleared fields, long text) |
| **C. Silent failures** | Any combination that renders nothing/wrong with no warning to the editor |
| **D. Editor comprehension** | Labels, instructions, legacy fields, technical input without example |
| **E. Unused guard rails** | Missing `max:1`, repeater `min`/`max`, `required`, `conditional_logic`, `message` fields |
| **F. Front-end resilience** | Escaping, empty states, `=== null` vs `empty()` fallbacks, hardcoded strings vs `v5_t()` |
| **G. Improvement ideas** | Cheap editor-UX suggestions (recorded, not implemented) |

**Severity:**
- **BLOCKER** — an editor action produces a broken or silently empty page
- **HIGH** — a real value is silently ignored/overridden, or a combination fails with no warning
- **MEDIUM** — works, but an editor will very likely misunderstand it
- **LOW** — cosmetic, stale comment, missing instruction on an obvious field

**Rules:** every finding cites `file:line`, is reproducible in wp-admin, and DB claims are
verified with WP-CLI (`wp-studio.sh eval`). Unverifiable suspicions go in a separate
"Suspected, not reproduced" subsection.

## Systemic issues (true across many sections — not repeated in each report)

| # | Issue | Evidence |
|---|---|---|
| S1 | **No layout has `max:1`** — singleton sections (hero, search, stats, contact form) can be added twice. The blog builder already does it right (`"max": 1` in `group_blog_content.json`). Only mitigation is the dismissible amber duplicate warning (`functions.php:415`). | `group_homepage_fields.json` |
| S2 | **No repeater has `min`/`max`** — grid layouts (cards, points) can be saved with 0 or 20 rows; design intent is not enforced or hinted. | all 9 repeaters |
| S3 | **No save-time validation anywhere** (`acf/validate_value` / `acf/validate_save_post` absent). All guard rails are client-side DOM JS that a reload or JS error removes. | `functions.php` |
| S4 | **16 of 21 layouts have zero instruction text.** Non-obvious fields (raw SVG textareas, icon-slug text fields) give the editor nothing. | `group_homepage_fields.json` |
| S5 | **Static-fallback strategy is inconsistent**: some layouts fall back on `=== null` only (an editor-saved empty repeater shows a blank section, not the fallback), others early-return, others render defaults. Editors can't predict what "leave it empty" does. | challenge/approach/outcomes vs methodology_* vs stats_band |
| S6 | **Language mix in admin**: Site Settings instructions are English; all other groups are French. | `group_site_settings.json` |
| S7 | **Hardcoded front-end French not routed through `v5_t()`** in several layouts → untranslatable with Polylang, unlike the rest of the theme. | e.g. `contact_form.php` (entire form UI) |
| S8 | **Near-identical layout labels**: `[Commun] CTA — bas de page (titre + boutons)` vs `[À Propos] CTA — bas de page (titre + boutons)` — only the prefix differs, sub-field names diverge. | `footer_cta_section` / `about_cta_section` |

## Report index

See [_TRACKER.md](_TRACKER.md) for status and finding counts.
