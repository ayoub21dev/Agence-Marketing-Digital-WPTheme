# Page builder: live section preview via iframe (2026-07-08)

Implements roadmap item #1. Replaces the abandoned screenshot-based attempt —
this one renders the **real section**, so it can never go stale.

## How it works

An eye button is added to every entry of ACF's flexible-content "Add Row"
popup. Clicking it opens a modal that iframes the section, rendered standalone
by a new AJAX endpoint. "Insérer cette section" inserts it without re-opening
the popup.

Because no ACF row exists at "Add Row" time, sub-field lookups return `false`
and layouts fall back to their `v5_get_field_default()` copy — so hero, approach,
challenge, etc. render their real default text. The CPT-driven layouts (picks,
specialties, stats_band, logos_band, outcomes) query the database directly and
show **real content**. Layouts that legitimately render nothing yet (e.g.
`form_section` before a form is chosen) show a "Rien à afficher" notice instead
of a blank frame.

## Files

### New
- `assets/admin/section-preview.js` — eye button (injected via
  `MutationObserver`, since ACF builds the popup on demand), modal, iframe
  loading + scaling, insert handling.
- `assets/admin/section-preview.css` — eye button + modal, styled for ACF's
  **light** popup. (The previous attempt styled the icon white-on-white.)

### Edited: `functions.php` — new section "2b" (before asset enqueue)
- `v5_digital_layout_descriptions()` — French description for all 21 layouts.
- `v5_digital_layout_preview_data()` — merges ACF's real layout labels (read
  from `group_homepage_fields`) with the descriptions. Skips any layout with no
  template file. **Doubles as the security allowlist.**
- `v5_digital_render_section_preview()` — `wp_ajax_v5_section_preview`
  endpoint. Outputs a standalone HTML doc: fonts + `tailwind.css` +
  `theme-styles.css`, then the template part.
- `v5_digital_admin_section_preview_assets()` — enqueues on `post.php` /
  `post-new.php` only, when ACF is active. Passes nonce, post id, layouts.

## Three problems solved

1. **GSAP would have hidden the hero.** `header.php` sets `.hero-title` /
   `.section-label` to `opacity: 0`, revealed only when JS adds
   `.motion-enhanced`. The preview document isn't built from `header.php`, so
   it puts `motion-enhanced` on `<body>` directly. (Exactly the bug that broke
   the first headless screenshot of the live site.)
2. **Mobile breakpoint.** Layouts are designed around ~1280px. The iframe
   renders at a fixed `1280px` and is CSS-`transform: scale()`d down to the
   modal width (`transform-origin: top left`), with the stage clipping it.
   Height is measured from the iframe's document (same-origin) and re-measured
   after webfonts settle.
3. **Post context.** Several layouts call `get_the_ID()` / `get_permalink()`.
   The edited page's id is passed and `setup_postdata()` runs before rendering.

## UI pass (same day, after first review)

- **ACF's popup rendered above the modal.** Modal `z-index` raised to `999999`,
  and while it is open the popup is hidden with `visibility: hidden` (not
  `display: none`, so its anchors stay clickable for insertion).
- **Long section labels pushed the eye button out of view.** The label text is
  wrapped in a `.v5-sp-label` span (`flex: 1 1 auto; min-width: 0;` + ellipsis),
  the eye is `flex: 0 0 auto`, and the anchor gets a `title` with the full text.
- **The eye is now always visible**, not hover-only.
- **Horizontal scrollbar in the preview area** (spotted in a screenshot):
  `transform: scale()` shrinks the render visually but the scaler keeps its
  1280px layout box. Added a `.v5-sp-clip` wrapper (`overflow: hidden`) that
  carries the *scaled* height; the stage is now `overflow-x: hidden`.
- **Blank previews for row-only layouts.** `about_grid.php` (and friends) only
  render inside `have_rows('cards')`, so with no ACF row they emit just their
  `<style>` block — a non-empty string that shows nothing. The endpoint now
  probes for genuinely visible content (strips `<style>`/`<script>`/comments,
  keeps media tags) before deciding to show the "Rien à afficher" notice.
- Dialog widened to `min(1240px, 100vw - 40px)`.

## Row preview (second pass)

An eye button was added to **each existing row's toolbar** (next to
`+ / duplicate / trash`), previewing that row with its **saved** values.

