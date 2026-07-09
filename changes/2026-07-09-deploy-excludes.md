# Deploy: stop shipping dev-only files to production (2026-07-09)

`.github/workflows/deploy1.yml` mirrored the whole repo to
`/wp-content/themes/agence-marketing-digital/` — build tooling, internal docs,
CI config and editor settings included. None of it is read at runtime.

## What changed

An `exclude:` list was added to **all three** FTP-deploy attempts (they are
separate retry steps against fresh runner IPs, so each needs its own copy —
they must be kept in sync):

| Excluded | Why |
| --- | --- |
| `.github/**`, `.vscode/**`, `.gitignore` | CI and editor config |
| `changes/**`, `docs/**` | internal changelogs and section audits |
| `src/**`, `tailwind.config.js`, `package.json`, `package-lock.json` | Tailwind build tooling — there is **no build step on the server**; only the compiled `assets/css/tailwind.css` is needed, and it is committed |
| `CLAUDE.md`, `README.md`, `ROADMAP.md`, `SITEMAP.md`, `STRUCTURED-DATA.md` | internal docs |
| `**/.git*`, `**/node_modules/**` | already the action's defaults; listed explicitly so the list is self-documenting |
| `.gitattributes` | repo config (keeps `*.mo` from being CRLF-mangled on Windows); nothing to do with the running theme |
| `languages/*.po`, `languages/*.pot` | translation *sources*; only the compiled `languages/en_US.mo` is read at runtime (added 2026-07-09, see `2026-07-09-admin-language.md`) |
| `assets/images/Screenshot*.png` | dev captures of the site, referenced nowhere (see "Loose end" below) |

Deliberately still deployed: `style.css` and `screenshot.png` (WordPress needs
both to register and display the theme), every PHP template, `acf-json/`,
`assets/`, and `languages/en_US.mo`.

## Verification

The workflow parses as valid YAML and all three deploy steps carry an identical
21-pattern list. The list was checked against the actual repo tree: every
non-runtime path is covered, every runtime path is left in.

## Loose end: the stray screenshot

`assets/images/Screenshot 2026-06-24 135345.png` (120 KB) is a capture of the
theme's own homepage hero. It is referenced **nowhere**: not in any `.php`,
`.js`, `.css`, `.json`, nor in the database (`wp_posts`, `wp_postmeta`,
`wp_options` all return 0 matches). It is not the theme's WordPress screenshot
either — that is `screenshot.png` at the repo root, which stays.

It **no longer deploys** (`assets/images/Screenshot*.png`), so it costs
production nothing.

It is still committed to the repo. It was added in `a5e37e6` by someone else, so
it has not been deleted here — that is a judgment call for whoever put it there.
It is tracked, so `git rm` would be recoverable from history if you want it gone.
