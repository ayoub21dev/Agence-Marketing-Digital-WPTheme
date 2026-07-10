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
`wp_head`), **sur chaque page rendue par le thème, y compris la 404 et les
archives de taxonomies**. Les valeurs suivent la configuration WordPress —
rien à modifier dans le code :

| Propriété     | Source                                              | Émis quand            |
| ------------- | --------------------------------------------------- | --------------------- |
| `@id`         | `/#organization` (référence stable)                  | toujours              |
| `name`        | Réglages → Général → Titre du site                   | toujours              |
| `url`         | URL du site                                          | toujours              |
| `email`       | `contact@<domaine>` (automatique)                    | toujours              |
| `description` | Réglages → Général → Slogan                          | **omis si vide**      |
| `logo`        | Logo personnalisé (Customizer)                       | **omis si absent**    |
| `sameAs`      | Site Settings → Social links (Twitter/X, LinkedIn,   | **omis si aucun lien**|
|               | Instagram, Facebook)                                 |                       |

> Sur l'installation locale actuelle, seuls `@id`, `name`, `url` et `email`
> apparaissent : le slogan, le logo et les réseaux sociaux ne sont pas encore
> renseignés. Ce n'est pas un bug — les trois clés sont volontairement omises
> plutôt qu'émises vides.

## ItemList — articles « classement »

Émis par `single-blog.php` uniquement quand l'article contient un bloc
**Analyses Éditoriales** (`agency_reviews_block`). Reflète exactement ce que
le lecteur voit :

| Propriété         | Source                                             |
| ----------------- | -------------------------------------------------- |
| `name` / `url`    | Titre et permalien de l'article                    |
| `numberOfItems`   | Nombre d'agences classées                          |
| `itemListElement` | Une entrée `ListItem` par agence                   |
| — `position`      | **Rang dans la liste : 1, 2, 3… consécutifs**      |
| — `name`          | Titre de la fiche agence                           |
| — `url`           | Site web **externe** de l'agence (si renseigné)    |

L'ordre de la liste suit le badge **RANK** de l'agence (tri croissant), mais
`position` n'est **pas** ce badge : c'est la place de l'élément *dans cette
liste*, 1-based et consécutive, comme l'exigent schema.org et Google.

> **Corrigé le 2026-07-10.** `position` valait auparavant le badge RANK :
> `/blog/seo-casablanca/` émettait un `ItemList` avec `numberOfItems: 1` et
> `position: 4`, un balisage invalide. `/blog/top-agencies/` passait seulement
> par chance (rangs 1 et 2). Voir `changes/2026-07-10-schema-itemlist-position.md`.

`url` pointe vers le **site web de l'agence**, jamais vers son permalien
WordPress : les CPT `agency` ne sont plus publiquement interrogeables et leurs
URL renvoient un 404 (cf. `SITEMAP.md`). Le balisage ne contient donc aucun lien
mort.

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