- `v5_digital_render_saved_row()` walks `page_layouts` to the row index and
  renders the template part *inside* that row's context, so the layout's
  `get_sub_field()` calls resolve to that row's content.
- The endpoint takes a `row` param; `-1`/absent keeps the old generic demo used
  by the "Add Row" popup.
- **Honesty guarantees** — the preview never passes off stale content as current:
  - Rows present at page load are recorded as the *saved* count. A row added but
    not yet saved shows *"Cette section n'a jamais été enregistrée…"*.
  - Any `input`/`change` inside a row (or a drag-reorder of the field) marks it
    dirty; the modal then shows *"Modifications non enregistrées : l'aperçu
    montre la dernière version enregistrée."*
  - Server-side, if the row index's `get_row_layout()` doesn't match the
    requested layout (rows reordered without saving), nothing is rendered and a
    "save first" notice appears instead of the wrong section.
- Row previews hide the "Insérer" button (nothing to insert) and show a
  **"Rafraîchir"** button instead — cache-busted, so it refetches after saving.

### UI polish (third pass)

- **Modal is centred** in the viewport (`display:flex; align-items:center`)
  instead of pinned near the top.
- **Close button** restyled: 34px rounded square, grey fill, red destructive
  hover, brand-blue focus ring, subtle press animation.
- **Row eye now matches its siblings.** Rendered as
  `<a class="acf-icon small light acf-js-tooltip">` — ACF's own control markup —
  so it inherits their size, colour, hover fill and tooltip, and is hidden until
  the row is hovered (previously it was always visible while `+`/duplicate/trash
  were not). A CSS fallback reveals it on `:hover`/`:focus-within` in case ACF's
  own rule doesn't match our element.
- **Two bugs from that pass, found and fixed:**
  - The row eye disappeared entirely. The reveal-on-hover rule used a *direct
    child* combinator (`.layout:hover > .acf-fc-layout-controls …`), but ACF
    nests the controls inside a header wrapper, so the rule never matched and
    the button stayed at `opacity: 0`. Now uses descendant combinators.
  - ACF's `.acf-icon` (`text-indent: -9999px; overflow: hidden`) has the same
    specificity as our rule and can be enqueued *after* it, hiding the dashicon.
    Those overrides now carry `!important`. A regression test renders the eye
    under deliberately hostile ACF-like CSS.
- **Close button icon is a dashicon**, not a `&times;` character — a text glyph
  carries its own baseline/side-bearing, which is what pushed it off-centre.
  Verified centred to sub-pixel (0.0px offset on both axes). No red hover.
- **The amber warning now clears on save.** `wp.data.subscribe()` watches the
  block editor's save lifecycle; on a completed save the dirty flags are
  dropped, the saved-row baseline is re-recorded (rows added this session now
  exist in the DB), and an open row preview refreshes to show what was saved.
  **Autosaves and failed saves deliberately do not clear it** — both are
  covered by tests. The classic editor reloads on submit, so flags reset
  naturally.

Deliberately **not** implemented: previewing *unsaved* field values. That needs
the row's inputs POSTed and ACF's meta rebuilt via `acf_setup_meta()`, which is
brittle across nested repeaters and field types, and unverifiable without a PHP
runtime here. The dirty hint makes the current limitation explicit.

## Row-toolbar eye — match the native controls

