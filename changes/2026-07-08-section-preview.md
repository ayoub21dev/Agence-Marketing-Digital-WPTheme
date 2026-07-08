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
