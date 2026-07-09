# Page builder: per-page section groups, "utilisée" badge, modal category chip (2026-07-09)

Builds on `2026-07-08-section-preview.md`. The "Ajouter une Section" picker
offered **all 21 layouts on every page** — `group_homepage_fields` attaches to
`post_type == page` with no per-page scoping, so the contact page listed
`[Méthodologie]` sections and the blog page listed `[À Propos]` ones.

The picker now groups sections by the page being edited, marks the ones already
in use, and parks the rest behind a toggle. The preview modal's heading renders
the category as a coloured chip instead of a raw `[Commun] …` label.

## How it works

Every layout encodes its page in its label prefix (`[Accueil]`, `[Blog]`,
`[Commun]`, `[Contact]`, `[Méthodologie]`, `[À Propos]`, `[Formulaire]`), and
`section-preview.js` stamps a category slug on each card as `data-cat` (it also
drives the chip colour). This change reuses that signal rather than introducing
a parallel one — but see "Translation-proof categories" below for where the
slug actually comes from.

**Filtering is client-side, deliberately.** A server-side
`acf/fields/flexible_content/layout` filter would *remove* layouts, which would
(a) make a "show the others" toggle impossible — they wouldn't be in the DOM —
and (b) risk orphaning pages that already contain a now-unavailable section.
Hiding rendered cards keeps every layout insertable and touches no content.

### The picker, by default

```
SECTIONS DE CETTE PAGE      ← the page's own category
SECTIONS COMMUNES           ← ONLY the commun/formulaire sections in use
[ Afficher les autres sections ]
    AUTRES SECTIONS COMMUNES    ← commun/formulaire not in use   } hidden
    SECTIONS DES AUTRES PAGES   ← every other category           } behind
                                                                   the toggle
```

- `[Commun]` and `[Formulaire]` belong to no page of their own, so they get
  their own band rather than being buried. Without this, `common_hero_section`
  — the first section on every non-home page — would sit behind the toggle.
- The commun band lists **only what the page actually uses**; the rest move
  below the toggle. An unused newsletter CTA is not "another page's" section,
  so the revealed area is split into two labelled sub-groups rather than one
  mislabelled `Sections des autres pages`.
- If the page uses no commun section (a fresh page), the band is omitted
  entirely — no empty labelled header.

### "Utilisée" badge

Any card whose layout is already on the page is badged, in **every** group —
including a section borrowed from another category (the front page uses
`guides_section`, a `[Blog]` layout; it is badged when revealed).

The count is read **live from the DOM** (`usedLayoutCounts()`), not from the
server, so rows added or removed since the last save are reflected. It excludes
ACF's hidden `.clones` templates (via the existing `isRealRow()`) and rows
belonging to a *nested* flexible-content field (via a `closest()` identity
check) — either would otherwise inflate the count.

The pill reads `Utilisée`; the full sentence, with a count, is the tooltip
(`Déjà utilisée 2 fois sur cette page`). Short text on purpose: the pill sits
beside the category chip on a ~256px card, and `Déjà utilisée` risked wrapping
the eye button onto a second line.

This complements — does not replace — `v5_digital_page_builder_duplicate_notice()`,
which warns *after* a duplicate is added. The badge is the signal *before*.
Badged cards stay fully clickable: most sections may legitimately repeat.

### Preview-modal category chip

`setModalTitle()` splits the label and renders `( COMMUN ) Bandeau — logos
partenaires` instead of printing `[Commun] Bandeau — logos partenaires`
verbatim. The `[Cat] Titre` regex was factored out of `decoratePopup()` into a
shared `splitLabel()` so the card and the modal can't diverge.

The seven category colours are now declared **once** as `--v5-chip-bg` /
`--v5-chip-fg` custom properties, consumed by both `.v5-sp-card-cat` and
`.v5-sp-title-cat`. Previously a retint would have had to be applied twice.
The modal chip is one step larger (`10px`/`3px 9px` vs `9.5px`/`2px 7px`) to
sit against the 15px heading.

## Files

### Edited: `functions.php`

- **`v5_digital_page_builder_category($post_id)`** (new, above
  `v5_digital_admin_section_preview_assets`) — maps the edited page to a slug
  matching `catSlug()` in the JS: front page → `accueil`; then by slug,
  `blog`→`blog`, `annuaire`→`accueil`, `about`→`propos`, `contact`→`contact`,
  `methodologie`→`methodo`. Returns `''` for anything else, which disables
  filtering entirely (see "Never an empty picker" below). Guarded to
  `post_type === 'page'`: the edit screen also loads for posts, which carry
  their own `blog_layouts` flexible field, and a post slugged `contact` would
  otherwise report a page category. Filterable via
  `add_filter('v5_digital_page_builder_category', …)` so custom pages can opt
  into a category without touching code.
