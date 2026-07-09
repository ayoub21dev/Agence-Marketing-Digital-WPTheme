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

Deliberately still deployed: `style.css` and `screenshot.png` (WordPress needs
both to register and display the theme), every PHP template, `acf-json/`, and
`assets/`.

## Verification

The workflow parses as valid YAML and all three deploy steps carry an identical
17-pattern list. The list was checked against the actual repo tree (83 files):
every non-runtime path is covered, every runtime path is left in.

## Loose end (not fixed)

`assets/images/Screenshot 2026-06-24 135345.png` (120 KB) is referenced nowhere
in any `.php`, `.js`, `.css` or `.json` — it looks like an accidental commit. It
still deploys, because the exclude list does not carve holes inside `assets/`.
Either delete it from the repo or add it to the list.