The eye rendered inside a filled white circle (ACF's default `.acf-icon` style),
looking nothing like the flat white +/duplicate/trash icons beside it. Dropped
the `.acf-icon` class (kept `acf-js-tooltip` for the native tooltip) and
hand-styled the eye to match: transparent background, white 17px glyph, 28px
hit area, subtle translucent hover, reveal on row hover. Also removed the
drop-shadow/lift from the "Add Row" card hover (kept the blue border). Verified
via computed styles (background transparent, not a circle) + screenshot.

## Audit fixes (4-reviewer pass)

- **Row-index desync (MEDIUM, fixed).** Dirty tracking only fired on
  `input`/`change`/reorder. ACF **duplicate / insert-after / delete** shift the
  DOM indices of *existing* saved rows without any of those events, so a preview
  could request the wrong `row=N` and silently render a different saved section
  (or wrongly claim "not saved"). The MutationObserver now marks the field dirty
  on any row add/remove after init, so those rows show the "unsaved" warning
  instead. Verified in headless Chrome (duplicate mid-list → field dirty →
  shifted row warns).

Reviewed and accepted as-is (not bugs): breaking a `have_rows()` loop +
`reset_rows()`; `setup_postdata`/`wp_reset_postdata` in the ajax context;
featured-image `null` context inside custom `WP_Query` loops; the empty-content
probe; `wp.data.subscribe` cost. See the audit summary for the reasoning.

## `row=-1` — row index lookup required a `.values` container

Confirmed live via the diagnostic stamp (`sp-8 · mode=ROW · post=22 · row=-1`).
`rowIndexOf()` looked up rows inside a container with the exact class `.values`;
the running ACF build wraps rows in a differently-named container, so the lookup
returned `-1`, the server dropped the row param, and the preview fell back to the
demo (the static default section). Rewritten to compute a row's index from its
own sibling `.layout` rows (`rowsIn(row.parentNode)`), independent of the
container's class — a real row is always in its parent's children, so it can't
return -1. Clone templates (`.acf-clone` / inside `.clones`) stay excluded.
Verified against a DOM whose container is NOT `.values`: `row=0`/`row=2` correct.

An always-visible diagnostic stamp (`sp-N · mode · post · row`) and a version
counter were temporarily added to make the running build and its state
observable in one screenshot; they confirmed the `row=-1` cause and were then
**removed** (along with the server-side `diag:` line and the `$diag` capture in
`render_saved_row()`) once the fix was verified.

## Row eye silently degraded to a demo

The row-toolbar eye could open the generic "Add Row" DEMO (defaults + "Insérer"
button) instead of a row preview. Cause: `openModal` derived the mode from
`rowIndex >= 0`, so if `rowIndexOf()` returned -1 for any reason, a row preview
became a demo with no signal. Mode is now **explicit** (`isRow`): the row eye is
always a row preview, the popup eye always a demo. If a row preview can't locate
its index it shows a plain error ("position introuvable"), never a demo.
The endpoint also emits a `diag:` line (post_id / row / rows_found /
layout_at_index / reason) in the empty state, so a failure is observable.

## Robustness pass — "not saved" for a saved row + false dirty warning

Symptom: previewing a saved row showed "Section non encore enregistrée" AND a
spurious unsaved-changes warning on a freshly loaded page. Three root causes,
all fixed by removing fragile heuristics rather than patching them:

1. **`post_id` could arrive as 0** → the saved-row endpoint returns empty →
   misleading "not saved". `get_the_ID()` at enqueue time isn't reliable on all
   edit screens. Now the JS resolves the post id at request time
   (`currentPostId()`): `cfg.postId` → `wp.data.select('core/editor')
   .getCurrentPostId()` → the classic `#post_ID` input. Verified: with
   `cfg.postId = 0`, the preview URL still carries `post_id=22`.

2. **Row-index desync from clone templates.** `rowsOf()` used
   `field.querySelectorAll('.layout')`, which also matches ACF's `.clones`
   container (one hidden `.layout` template per layout type). Any miscount
   shifts every saved-row index → the server renders the wrong row → the layout
   guard fails → "not saved". Now scoped to the `.values` container's direct
   children (`valuesOf()`/`isRealRow()`), which by construction excludes clone
   templates and nested-field rows. Eyes are only attached to real rows.

3. **False "unsaved" warning.** The old per-row dirty tracking (input/change
   listeners + `sortstop` + a MutationObserver marking fields dirty on any row
   add/remove + per-row `data-v5-dirty` flags) produced false positives on load
   (ACF fires programmatic change events during init) and would have missed
   real edits from WYSIWYG/select2 (which fire synthetic events). **That entire
   subsystem was deleted** and replaced with one call to the block editor's own
   source of truth, `isEditedPostDirty()` (`postIsDirty()`): false on load, true
   after any edit, false after save. Fewer moving parts, no heuristics to
   desync. Verified: clean load and a programmatic input event both leave the
   preview un-warned; a dirty post warns; save re-baselines.

Net: removed ~60 lines of edit-detection heuristics; the feature now reads
existing editor state instead of trying to reconstruct it. Verified in headless
Chrome against a faithful ACF DOM (a `.clones` container of templates + a
`.values` of real rows) with a mocked editor store — 13 assertions, plus all
prior suites still pass.

## Iframe height overestimate (blank space under short sections)

Short sections (e.g. the ~107px stats band) showed a huge blank area below the
content. Cause: the height measurement used
`Math.max(body.scrollHeight, documentElement.scrollHeight)`, but
`documentElement.scrollHeight` always fills at least the iframe's own viewport
height (~748px), so `Math.max` reported the viewport height, not the content.
Tall sections hid it (their real height exceeds the viewport). Now measures
`body.scrollHeight` (falling back to the body's bounding box, then
documentElement, only if body reports 0). Verified: 110px content → 110px
measured; 1500px content → 1500px measured.

## "Add Row" popup → card grid

ACF's plain `<ul>` text list is restyled into a **2-column grid of cards**, the
pattern professional block/section pickers use (Gutenberg pattern inserter,
Elementor block panel — see web research). Each card:
- parses the `[Catégorie]` label prefix into a **colour-coded chip**
  (Accueil=blue, Commun=slate, Blog=purple, Contact=green, Méthodologie=amber,
  À Propos=teal), title stripped of the bracket, description clamped to 2 lines;
- **click card = insert** (ACF's own delegated handler on the untouched
  `<a data-layout>` still fires), **click eye = preview**;
- hover lifts the card with a brand-blue border + shadow.

`clampPopup()` nudges the widened popup (or its `.acf-tooltip` wrapper) back
into the viewport if it would overflow the right edge; a `@media (max-width:620px)`
query drops to one column.

**Every structural rule carries `!important`** and the `.acf-tooltip` wrapper is
widened too: ACF's `acf-input.css` loads after this stylesheet and would
otherwise win — its `display:block` collapsed the grid to one column and its
fixed-height `nowrap` rows clipped the titles and hid the descriptions. The
Chrome test harness now loads a simulation of ACF's constraining CSS (narrow
width, block display, 30px nowrap rows) and asserts the overrides win — the gap
that previously let the grid pass tests but break in the real editor. Verified:
2-col grid, wrapper widened, cards flex to full height with chip + title +
description all visible, titles wrap; plus a visual screenshot.