- **`v5_digital_admin_section_preview_assets()`** — localizes `pageCategory`
  plus the new i18n strings (`groupPage`, `groupCommon`, `groupOtherCommon`,
  `groupOther`, `used`, `usedOnce`, `usedTimes`, `showOther`, `hideOther`).

### Edited: `assets/admin/section-preview.js`

`splitLabel()`, `setModalTitle()`, `COMMON_CATS`, `cardCell()`,
`builderField()`, `usedLayoutCounts()`, `fullWidthCell()`, `groupHeader()`,
`markUsed()`, `applyCategoryFilter()` (called at the end of `decoratePopup()`,
before `clampPopup()`).

### Edited: `assets/admin/section-preview.css`

Chip colour custom properties, `.v5-sp-group`, `.v5-sp-card-used`,
`.v5-sp-title-cat` / `.v5-sp-title-text`, `.v5-sp-more` / `.v5-sp-more-li`,
`.v5-sp-hidden`.

No new Tailwind classes (this is a hand-written admin stylesheet), so **no
`npm run build`**.

## Never an empty picker

Three guards, in `applyCategoryFilter()`:

1. `pageCategory === ''` (an unmapped page, e.g. `sample-page`) → no grouping,
   no toggle, all 21 shown. An editor is never left with nothing.
2. No card matches the page's category → same bail-out.
3. Only one group and nothing to reveal → grouping would add noise, not signal.

A layout with **no template file** is skipped by `v5_digital_layout_preview_data()`,
so it never becomes a card, so the filter never hides it: it would stay visible
in every category. All 21 layouts currently have templates, so this is latent,
not live. Documented rather than fixed.

## Translation-proof categories

