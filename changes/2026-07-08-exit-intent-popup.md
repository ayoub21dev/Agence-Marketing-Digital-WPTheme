# Exit-intent newsletter popup (2026-07-08)

Implements roadmap item #4 ("Exit-intent pop-up modal"). Content: newsletter
signup (the roadmap listed matchmaker CTA / newsletter / guides link as
candidates — newsletter was the choice made here).

## How it works

A `<dialog>` modal (matching the existing `#search-modal`/`#matchmaker-modal`
pattern in `footer.php`) opens on a strong "about to leave" signal:

- **Desktop:** cursor exits the viewport upward with no related target — i.e.
  toward the tab bar/address bar (`mouseout` at `clientY <= 0`).
- **Touch devices:** no cursor to read exit intent from, so a fast upward
  scroll near the top of the page stands in (> 80px in < 300ms, only while
  `scrollY < window.innerHeight`).
- **Explicit:** clicking the article's own "retour aux articles" link
  (`#v5-back-to-articles`, `single-blog.php`). Stronger than the other two
  signals — an actual navigation, not an inference — so it's handled
  differently: the click is intercepted (`preventDefault`) and the popup
  opens in its place; whatever then dismisses the popup (✕, backdrop click,
  or auto-close after submitting) completes the original navigation
  (`exitIntentPendingRedirect` in `theme-scripts.js`), so the visitor still
  ends up back on the blog listing — the popup interrupts, it doesn't trap.

