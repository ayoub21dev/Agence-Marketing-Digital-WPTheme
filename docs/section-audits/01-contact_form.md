# 01 — contact_form_section

**Label:** `[Contact] Formulaire — contact & infos` · **PHP:** `template-parts/layouts/contact_form.php` · **JSON:** `acf-json/group_homepage_fields.json:1755-1938`
**Used on:** page "Contact" (ID 7), as 2nd section after `common_hero_section` *(verified via WP-CLI)*

> **STATUS: ALL 10 FINDINGS FIXED** (see the `fix(contact)` commit following this audit).
> F1/F2/F3/F8 — office/email page sub-fields deleted; Site Settings is the single source (empty = hidden; email always resolves via `v5_digital_get_dynamic_email()`). F4 — `class_exists('AMD_CF_Form')` guard + mailto fallback card. F5 — default Lucide icons (map-pin/mail) + truthful instructions. F6 — `max:1` on the layout. F7 — all visitor strings via `v5_t()` + registered. F9 — editable `success_title`/`success_desc` sub-fields. F10 — subject options rendered from `AMD_CF_Handler::subject_labels()`.
> Verified on the rendered page: 0 occurrences of "rue de la Paix", mailto = Site Settings email, both default icons present. Line numbers below refer to the pre-fix code.

## 1. What this section does

Renders a two-column contact block: left, a hand-built contact form (submitted through the AMD Contact Forms plugin under the form named `default`); right, a sidebar with the office address, the contact email, and an "independence guarantee" card. Address/email pull from **Site Settings first**, then the page's own fields, then hardcoded defaults.

## 2. Fields

| Field | Type | Required | Default | Read by PHP? | Instruction? |
|---|---|---|---|---|---|
| `form_title` | text | no | "Envoyer un Message" | ✅ :5 | ❌ |
| `form_desc` | textarea | no | long FR text | ✅ :6 | ❌ |
| `office_icon` | image (url) | no | — | ✅ :7 | ✅ (but wrong — see F5) |
| `office_title` | text | no | "Siège Social" | ✅ :11 (2nd priority) | ❌ |
| `office_address` | text | no | **"8 rue de la Paix, 75002 Paris, France"** | ✅ :12 (2nd priority) | ❌ |
| `office_city` | text | no | "Casablanca, Maroc" | ✅ :13 (2nd priority) | ❌ |
| `email_icon` | image (url) | no | — | ✅ :14 | ✅ (but wrong — see F5) |
| `email` | **text** (not email) | no | "contact@agencemarketingdigital.com" | ✅ :15 (2nd priority) | ❌ |
| `guarantee_title` | text | no | "Garantie d'Indépendance" | ✅ :20 | ❌ |
| `guarantee_desc` | textarea | no | long FR text | ✅ :21 | ❌ |

**External reads (not in this layout):** `contact_email`, `office_title`, `office_address`, `office_city` from **Site Settings** (`'option'`, `group_site_settings.json`) — these WIN over the page fields (`contact_form.php:11-15`). The form itself depends on the **AMD Contact Forms plugin** (`form_id=default` hardcoded at :76, JS handler from the plugin).

## 3. Use-case matrix

| # | Editor sets… | Result on the page | OK? |
|---|---|---|---|
| 1 | Nothing (fresh section, no options) | Full section with defaults — **including the fake Paris address next to "Casablanca, Maroc"** | ❌ F1 |
| 2 | Fills Site Settings email; page field still has old value | Option wins, page value ignored (by design after the email bug) | ✅ |
| 3 | Edits the **page's** email/office fields while Site Settings is filled | **Nothing changes.** No warning anywhere | ❌ F2 |
| 4 | Clears office_title + address + city (page AND options) to hide the address block | **Impossible** — hardcoded defaults resurrect; the block can never be hidden | ❌ F3 |
| 5 | Clears `email` everywhere to hide the email row | Impossible — `v5_digital_get_dynamic_email()` always produces `contact@<domain>` | ❌ F3 |
| 6 | Clears `form_title` / `form_desc` | Correctly hidden (saved `""` passes through) — **opposite behavior** from #4/#5 | ⚠️ F3 |
| 7 | Leaves `office_icon` empty | **No icon at all** — instruction promises a "default icon" that doesn't exist | ❌ F5 |
| 8 | Uploads an icon | Icon shows | ✅ |
| 9 | Clears `guarantee_title` + `guarantee_desc` | Whole guarantee card correctly disappears (:180) | ✅ |
| 10 | Adds the section **twice** on one page (allowed — no `max:1`) | Duplicate HTML IDs; the 2nd form submits but its success/error message appears **inside the 1st copy** | ❌ F6 |
| 11 | AMD Contact Forms plugin deactivated | Form still renders; submit does a GET reload — **message silently lost**, no error shown | ❌ F4 |
| 12 | Types a non-email in the `email` field (type is `text`) | Broken `mailto:` link rendered as-is | ❌ F8 |
| 13 | Very long office_address / form_title | Wraps; layout holds (`break-all` on email link) | ✅ |
| 14 | Switches site language (Polylang) | Form labels, subject options, success/error messages stay French — zero `v5_t()` in the file | ❌ F7 |

