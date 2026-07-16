# Changelog

Toutes les modifications notables du thème. Format
[Keep a Changelog](https://keepachangelog.com/fr/1.1.0/), versionnage
[SemVer](https://semver.org/lang/fr/).

**Ce fichier est généré.** Ne le modifiez pas à la main sous `## [Unreleased]` :
lancez `npm run release`, qui lit les commits et les fiches `changes/*.md`
depuis le dernier tag. Détails : `changes/2026-07-10-changelog-versioning.md`.

> La version affichée dans **Apparence → Thèmes** sur le site en production
> porte un suffixe de build (`1.0.0.57`) ajouté par le déploiement GitHub
> Actions. Le dépôt, lui, ne contient que la version sémantique propre.

## [Unreleased]

## [1.1.0] - 2026-07-16

### Added

- Changelog navigable : sommaire des versions + recherche/filtre — [`changes/2026-07-16-changelog-browse-and-search.md`](changes/2026-07-16-changelog-browse-and-search.md)
- Changelog et versionnage dynamiques — [`changes/2026-07-10-changelog-versioning.md`](changes/2026-07-10-changelog-versioning.md)
- Translate the whole ACF admin, dynamically — [`changes/2026-07-09-acf-admin-translation.md`](changes/2026-07-09-acf-admin-translation.md)
- Admin language: French backoffice + English for admins who need it — [`changes/2026-07-09-admin-language.md`](changes/2026-07-09-admin-language.md)

### Changed

- Habillage visuel de l'écran « Nouveautés du thème » — [`changes/2026-07-16-changelog-admin-styling.md`](changes/2026-07-16-changelog-admin-styling.md)
- Structured data (JSON-LD) désactivé — [`changes/2026-07-10-schema-disabled.md`](changes/2026-07-10-schema-disabled.md)

### Fixed

- Audit fixes: the remaining High severity findings — [`changes/2026-07-09-audit-high-fixes.md`](changes/2026-07-09-audit-high-fixes.md)
- Audit fixes: all Medium and Low severity findings — [`changes/2026-07-09-audit-medium-low-fixes.md`](changes/2026-07-09-audit-medium-low-fixes.md)
- Final audit: re-review the whole session's diff, fix what it found — [`changes/2026-07-09-final-audit-fixes.md`](changes/2026-07-09-final-audit-fixes.md)
- Fix: search/matchmaker modal centering, JS i18n bridge, subtitle mismatch — [`changes/2026-07-09-modal-centering-and-i18n-bridge.md`](changes/2026-07-09-modal-centering-and-i18n-bridge.md)
- Fix: blank white screen on Appearance → Patterns/Fonts — [`changes/2026-07-09-theme-json-fonts-colors-fix.md`](changes/2026-07-09-theme-json-fonts-colors-fix.md)
- Accorder le tampon de déploiement et le versionnage sémantique — [`changes/2026-07-10-version-stamp-alignment.md`](changes/2026-07-10-version-stamp-alignment.md)
- ItemList: `position` was the RANK badge, not the list position — [`changes/2026-07-10-schema-itemlist-position.md`](changes/2026-07-10-schema-itemlist-position.md)
- CPT URLs, `/blog/` prefix, and crawlability — [`changes/2026-07-10-cpt-urls-and-crawlability.md`](changes/2026-07-10-cpt-urls-and-crawlability.md)

### Security

- Fix: icon_svg stored XSS — sanitize, don't remove the feature — [`changes/2026-07-09-icon-svg-xss-fix.md`](changes/2026-07-09-icon-svg-xss-fix.md)


## [1.0.0] - 2026-07-09

Première version balisée. Reprend l'état déployé en production : le thème
existait déjà, mais sans changelog ni tag — ce point de départ les ancre.

### Added

- Native featured images enabled — [`changes/2026-07-07-featured-image.md`](changes/2026-07-07-featured-image.md)
- Structured data: Organization + ItemList — [`changes/2026-07-07-structured-data.md`](changes/2026-07-07-structured-data.md)
- Exit-intent newsletter popup — [`changes/2026-07-08-exit-intent-popup.md`](changes/2026-07-08-exit-intent-popup.md)
- Page builder: live section preview via iframe — [`changes/2026-07-08-section-preview.md`](changes/2026-07-08-section-preview.md)
- Page builder: per-page section groups, "utilisée" badge, modal category chip — [`changes/2026-07-09-page-builder-section-groups.md`](changes/2026-07-09-page-builder-section-groups.md)

### Changed

- Article page redesign — image hero card — [`changes/2026-07-07-article-hero.md`](changes/2026-07-07-article-hero.md)
- Blog permalinks moved under /blog/ — [`changes/2026-07-07-blog-permalinks.md`](changes/2026-07-07-blog-permalinks.md)
- XML sitemap rewrite — [`changes/2026-07-07-sitemap.md`](changes/2026-07-07-sitemap.md)
- Deploy: stop shipping dev-only files to production — [`changes/2026-07-09-deploy-excludes.md`](changes/2026-07-09-deploy-excludes.md)

[Unreleased]: https://github.com/ayoub21dev/Agence-Marketing-Digital-WPTheme/compare/v1.1.0...HEAD
[1.0.0]: https://github.com/ayoub21dev/Agence-Marketing-Digital-WPTheme/releases/tag/v1.0.0
[1.1.0]: https://github.com/ayoub21dev/Agence-Marketing-Digital-WPTheme/compare/v1.0.0...v1.1.0
