# Données structurées — agencemarketingdigital.com

Carte des schémas JSON-LD (schema.org) émis par le thème. Complément de
`SITEMAP.md`. Détail de l'implémentation et retour arrière :
`changes/2026-07-07-structured-data.md`.

---

## Vue d'ensemble

```
Toutes les pages
└── Organization              qui publie le site (émis dans <head>)

Articles avec classement d'agences (« Analyses Éditoriales »)
└── ItemList                  le classement, une entrée par agence
```

## Organization — toutes les pages

Émis par `v5_digital_organization_schema()` dans `functions.php` (hook
`wp_head`). Les valeurs suivent la configuration WordPress — rien à modifier
dans le code :

| Propriété     | Source                                              |
| ------------- | --------------------------------------------------- |
| `name`        | Réglages → Général → Titre du site                  |
| `description` | Réglages → Général → Slogan                         |
| `url`         | URL du site                                         |
| `email`       | `contact@<domaine>` (automatique)                   |
| `logo`        | Logo personnalisé (Customizer) — omis tant qu'aucun |
| `@id`         | `/#organization` (référence stable)                 |

## ItemList — articles « classement »

Émis par `single-blog.php` uniquement quand l'article contient un bloc
**Analyses Éditoriales** (`agency_reviews_block`). Reflète exactement ce que
le lecteur voit :

| Propriété         | Source                                             |
| ----------------- | -------------------------------------------------- |
| `name` / `url`    | Titre et permalien de l'article                    |
| `numberOfItems`   | Nombre d'agences classées                          |
| `itemListElement` | Une entrée `ListItem` par agence                   |
| — `position`      | Le badge RANK de l'agence (sinon l'ordre du bloc)  |
| — `name`          | Titre de la fiche agence                           |
| — `url`           | Site web de l'agence (si renseigné)                |

Un article sans bloc de classement n'émet **aucun** ItemList (pas de schéma
vide ou trompeur).

## Vérification

1. Afficher le code source d'un article classement
   (ex. `/blog/top-agencies/`) → deux blocs
   `<script type="application/ld+json">`.
2. Après déploiement : tester l'URL dans le
   [Rich Results Test](https://search.google.com/test/rich-results) de Google
   ou sur [validator.schema.org](https://validator.schema.org/).

## Pas encore implémenté (pistes)

- **BlogPosting** sur les articles (headline, datePublished, author, image) —
  un brouillon existe dans le stash git « AMD engagement work ».
- **BreadcrumbList** — le fil d'ariane des articles existe visuellement,
  il pourrait être décrit en schéma.
- **WebSite + SearchAction** — pour la palette de recherche Ctrl/Cmd+K.
- **AggregateRating** sur les agences — les fiches ont déjà `rating_value`
  et `review_count` en ACF ; à n'ajouter que si les avis respectent les
  consignes Google (avis authentiques, visibles sur la page).