## 4. Findings

### F1 — Live contradictory fake address (Paris street + Casablanca city) [HIGH]
- **What happens:** with Site Settings address/city empty (their current state — verified), the seeded page values render: "Siège Social / 8 rue de la Paix, 75002 Paris, France / Casablanca, Maroc".
- **Why it's wrong:** a fake foreign street address is publicly displayed on a Morocco-focused site, followed by a contradicting city line. **This is on the local site right now.**
- **Reproduce:** open `/contact/`, look at the sidebar.
- **Evidence:** JSON defaults `group_homepage_fields.json:1837` (Paris) vs `:1854` (Casablanca); seeded meta verified via WP-CLI (`page_layouts_1_office_address = '8 rue de la Paix…'`); render `contact_form.php:154-158`.
- **Possible fix (not applied):** empty the `office_address` default in JSON + fix the seeder values; or drop the page-level office fields entirely (Site Settings only).

### F2 — Page-level contact fields are silently dead when Site Settings is filled [HIGH]
- **What happens:** the option wins by design (`option → page → default`), but the page fields remain visible and editable in the builder. Editing them does nothing, with no hint.
- **Why it's wrong:** identical editor experience to the original email bug, just mirrored: "I changed it and nothing happened."
- **Reproduce:** fill Site Settings → Contact email; edit the Contact page's "Adresse Email de contact" field; save; front-end unchanged.
- **Evidence:** `contact_form.php:11-15` (precedence comment at :8-10); options verified filled (`options_contact_email = ayoubdida@gmail.com`) while page meta holds stale `contactAMD@localhost`.
- **Possible fix (not applied):** remove the 4 duplicated page sub-fields from the layout (one source of truth), or an admin JS note on those fields: "overridden by Site Settings while it's filled".

### F3 — The address block and email row can never be hidden; clearing behavior is inconsistent [HIGH]
- **What happens:** the `?:` chains turn a deliberately cleared field back into the hardcoded default (`'' ?: 'Siège Social'`), and the email always falls back to generated `contact@<domain>`. So `$has_address`/`$has_email` are always truthy and the guard at :140 can never be false. Meanwhile `form_title`/`form_desc`/guarantee CAN be hidden by clearing (their `v5_get_field_default` returns saved `""` as-is).
- **Why it's wrong:** an editor who wants to remove the (fake) address has no way to do it — clearing every field resurrects "8 rue de la Paix". And the same "clear the field" gesture hides some blocks but resurrects others; the editor can't build a mental model.
- **Reproduce:** clear office title/address/city on the page AND in Site Settings → save → the Paris default reappears.
- **Evidence:** `contact_form.php:11-15` (`?:` chains), `:138-140` (dead guard); wrapper semantics `functions.php:4194-4204` (returns saved `""`, but `?:` then discards it).
- **Possible fix (not applied):** drop the final hardcoded tier for office fields (empty = hidden), keeping defaults only as ACF `default_value` for new rows.

### F4 — Form renders but is silently dead without the AMD Contact Forms plugin [HIGH]
- **What happens:** the `<form>` has no `action` (:67); submission is handled entirely by the plugin's `contact-form.js`. With the plugin inactive, submit performs a native GET reload — the visitor's message is lost with no error. The sibling `form_section` layout early-returns in this case; this one doesn't check at all.
- **Why it's wrong:** theme and plugin deploy from **separate repos to separate directories** — a live site can genuinely have one without the other, and the failure is invisible (page looks perfect).
- **Reproduce:** deactivate AMD Contact Forms locally, open `/contact/`, submit the form → page reloads with fields in the URL, nothing sent, no feedback.
- **Evidence:** `contact_form.php:67` (no action), `:203` (comment: plugin handles submission); binding in `amd-contact-forms/assets/contact-form.js:125`.
- **Possible fix (not applied):** guard the form column on `class_exists('AMD_CF_Form')` with a fallback `mailto:` block, mirroring `form.php`.

