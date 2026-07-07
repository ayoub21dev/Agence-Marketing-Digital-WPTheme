# Sitemap — agencemarketingdigital.com

Human-readable map of the site's URL structure. The machine version for
search engines is generated dynamically at
**https://agencemarketingdigital.com/sitemap.xml** (see
`changes/2026-07-07-sitemap.md` for how it's built).

> Content snapshot taken from the live site on **2026-07-07**. Pages and
> agencies listed here evolve in the WordPress admin; the XML sitemap picks
> changes up automatically, this file does not.

---

## Structure

```
agencemarketingdigital.com/
├── /                        Accueil
├── /blog/                   Liste des articles (filtres de catégorie intégrés)
│   └── /<slug-article>/     Article (template single-blog.php)
├── /about/                  À propos
├── /methodologie/           Méthodologie
├── /contact/                Contact (formulaire AMD Contact Forms)
└── /sitemap.xml             Sitemap XML dynamique (+ ligne Sitemap: dans robots.txt)
```

## Pages principales

| URL                | Page                 | Priorité sitemap |
| ------------------ | -------------------- | ----------------- |
| `/`              | Accueil              | 1.0 · daily      |
| `/blog/`         | Blog                 | 0.9 · daily      |
| `/about/`        | À propos            | 0.7 · monthly    |
| `/methodologie/` | Méthodologie        | 0.7 · monthly    |
| `/contact/`      | Contact              | 0.7 · monthly    |

## Articles de blog (snapshot 2026-07-07)

Depuis le 2026-07-07 les articles vivent sous `/blog/…` (structure de
permaliens `/blog/%postname%/`). Les anciennes URL racine font une
redirection 301 vers la nouvelle adresse.

| URL                              | Note                                           |
| -------------------------------- | ---------------------------------------------- |
| `/blog/top-agencies/`          | Top Agences de Marketing Digital au Maroc      |
| `/blog/seo-casablanca/`        | SEO à Casablanca                              |
| `/blog/social-media-compared/` | Les Agences Social Media sous le prisme du SEO |
| `/blog/test/`                  | ⚠️ contenu de test — à supprimer           |
| `/blog/tesing-two/`            | ⚠️ contenu de test — à supprimer           |
| `/blog/another-test/`          | ⚠️ contenu de test — à supprimer           |

## Volontairement hors sitemap XML

- **Spécialités** (`/specialty_hub/seo-growth/`, `/marque-reseaux/`,
  `/web-conversion/`) — le site ne lie jamais leurs permaliens directement ;
  ce sont des pages orphelines. *(L'ancien sitemap les listait — retirées le
  2026-07-07.)*
- **CPT internes** (`stat_metric`, `testimonial`, `partner_logo`) — blocs de
  contenu affichés dans les sections, pas des pages.
- **Archives de catégories** (`/category/<slug>/`) — pas de template dédié
  dans le thème (rendu par le fallback `index.php`) et jamais liées : le blog
  filtre ses catégories côté client. À ajouter si un template `category.php`
  est créé un jour.
- **Archives taxonomies** (`/service/<slug>/`, `/city/<slug>/`) — pas de
  template dédié dans le thème. À ajouter au sitemap si un template
  d'archive est créé un jour.
- `/wp-sitemap.xml` (sitemap natif WordPress) — désactivé pour éviter deux
  sitemaps concurrents.

## À faire (SEO)

- [ ] Supprimer les articles de test (`/blog/test/`, `/blog/tesing-two/`,
  `/blog/another-test/`) encore publiés sur le site live.
- [ ] Après le prochain déploiement, soumettre le sitemap dans Google Search
  Console : `https://agencemarketingdigital.com/sitemap.xml`.
- [ ] Vérifier qu'aucun `robots.txt` physique n'existe sur Hostinger (sinon y
  ajouter manuellement la ligne `Sitemap:`).
