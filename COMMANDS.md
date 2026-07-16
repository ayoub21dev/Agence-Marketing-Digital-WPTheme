# Commands

Everything you need to run day-to-day on this project, from your own terminal.
None of this is deployed — see `.github/workflows/deploy1.yml`'s exclusion list.

---

## 1. Tailwind (CSS build)

Tailwind is **compiled and committed** — there's no build step on the server.
If you add/change Tailwind classes in any `.php` file, rebuild before committing:

```powershell
npm run build   # one-shot, minified — do this before every commit that touches classes
npm run watch   # rebuilds continuously while you edit — use during a work session
```

Output: `assets/css/tailwind.css`. Source: `src/tailwind.css`.

---

## 2. Releasing a new version

### 2.0 In plain terms

Think of the version number like a draft number on a document — "Draft 1,"
"Draft 2," and so on. Once you've made enough changes worth remembering, you
stamp a new draft number and write down what changed since the last one. A
small program does that stamping and note-writing for you, so you don't have
to do it by hand.

Every single time, in this order:

1. **Make sure your changes are saved in git first** ("committed"). Not sure?
   Ask first — check before you commit.
2. **Look before you leap:**
   ```powershell
   npm run release:dry
   ```
   100% safe — changes nothing. Just a preview: "here's the number I'd pick,
   here's what I'd write down." Read it, nothing happens yet.
3. **If it looks right, do it for real:**
   ```powershell
   npm run release
   ```
   Now it stamps the new number and writes the notes. **Still nothing goes
   onto the live website at this point** — it's all sitting on your computer.
4. **Only when you decide to actually publish it**, separately run:
   ```powershell
   git push --follow-tags origin main
   ```
   This is the one and only step that puts it on the live site.

Steps 2–3 are *preparing*. Step 4 is *publishing*. They're kept separate on
purpose so nothing goes live by accident.

The rest of this section goes into more technical detail, for reference —
skip it unless you want the "why" behind the above.

### 2.1 The one thing to understand first

There are two copies of the version number, owned by two different tools,
that never touch each other:

- **The repo** — `style.css`'s `Version:` header (mirrored in `package.json`)
  is always a clean 3-part semver, e.g. `1.1.0`. Only `npm run release`
  writes it.
- **The deployed copy** — `deploy1.yml` appends `.{github.run_number}` to
  `style.css` on the GitHub Actions runner, right before FTP-ing it to
  Hostinger (e.g. `1.1.0.62`). That stamped file is never committed back to
  git — it exists only on the live server.

That's why `v5_digital_theme_version()` in `functions.php` reads the
*installed* header at runtime instead of a hardcoded constant: on your
machine it correctly says `1.1.0`, on production it correctly says `1.1.0.62`.
`npm run release` only ever owns the first half, so it can't conflict with
what the deploy does to the second.

### 2.2 How the bump level (major/minor/patch) gets decided

`bin/release.mjs` looks at every commit since the last git tag
(`git describe --tags --abbrev=0`) and reads each one as a Conventional
Commit:

| Commit looks like | Bump |
|---|---|
| `type!: ...`, or a `BREAKING CHANGE:` footer | **major** |
| `feat: ...` | **minor** |
| anything else conventional (`fix:`, `docs:`, `chore:`, ...) | **patch** |

If **any** commit in that range doesn't parse as `type: subject` at all, it
refuses outright instead of guessing — a missed `feat:` would otherwise ship
silently as a patch. Your options when that happens: pass `--bump <level>`
yourself, pass `--allow-unconventional` (counts the unparseable ones as
patches), or go reword the offending commit messages.

### 2.3 The commands

```powershell
npm run release:dry                          # preview only — writes nothing, commits nothing, tags nothing. Always run this first.
npm run release                              # cut it for real: writes CHANGELOG.md/style.css/package.json, commits, tags
npm run release -- --bump patch              # force the level instead of auto-detecting
npm run release -- --bump minor
npm run release -- --bump major
npm run release -- --allow-unconventional    # treat any non-Conventional commit as a patch instead of refusing
npm run changelog:check                      # = npm run release -- --check: lists commits that touched code but added no changes/ doc
```

Two hard refusals worth knowing about, both by design:
- **Dirty working tree** — a real run (not `--dry-run`) dies immediately if
  `git status` isn't clean. Commit everything first.
- **Tag already exists** — refuses if the computed next version's tag is
  already there, so you can't accidentally re-release the same number.

### 2.4 What one release actually writes

For a run that bumps `1.0.0` → `1.1.0`:

1. **`CHANGELOG.md`** — inserts `## [1.1.0] - <today>` directly under
   `## [Unreleased]`, built from every `changes/*.md` file **added** (not
   merely edited) since the last tag. Each file's `# H1`, trailing `(date)`
   stripped, becomes one bullet. Also rewrites the compare-link references at
   the bottom of the file.
2. **`style.css`** — `Version:` header → `1.1.0`.
3. **`package.json`** — `"version"` → `"1.1.0"`.
4. `git add` exactly those three files, then
   `git commit -m "chore(release): v1.1.0"`.