### F5 — Icon instructions promise a "default icon" that doesn't exist [MEDIUM]
- **What happens:** both icon fields say *"Laissez vide pour utiliser l'icône par défaut"*, but the PHP renders an icon **only when one is set** — there is no default icon path.
- **Why it's wrong:** the editor leaves it empty expecting a default and gets nothing; verified the live page currently renders the address block with no icon (`page_layouts_1_office_icon = ''`).
- **Reproduce:** leave "Icône Adresse" empty → no icon renders.
- **Evidence:** instruction `group_homepage_fields.json:1800` and `:1871`; render guard `contact_form.php:147-149`, `:166-168`.
- **Possible fix (not applied):** fix the instruction text, or actually ship a default Lucide icon in the `else` branch.

### F6 — Duplicated section: second form's feedback appears inside the first copy [MEDIUM]
- **What happens:** no `max:1`, so the section can be added twice (systemic S1). Both copies emit the same HTML IDs (`#contact-form`, `#contact-success-msg`, `#contact-error-msg`, `#contact-submit-btn`) — invalid HTML. The plugin JS binds every `#contact-form` (`querySelectorAll`, contact-form.js:125) but resolves the feedback elements with `getElementById` (:22-24), which always returns the **first** match: submitting form 2 shows its success/error message in section 1, possibly off-screen. The inline `<style>` block (:24-51) is also printed once per copy.
- **Why it's wrong:** the generic amber duplicate warning exists but is dismissible and doesn't say the second form will misbehave.
- **Reproduce:** add `contact_form_section` twice, submit the lower form, watch the upper section for the success message.
- **Evidence:** `contact_form.php:67,122,131`; `amd-contact-forms/assets/contact-form.js:22-25,125`; no `max` on the layout (`group_homepage_fields.json:1755`).
- **Possible fix (not applied):** `max:1` on this layout (pattern already used in `group_blog_content.json`).

### F7 — Entire form UI untranslatable (zero `v5_t()`) [MEDIUM]
- **What happens:** every visitor-facing string — field labels, placeholders, the 4 subject options, the submit button, the success/error messages, "Informations de contact", "Email" — is hardcoded French. The file contains no `v5_t()` call, while header/footer/search UI strings all route through it.
- **Reproduce:** switch Polylang to EN → the whole contact form stays French.
- **Evidence:** `contact_form.php:84-131` (form strings), `:143` ("Informations de contact"), `:170` ("Email"); compare `footer.php:150` for the established pattern.
- **Possible fix (not applied):** wrap the strings in `v5_t()` + register them in `v5_digital_ui_strings()`.

### F8 — `email` sub-field is type `text`, not `email` [LOW]
- **What happens:** no format validation; a typo renders a broken `mailto:` link. The equivalent Site Settings field IS type `email` — inconsistent.
- **Evidence:** `group_homepage_fields.json:1890` vs `group_site_settings.json` (`field_site_contact_email`, type email).
- **Possible fix (not applied):** change field type to `email`.

### F9 — Hardcoded reply-time promise in the success message [LOW]
- **What happens:** "Notre équipe éditoriale vous contactera sous 24 heures." is not editable; the editor can't change or remove a service-level promise the business must honor.
- **Evidence:** `contact_form.php:128`.
- **Possible fix (not applied):** expose as a sub-field with the current text as default.

### F10 — Subject dropdown choices hardcoded [LOW]
- **What happens:** the 4 subject options (:101-104) can't be edited without code. Fine today, but the editor has no way to add e.g. "Demande de devis".
- **Evidence:** `contact_form.php:100-105`.
- **Possible fix (not applied):** optional repeater sub-field with these 4 as defaults.

## 5. Editor-UX suggestions (not implemented)

1. **One source of truth for contact info** — delete the 4 duplicated page sub-fields; the section reads Site Settings only. Kills F2 and half of F3 structurally.
2. **`max:1` on this layout** — a page never needs two contact forms; the blog builder already uses this pattern.
3. **Admin note on the office/email fields** (while they exist): reuse the existing advisor pattern (`functions.php:415`) to show "Overridden by Site Settings while it's filled" on those fields.
4. **Truthful icon instructions** — smallest possible fix with real editor impact.
5. **A `message` field at the top of the layout** saying where the form submissions go ("Submissions → Formulaire de contact") — the editor currently has no way to know.

## 6. Verdict — 10 findings (0 blocker · 4 high · 3 medium · 3 low)

The section renders robustly (all output escaped, no crash paths found), but it concentrates the theme's worst *editor* traps: a live fake address (F1), a dead-field trap identical to the email bug (F2), an unhideable block (F3), and a hard runtime dependency on a separately-deployed plugin with no guard (F4). Most fixes are small; F2+F3 are best solved together by making Site Settings the only source for contact info.
