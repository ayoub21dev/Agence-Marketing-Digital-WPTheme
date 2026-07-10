# Données structurées — agencemarketingdigital.com

> ## ⛔ DÉSACTIVÉ — 2026-07-10
>
> **Le thème n'émet plus aucun JSON-LD.** Ni `Organization`, ni `ItemList`, sur
> aucune page. Désactivé à la demande, en attendant le feu vert.
>
> Le code n'est **pas supprimé** : il est derrière un interrupteur unique,
> `v5_digital_schema_enabled()` ([functions.php](functions.php)), qui renvoie
> `false` par défaut.
>
> **Pour réactiver**, au choix :
>
> ```php
> // 1. Sans toucher au thème (mu-plugin, plugin, functions.php enfant) :
> add_filter('v5_digital_schema_enabled', '__return_true');
>
> // 2. Ou basculer la valeur par défaut dans functions.php :
> return (bool) apply_filters('v5_digital_schema_enabled', true);
> ```
>
> Rien d'autre à modifier : les deux émetteurs (`v5_digital_organization_schema()`
> et le bloc `ItemList` de `single-blog.php`) consultent ce même interrupteur.
>
> **Le reste de la page est intact.** Le classement visible « Analyses
> Éditoriales » (cartes agences, logos, badges RANK, boutons « voir le site
> de… ») s'affiche exactement comme avant : seul le `<script type="application/
> ld+json">` disparaît. Vérifié sur les 12 routes — voir
> `changes/2026-07-10-schema-disabled.md`.
>
> **Le reste de ce document décrit ce qui sera émis une fois réactivé.**

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

> Tant que l'interrupteur est sur `false` (état actuel), le code source ne
> contient **aucun** `<script type="application/ld+json">` : c'est le résultat
> attendu, pas une régression. Les étapes ci-dessous supposent le schéma
> réactivé.

1. Afficher le code source d'un article classement
   (ex. `/blog/top-agencies/`) → deux blocs
   `<script type="application/ld+json">` (`Organization` + `ItemList`) ;
   une page sans classement n'en a qu'un (`Organization`).
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