5. `git tag -a v1.1.0 -m v1.1.0`.
6. Prints the `git push --follow-tags ...` command — but does **not** run it.

### 2.5 Where a changelog entry's category comes from

Each `changes/*.md` doc contributes exactly one bullet, filed under a Keep a
Changelog section (Added/Changed/Fixed/Performance/Removed/Documentation/
Security), decided in this order:

1. An explicit `<!-- changelog: Fixed -->` marker in the doc's first ~10
   lines — wins over everything else.
2. Otherwise, the type of the commit that **added** the file (`feat`→Added,
   `fix`→Fixed, `perf`→Performance, `docs`→Documentation, `revert`→Removed).
3. Otherwise, falls back to **Changed**.

Add the marker whenever step 2 would lie — e.g. a security fix added by a
commit that isn't literally typed `fix:`, which would otherwise land under
whatever generic type wraps it. Every file under `changes/` already follows
this pattern; copy it.

### 2.6 A real example (this repo)

`npm run release:dry -- --bump minor` — preview only:
```
  repo    ...\Agence-Marketing-Digital-WPTheme
  branch  feature/wp-admin-i18n
  since   v1.0.0

  11 commit(s), 15 changes/ doc(s)
  bump    minor  (--bump minor)
  version 1.0.0 -> 1.1.0

  │ ## [1.1.0] - 2026-07-16
  │
  │ ### Added
  │ - ...
  │ ### Changed
  │ - ...
  │ ### Fixed
  │ - ...
  │ ### Security
  │ - ...

  ! --dry-run: nothing written, nothing committed, nothing tagged.
```

The same command **without** `--dry-run`, for real:
```
  ✔ CHANGELOG.md
  ✔ style.css        Version: 1.1.0
  ✔ package.json     "version": "1.1.0"
  ✔ commit  chore(release): v1.1.0
  ✔ tag     v1.1.0

  Not pushed. Pushing `main` deploys to production. When you are ready:

      git push --follow-tags origin feature/wp-admin-i18n
```

### 2.7 Rules of thumb

- Always `release:dry` before the real thing — reading the preview is free.
- Never hand-edit `CHANGELOG.md` under `## [Unreleased]` — that region is the
  generator's.
- Don't manually bump `style.css`/`package.json` outside of `npm run release`
  "just to test something." If you ever do, revert them first —
  `git restore style.css package.json CHANGELOG.md` — before the next real
  release, or the tool computes the wrong "current" version to bump from.
- One `changes/*.md` doc per user-visible change. `npm run changelog:check`
  catches commits that touched code but forgot one.

---

## 3. Git — pushing a release

None of the above pushes anything. Once you're ready:

```powershell
git push --follow-tags origin <branch>   # --follow-tags is required, or the new tag never reaches GitHub
```

**⚠️ Pushing to `main` triggers `deploy1.yml` — a live production FTP deploy,
no staging.** Merge/push to `main` only when you mean it.

To check what's really on the remote (not just your last `fetch`):
```powershell
git ls-remote --heads origin <branch>
```

---

## 4. Local WordPress (Studio)

This site runs on **WordPress Studio** (SQLite, no MySQL). Bare `wp` CLI does
**not** work here — everything goes through the `studio` wrapper.

**First time only** — if `studio` isn't recognized in your terminal: open the
WordPress Studio desktop app → **Settings → Studio CLI for terminal** → enable
the toggle, then open a *new* terminal window.

```powershell
studio site status          # get the current URL, admin credentials, PHP/WP versions (port changes each run — never hardcode it)
studio site start --skip-browser
studio site stop
studio site set --php 8.4
studio wp <any wp-cli command>     # e.g. studio wp plugin list --status=active
studio wp eval 'echo "OK";'        # quick PHP sanity check — studio wp shell is NOT supported
studio wp db query "SELECT option_name, option_value FROM wp_options LIMIT 10;"
```

Menus, pages, posts, and images live in the site's database — they do **not**
travel with `git`. A fresh clone needs its own content seeded/recreated.

---

## 5. PHP syntax check (optional)

A real PHP CLI ships bundled with WordPress Studio — useful for a fast syntax
check without loading the whole site:

```powershell
& "C:\Users\Anouar BENYEKHLEF\.studio\php-bin\8.4.21\php.exe" -l functions.php
```

(Version folder name may change after a Studio update — check
`C:\Users\Anouar BENYEKHLEF\.studio\php-bin\` if this path stops working.)

---

## 6. If `node`/`npm` aren't recognized

This has happened before on this machine: Node.js gets installed, but a
terminal that was **already open** before the install won't see the updated
`PATH` — closing one tab and opening another inside the same app (e.g. VS
Code's integrated terminal) often isn't enough, since both are children of the
same already-running process. Two fixes, in order of preference:

1. Fully quit and reopen the whole terminal application (not just a tab).
2. Or skip `PATH` entirely and call it directly:
   ```powershell
   & "C:\Program Files\nodejs\node.exe" -v
   & "C:\Program Files\nodejs\npm.cmd" run release:dry
   ```