`catSlug()` originally decided a section's category by **matching French words in
its display label** (`accueil`, `commun`, `thodo`, `propos`, `formulaire`). That
made the entire grouping depend on the label staying French — and a label is
user-facing text that can be translated (Polylang Pro's ACF integration, WPML) or
simply renamed in the ACF admin.

Reproduced in headless Chrome with the labels translated to English
(`[Home] …`, `[Shared] …`): **15 of 21 cards fell through to `default`**, zero
page-category cards stayed visible, and the group headers, toggle and badges all
vanished. A silent, total collapse of the picker — no error.

So the category now comes from **`v5_digital_layout_categories()`**, a
`layout name => slug` map keyed on the stable layout name (`about_grid_section
=> propos`), exposed per layout as `cat` in `v5_digital_layout_preview_data()`.
The JS helper `layoutCat(name, labelCat)` prefers that value and only falls back
to parsing the label for a layout with no map entry.

Consequence, by design: the chip's **text** still follows the (translatable)
label, while the chip's **colour** and the grouping follow the stable slug. Both
were asserted.

The map is filterable (`v5_digital_layout_categories`), so a new layout can
declare its category without editing the function.

## A CSS specificity trap (found by test, worth keeping)

`.acf-fc-popup li.v5-sp-group { display: block !important }` and
`.acf-fc-popup li.v5-sp-hidden { display: none !important }` have **identical
specificity** (0,2,1). The group rule is later in the file, so it won — the
"Sections des autres pages" header refused to hide while collapsed.

The hide rule is therefore written `.acf-fc-popup ul li.v5-sp-hidden` — the
extra `ul` lifts it to (0,2,2) so it beats both the group rule and
`.acf-fc-popup li { display: flex !important }` (0,1,1) **regardless of source
order**. That `ul` is load-bearing; a comment in the stylesheet says so.

## ACF contract this depends on

Verified against the installed **ACF Pro 6.8.4**, not assumed:

- `src/Pro/Fields/FlexibleContent/Render.php:229-239` builds the picker as
  `<ul><li><a data-layout="…">label</a></li>…</ul>` — the markup the CSS grid
  and `cardCell()` rely on.
- Insertion is a **delegated** `click [data-layout]` on the popup root. The
  toggle `<button>` carries no `data-layout`, so it cannot trigger an insert;
  it also calls `stopPropagation()`, since an outside click closes the popup.
- Reordering `<li>` nodes is therefore safe — ACF binds by delegation, not by
  element identity.
- ACF 6.5+ reuses the `acf-fc-popup` class for a **second** popup, the
  "more layout actions" menu (`tmpl-more-layout-actions`: `<ul role="menu">`
  with `data-action` anchors). It has no `data-layout` anchors, so no cards are
  decorated, so `applyCategoryFilter()` no-ops on it. Explicitly tested.

## Verification performed

No PHP or Node runtime on this machine. `functions.php` passed a
string/comment-aware delimiter-balance check; the CSS a brace-balance check;
and every i18n key was confirmed both defined in PHP and consumed in the JS.
Every slug `v5_digital_page_builder_category()` can emit was cross-checked
against what `catSlug()` can return (a mismatch would silently filter a page to
zero cards).

The slug map was validated against the **real Studio SQLite database**, not
guessed: all five slugs exist, and `page_on_front = 22` is `accueil`. Only
`sample-page` and `privacy-policy` are unmapped, and they correctly fall
through to the unfiltered picker.

The JS ran in real headless Chrome (CDP) against the real `section-preview.js`
and `section-preview.css`, with ACF's actual popup markup:

| Suite | Checks |
| --- | --- |
| Grouping + badge, driven by each page's **real DB rows** | 196 |
| Hard audit — grid spans, idempotency under re-observation, insert path | 110 |
| Modal chip — colour parity with cards, no raw brackets, a11y name | 70 |
| ACF "more layout actions" menu unaffected | 7 |
| Translation-proof categories: French labels, English labels | 22 |

Plus a **negative control** for the last suite: run with the server-supplied
`cat` removed (i.e. the old label-parsing code) against English labels, and
7 of 11 checks fail exactly as described above. The test detects the bug it
claims to prevent.

All pass. Notable individual checks: the toggle spans `grid-column: 1 / -1` and
sits below every visible card; a re-observed popup grows no second toggle; a
card click still propagates (insert works) while the toggle's click does not;
each modal chip's computed colour equals its card chip's, byte for byte; the
duplicate case reports "2 fois"; clone templates and nested-field rows never
inflate the used count.

An earlier version of the grid assertion was `ok(x || true, …)` — a tautology
that could never fail. Replaced with real `getComputedStyle` measurements.

## Not verified here

The visual result in the real WP admin (spacing, colour against ACF's own
chrome), and behaviour when `contact_form_section` hits its `max: 1` — on the
contact page `[Contact]` has exactly one layout, so once it is added ACF
disables that card and the default band can look sparse until the toggle is
expanded.

## Pre-existing bug found during this work (NOT introduced here — now fixed)

`.acf-fc-popup ul { display: grid !important; width: 540px !important }` (from
the 2026-07-08 card-grid restyle) was unscoped, so it also matched ACF 6.5+'s
Rename/Disable menu, which reuses the `acf-fc-popup` class. Measured on that
menu: `display: grid`, `width: 540px`, `grid-template-columns: 256px 256px` —
a two-column card grid where a small dropdown belongs.

**Fixed** by scoping the three structural picker rules (wrapper, `ul`, `li`) and
the single-column media query to
`.acf-fc-popup:not(.acf-more-layout-actions)`. The `:not()` is load-bearing and
is commented as such in the stylesheet.

Specificity was re-checked: `:not(.acf-more-layout-actions)` lifts the `li` rule
from (0,1,1) to (0,2,1), which still loses to `.acf-fc-popup ul li.v5-sp-hidden`
(0,2,2) — so hidden cards stay hidden — and still ties-then-loses to the later
`.v5-sp-group` / `.v5-sp-more-li` rules, exactly as before.

Verified both directions in headless Chrome against ACF's real markup: the
actions menu now computes `display: block` at its natural width with
`grid-template-columns: none` and unforced `<li>`, while the real layout picker
still computes `display: grid` at `540px`.

## How to revert

1. `functions.php` — delete `v5_digital_page_builder_category()`; remove
   `pageCategory` and the nine new i18n keys from the `wp_localize_script()`
   array in `v5_digital_admin_section_preview_assets()`.
2. `assets/admin/section-preview.js` — delete `applyCategoryFilter()`,
   `markUsed()`, `groupHeader()`, `fullWidthCell()`, `usedLayoutCounts()`,
   `builderField()`, `cardCell()`, `COMMON_CATS`, and the
   `applyCategoryFilter(popup)` call in `decoratePopup()`. For the modal chip,
   delete `setModalTitle()` and restore
   `modal.querySelector('.v5-sp-title').textContent = info.label || opts.layout;`.
   `splitLabel()` may stay (still used by `decoratePopup`) or be inlined back.
3. `assets/admin/section-preview.css` — delete the `.v5-sp-group`,
   `.v5-sp-card-used`, `.v5-sp-title-cat`, `.v5-sp-title-text`, `.v5-sp-more*`
   and `.v5-sp-hidden` rules; revert the chip colours from `var(--v5-chip-*)`
   back to literal values on the seven `a[data-cat="…"] .v5-sp-card-cat` rules.

Purely an editor-UX change: no page content, no database field, and no
front-end rendering is touched. Nothing restricts what can be saved — every
revealed section still inserts normally.