## Security

- `check_ajax_referer('v5_section_preview')` (nonce).
- Capability: `edit_post` on the previewed page, else `edit_posts`.
- `sanitize_key()` + **allowlist** — the template path is never built from raw
  input.
- `noindex, nofollow` + `nocache_headers()`; `wp_ajax_` (not `nopriv_`) so it
  is unreachable when logged out. Links are `pointer-events: none` inside the
  preview.

## Verification performed

`functions.php` passed a structural check (PHP-mode/string/heredoc-aware
delimiter balance). The admin JS was **executed in headless Chrome** against a
simulated ACF popup:

| Check | Result |
| --- | --- |
| Eye added to known layout / skipped for unknown | pass |
| Eye click opens modal and does **not** insert a row | pass |
| iframe URL carries action, layout, nonce, post_id | pass |
| Escape closes the modal | pass |
| "Insérer" inserts the row and closes the modal | pass *(fixed — see below)* |
| Content height measured, scale = stageWidth / 1280 | pass (0.832 @ 1065px) |
| Stage height = ceil(contentHeight × scale) | pass |
| Uncaught JS errors | none |

**Bug caught by the test:** `closeModal()` clears `currentAnchor`, so the
insert path found no anchor and silently fell through to the ACF API fallback.
The anchor is now captured before closing and passed into `insertLayout()`.

Not verifiable here (no PHP runtime on this machine): the endpoint's rendered
output. Open a page → Add Row → eye icon to confirm each layout renders.

## How to revert

1. Delete `assets/admin/`.
2. In `functions.php`, delete the whole `2b. PAGE BUILDER — LIVE SECTION
   PREVIEW` section (4 functions + `add_action('wp_ajax_…')` +
   `add_action('admin_enqueue_scripts', …)`), between sections 2 and 3.

Admin-only and purely additive: no front-end output, no Tailwind rebuild, no
data touched.

## Real GSAP motion in the preview

The preview originally hardcoded `motion-enhanced` on `<body>` and shipped no
JS, on the theory that loading GSAP might leave hero titles stuck at
`opacity:0` in an isolated single-section frame missing the header/other
sections `theme-scripts.js` normally expects. Checked that assumption against
the actual code instead of leaving it as a known limitation: every GSAP call
in `theme-scripts.js` is null-guarded (`if (header)`, `if (heroTitle)`,
`if (!title || ...) return`, etc.), so it already degrades cleanly with parts
of the page missing. Traced `header.php`'s CSS cascade too — the fallback rule
`body:not(.motion-enhanced) .hero-title{opacity:1}` outranks the base
`opacity:0` by specificity, so even if the GSAP CDN fails to load, nothing
gets stuck invisible; that's exactly why production doesn't need the hardcoded
class either.