Fires **once per session** (`sessionStorage`), and never again for a visitor
who already subscribed (`localStorage`, persists across sessions — closer to
the roadmap's "or if the visitor already converted"). Server-side, a global
switch (`v5_digital_exit_intent_enabled()`) restricts it to **article pages
only** (`is_singular('post')` — the request narrowed this from "any page" to
specifically when leaving a blog post), filterable
(`add_filter('v5_digital_exit_intent_enabled', '__return_false')`) without
touching JS.

The submit handler is **cosmetic**, matching the existing footer
`newsletter_cta_section` band: no backend storage or email-service
integration exists anywhere in this theme yet (checked — the matchmaker
wizard and the newsletter band are both front-end-only fakes, no
`wp_ajax_*`/`fetch()` calls anywhere in `theme-scripts.js`). Building real
persistence is a separate, larger task (storage design, an admin view,
GDPR/consent, spam protection) that would apply to both existing forms at
once — flagged here rather than solved solo for just the new popup, to avoid
ending up with one working form and two fake ones.

## Files

### Edited

- **`functions.php`** — new section "3b. EXIT-INTENT NEWSLETTER POPUP" (after
  section 3, before section 4): `v5_digital_exit_intent_enabled()`, the on/off
  gate — `is_singular('post')`, filterable via `v5_digital_exit_intent_enabled`.
- **`header.php`** — `window.wpThemeSettings` gains
  `exitIntentEnabled: <?php echo v5_digital_exit_intent_enabled() ? 'true' : 'false'; ?>`,
  next to the existing `homeUrl`.
- **`footer.php`** — new `<dialog id="exit-intent-modal">`, placed right after
  `#matchmaker-modal`: brand-gradient header + close button, an email form,
  and a success state (visual style lifted from the matchmaker wizard's own
  success step).
- **`assets/js/theme-scripts.js`** — `openExitIntent()` / `closeExitIntent()`
  (mirror `openMatchmaker()`/`closeMatchmaker()`, reusing the existing
  `animateModalOpen`/`animateModalClose` GSAP helpers, which already no-op
  under reduced motion or missing GSAP); `initExitIntentModal()` (backdrop-click
  dismissal + the cosmetic submit handler); `initExitIntent()` (the detection
  logic above, opening after a short defer — see Positioning below), including
  the `#v5-back-to-articles` click listener and the `exitIntentPendingRedirect`
  variable `closeExitIntent()` reads to resume that navigation. Wired into the
  existing `DOMContentLoaded` handler as step 5.
- **`assets/css/theme-styles.css`** — `#exit-intent-modal` height cap (see
  Positioning below).
- **`single-blog.php`** — the existing "retour aux articles" link gained
  `id="v5-back-to-articles"` so the click listener above can target it; no
  other change (still a plain link, works with JS disabled).

## Positioning — appeared shifted up, only the bottom visible

Reported after testing on an actual article page (scrolled into the content,
with the WP admin bar visible — a logged-in editor's own view): the popup
rendered far too high, only its bottom sliver visible.

**First pass (insufficient):** initially suspected the mobile scroll trigger
opening mid-gesture while a mobile browser's address bar resizes the
viewport, and added a 220ms defer before opening plus a `max-height`
safety net. Both are reasonable and were kept, but neither was the actual
cause — the bug reproduced even from a plain desktop trigger with no address
bar involved.

**Root cause, found by reproducing faithfully instead of guessing further:**
built a test harness combining the real dialog markup, the real compiled
`tailwind.css`, the real `theme-scripts.js`, **real GSAP fetched from the same
CDN the site uses**, a simulated WP admin bar (`#wpadminbar` fixed + the
`html { margin-top }` push WP core applies), the theme's real sticky header,
and a scroll position 700px down the page — then A/B tested by removing one
variable at a time:

| Variant | Result |
| --- | --- |
| Full environment, GSAP loaded | **Broken** — dialog `top: -667px` (off-screen above the viewport) |
| Same, GSAP script removed entirely | Fixed — `top: 172px`, fully visible |
| Same, GSAP loaded, but manually set the exact same static end-state CSS GSAP produces (`transform`, `opacity`, `visibility`) instead of running the tween | Fixed — a static transform alone doesn't break it |

This isolated it to the **running GSAP tween itself** (not any static value it
produces) applied **directly to the `<dialog>` element**:
`animateModalOpen`/`animateModalClose` call `gsap.fromTo(modal, ...)` /
`gsap.to(modal, ...)`, animating `y`/`scale` (i.e. the same element's own
`transform`) on the dialog that is simultaneously relying on the browser's
native `dialog:modal { position:fixed; inset-block:0; margin:auto }`
top-layer centering. Something in that combination — GSAP's tween ticking via
`requestAnimationFrame` on a top-layer-promoted element — makes the browser
stop positioning the dialog via its native centering and fall back to normal
in-flow positioning, so it renders wherever it would sit in the *document*
(near the top, before the long article) rather than centered in the
*viewport* — which is exactly why the offset (`-667px`) so closely tracked
the 700px the page had been scrolled.

**Fix:** GSAP now animates a new inner wrapper (`#exit-intent-inner`, one
level inside the `<dialog>`), never the `<dialog>` element itself.
`openExitIntent()`/`closeExitIntent()` (`theme-scripts.js`) still call
`modal.showModal()`/`.close()` on the real dialog, but pass
`document.getElementById("exit-intent-inner")` to
`animateModalOpen`/`animateModalClose`. The dialog's own position is now
never touched by GSAP, so its native centering is never at risk. The
`max-height` CSS selector moved from `#exit-intent-modal > div:last-child` to
an explicit `#ei-body` id, since there's now an extra wrapper level in the
DOM.

**This same root cause equally affects `#search-modal` and `#matchmaker-modal`**
— both call `animateModalOpen(modal)`/`animateModalClose(modal, ...)` on
their own `<dialog>` element directly, the identical pattern that broke here.
It's a latent, currently-shipped bug: scroll deep into any page, then open
the search palette or the matchmaker wizard, and it should reproduce the same
way. Not fixed here — restructuring those two would mean editing their markup
and is a distinct, pre-existing issue outside what was asked for this popup —
flagging it for a follow-up decision rather than changing unrelated shipped
features silently.

**Verified:** the full A/B isolation above (5 headless-Chrome runs pinpointing
the exact cause), then re-ran the complete behavioral suite (8/8 still pass)
and re-confirmed the fix directly: same faithful environment (real GSAP, real
admin bar, real sticky header, scrolled 700px, dialog opened via
`openExitIntent()`) now measures `top: 172.6px, bottom: 531.3px, fullyVisible:
true`, with the `<dialog>` element itself carrying **zero** inline transform
(confirming GSAP no longer touches it) while the inner wrapper carries the
animation.

## Accessibility

Native `<dialog>` + `.showModal()` gives focus-trapping and Escape-to-close
for free (same as the existing search/matchmaker modals). Added explicit
`aria-labelledby`/`aria-describedby` (the existing modals don't have these —
added here since the roadmap called out accessibility specifically for this
item) plus an `aria-label` on the close button. Backdrop-click dismissal
(`click` on the `<dialog>` element itself, i.e. outside its content) is new —
neither existing modal has it, added here per the roadmap's explicit
requirement ("must be dismissible... backdrop click").

## Escape-to-close silently dropped the pending navigation (2026-07-09 fix)

"Escape-to-close for free" above was true of the *dialog* but not of this
popup's behaviour, and a later audit found the gap.

`initExitIntentModal()` only wired **click** on the dialog (backdrop). Escape
is handled by the browser itself: it fires a `cancel` event and then calls
`.close()` directly, never routing through `closeExitIntent()`. So the branch
in `closeExitIntent()` that resumes `exitIntentPendingRedirect` never ran.

**Failure:** a reader clicks "retour aux articles" (the click is intercepted,
the popup opens in its place), then dismisses the popup with **Escape** — the
affordance this very document advertises. The dialog closes, the pending
navigation is discarded, and the reader is silently stranded on the article.
Their deliberate click just vanished. (Clicking the link a second time works,
since the listener was already removed — but the first one is lost.) Escape
also skipped the closing GSAP animation.

**Fix:** a `cancel` listener on `#exit-intent-modal` calls `preventDefault()`
and routes through `closeExitIntent()`, so Escape now behaves exactly like the
✕ and the backdrop: same close animation, and the deferred navigation
completes.

Also registered `'Fermer'` (the close button's `aria-label`, via `v5_t()`) in
`v5_digital_ui_strings()` — it was being translated through Polylang but never
registered, so it silently stayed French in every language. The search modal's
`'Fermer la recherche'` was already registered; this matches it.

Files touched by the fix: `assets/js/theme-scripts.js`
(`initExitIntentModal()`), `functions.php` (`v5_digital_ui_strings()`).

## Verification performed

No PHP runtime on this machine — `functions.php`/`header.php`/`footer.php`
passed a structural balance check (PHP-mode/string/heredoc-aware delimiter
matching) after the edits.

The JS was executed in headless Chrome against the real dialog markup and the
real `theme-scripts.js`:

| Check | Result |
| --- | --- |
| Modal closed at page load | pass |
| Simulated desktop exit-intent (`mouseout`, `clientY<=0`, no `relatedTarget`) opens the modal | pass |
| `sessionStorage` flag set after first fire | pass |
| Backdrop click closes the modal | pass |
| Submit hides the form, shows the success state | pass |
| Submit sets the "subscribed" `localStorage` flag | pass |
| `exitIntentEnabled: false` blocks the popup entirely | pass |
| A visitor with the "subscribed" flag never sees it again | pass |
| Clicking "retour aux articles" is intercepted (`preventDefault`) and opens the popup | pass |
| Navigation stays deferred immediately after that click (nothing navigates yet) | pass |
| Closing the popup (✕ button) completes the original navigation | pass |
| Already-shown-this-session: the same click is **not** intercepted, navigates normally | pass |
| No uncaught JS errors | none |

Escape handling (added with the 2026-07-09 fix above; these were **not**
covered by the original suite, which is precisely why the bug shipped).

A synthetic `keydown` does **not** trigger `<dialog>`'s Escape default action,
so these can only be exercised with a *trusted* key event — dispatched here via
the Chrome DevTools Protocol (`Input.dispatchKeyEvent`) against the real
`theme-scripts.js`. GSAP is deliberately absent, so `canUseMotion()` is false
and `animateModalClose()` invokes its callback synchronously; the close *path*
is what's under test.

| Check | Result |
| --- | --- |
| Back-link click opens the popup, navigation still deferred | pass |
| Escape closes the popup | pass |
| Escape after "retour aux articles" completes the deferred navigation | pass |
| Escape with no pending redirect just closes, navigates nowhere | pass |
| ✕ button still closes and still completes the navigation | pass |
| No uncaught JS errors (both trigger paths) | none |

**Negative control** — the suite was re-run against a copy of
`theme-scripts.js` with only the `cancel` listener removed, to confirm it
actually detects the regression rather than passing vacuously. It fails there,
on exactly one row: *"Escape after 'retour aux articles' completes the deferred
navigation"* → the pending URL is dropped (`location.hash` stays `''`). Every
other row still passes, which is why the bug was invisible: Escape *looked*
like it worked.

(Positioning has its own dedicated verification above, including the A/B
isolation that found the GSAP/dialog conflict and the re-confirmation after
the inner-wrapper fix.)

Not verifiable here (no live WP/browser on this machine): the real mobile
rapid-scroll-up trigger on an actual touch device, and the visual result in
the real theme (brand colors, spacing, animation feel). Open any front-end
page, simulate the desktop trigger (move the cursor off the top of the
window), and confirm.

## How to revert

1. In `functions.php`, delete the "3b. EXIT-INTENT NEWSLETTER POPUP" section.
2. In `header.php`, remove the `exitIntentEnabled` line from
   `window.wpThemeSettings`.
3. In `footer.php`, delete the `#exit-intent-modal` `<dialog>` block.
4. In `assets/js/theme-scripts.js`, remove the "5. Exit-intent newsletter
   popup" step from `DOMContentLoaded` and delete `openExitIntent`,
   `closeExitIntent`, `initExitIntentModal`, `initExitIntent`, and the
   `exitIntentPendingRedirect` variable.
5. In `assets/css/theme-styles.css`, delete the `#exit-intent-modal` rules
   (right after the `dialog:focus-visible` rule).
6. In `single-blog.php`, remove `id="v5-back-to-articles"` from the "retour
   aux articles" link (harmless to leave, but unused once step 4 is done).
7. In `functions.php`, remove `'Fermer'` from `v5_digital_ui_strings()` (added
   by the 2026-07-09 Escape fix; harmless to leave — an unused registered
   string costs nothing).

Note the `cancel` listener in `initExitIntentModal()` disappears with step 4;
there is nothing separate to undo for the Escape fix.

Purely additive: no data touched, no Tailwind rebuild needed (no new
utility classes — all classes already used elsewhere in `footer.php`).