Replaced the hardcode with the real thing: ported `header.php`'s three FOUC
rules into the endpoint's `<style>` block, dropped the forced
`motion-enhanced` class, and loaded the same three scripts `footer.php` loads
(`gsap.min.js`, `ScrollTrigger.min.js`, `theme-scripts.js`, versioned by
`filemtime` like the CSS already was) right before `</body>`. The preview now
runs the actual hero/section entrance animation and the logo marquee's
continuous scroll, instead of a static already-revealed frame.

## Row eye visibility — matched to ACF's actual reveal rule

The eye appeared alone on a resting (collapsed, light-background) row, and was
invisible on the blue selected/hover header. Two separate mistakes:

1. **Wrong color.** The eye was hardcoded `color: #fff`. The header bar has two
   states — light with dark icons at rest, blue with white icons when
   hovered/selected — and the native +/duplicate/trash icons use
   `color: inherit` to track it. Fixed the same way, so the glyph is dark on
   the light bar and white on the blue bar like its siblings. The hover
   highlight was likewise hardcoded white (`rgba(255,255,255,.22)`, invisible
   on the light bar); changed to a neutral `rgba(127,127,127,.22)` that reads
   on both.
2. **Wrong visibility rule.** Checked ACF's own compiled CSS/JS
   (`acf-pro-input.min.css`, `acf-pro-input.min.js`) directly: the native
   +/duplicate/trash icons are `visibility: hidden` at rest and only become
   visible on `.layout:hover`/`.layout.-hover` **or** `.layout.active-layout`
   (the class ACF's `setActiveLayout()` applies to the selected/open row on
   click — confirmed in the JS, called from a delegated click handler on
   `.layout`). The eye had instead been forced `opacity: 1` unconditionally,
   so on a resting row it stood alone while its correctly-hidden siblings
   stayed invisible — the "floating eye" in the screenshot. It also had
   `.layout.-open` in its reveal selector, which doesn't exist as a row class
   in ACF at all (that string is used elsewhere, on the popup control) and so
   was inert. Replaced the whole reveal mechanism with `visibility:
   hidden`/`visible` gated on the same two real conditions ACF uses
   (`:hover`/`.-hover`/`.active-layout`), plus `:focus-visible` for keyboard
   users (harmless — never fires on mouse click, so it can't reintroduce the
   "visible after click" bug).

### Follow-up: `color: inherit` produced a near-invisible "ghost" eye on hover

A screenshot showed a faint white ring where the eye sits on a merely-hovered
(not selected) row — still light gray, not blue. Checked ACF's real CSS
(`acf-fc-layout-actions-wrap{background:#f9f9f9}` at rest,
`.layout.active-layout>.acf-fc-layout-actions-wrap{background:...blue}` only
on selection) and confirmed the header bar only turns blue when a row is
*selected*, not on plain hover — hover just reveals the native icons in their
existing (dark, resting) color. `color: inherit` had been applied on **both**
hover and active-layout, so a merely-hovered row forced the eye white against
its still-light background — a near-invisible ghost. Fixed by restricting
`color: #fff` to `.layout.active-layout` only; on hover it now falls back to
its natural dark `inherit` value, matching the native icons exactly (dark on
hover, white when selected).

**Verified against ACF's own files, not assumption:** copied the real compiled
`acf-pro-input.min.css` (not a hand-rolled simulation) and the exact row
markup from ACF Pro's `Layout.php` template into a headless-Chrome test,
loaded `section-preview.css` after it in the same order WordPress enqueues it,
and read computed styles for three states:

| State | `visibility` | `color` |
| --- | --- | --- |
| Resting | `hidden` | — |
| Hovered (not selected) | `visible` | dark (`rgb(0,0,0)`) |
| Selected (`.active-layout`) | `visible` | white (`rgb(255,255,255)`) |

Also confirmed in ACF's minified JS that `.layout` (not some inner wrapper) is
exactly what gets `-hover`/`active-layout` classes, so the plain CSS
descendant selectors (`.layout:hover .v5-sp-eye--row`, etc.) match the real
DOM without any adjustment.
