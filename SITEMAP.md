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

- **CPT sans page publique** (`agency`, `specialty_hub`, `stat_metric`,
  `testimonial`, `partner_logo`) — depuis le 2026-07-10 ils sont
  `publicly_queryable: false` **et** `rewrite: false` **et** `query_var: none`.
  Le thème n'a pas de template `single-*` pour eux : `single.php` rend la mise en
  page **d'un article de blog**, si bien que `/agency/<slug>/` affichait
  « retour aux articles », un temps de lecture et aucune donnée de l'agence.
  Ces 22 URL renvoient maintenant un **404 natif**. *(Auparavant : 200 et
  indexables — le thème n'émet aucun `noindex` en front-end.)*

  ⚠️ `publicly_queryable: false` **seul** ne suffit pas : les règles de réécriture
  subsistent, `WP::parse_request()` supprime la query var devenue non publique,
  la requête retombe sur la page d'accueil et `redirect_canonical()` renvoie une
  **301 vers l'accueil**. Google assimile ce schéma à un *soft 404*
  ([Search Central](https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes)) :
  pour une page qui ne doit pas exister, il faut un 404/410, jamais une
  redirection vers une URL sans rapport. D'où `rewrite: false`.

  À réactiver pour `agency` uniquement quand un vrai `single-agency.php` existera
  (Sprint 2).
- **Archives de catégories** (`/category/<slug>/`) — pas de template dédié
  dans le thème (rendu par le fallback `index.php`) et jamais liées : le blog
  filtre ses catégories côté client. À ajouter si un template `category.php`
  est créé un jour.
- **Archives taxonomies** (`/service/<slug>/`, `/city/<slug>/`) — publiques et
  fonctionnelles (elles listent les agences du terme), mais rendues par le
  fallback `index.php`. À ajouter au sitemap si un template d'archive dédié est
  créé un jour.

  ⚠️ `agency` doit conserver `exclude_from_search: false`. Ces archives lancent
  une `WP_Query` sans `post_type`, résolue via
  `get_post_types(['exclude_from_search' => false])` : passer ce flag à `true`
  vide silencieusement `/service/…` et `/city/…`. Les CPT sont exclus de la
  recherche par `v5_digital_exclude_cpts_from_search()` à la place.

  Depuis le 2026-07-10 ces archives ont perdu le préfixe `/blog/`
  (`with_front: false`). WordPress **ne redirige pas** après un changement de
  structure — `redirect_canonical()` n'applique que les règles courantes et
  l'historique `_wp_old_slug` d'un post. Un 301 explicite
  (`v5_digital` §5d) envoie donc `/blog/service/<t>/` → `/service/<t>/`.
  Les anciens singles `/blog/agency/<slug>/` ne sont **pas** redirigés : il n'existe
  pas d'équivalent, et rediriger vers une page sans rapport serait un soft 404.

- **`Sample Page`** — page d'exemple de WordPress, passée en brouillon le
  2026-07-10 (elle figurait dans le sitemap). À refaire sur la production.
- `/wp-sitemap.xml` (sitemap natif WordPress) — désactivé pour éviter deux
  sitemaps concurrents.

## À faire (SEO)

### Au déploiement — contenu (rien de tout ceci ne voyage avec le thème : c'est en base)

- [ ] **Réglages → Général → Langue du site : `Français`.** Sans cela, `WPLANG`
  reste vide, `en_US.mo` se charge pour **tout le monde** (back-office en anglais)
  et le front-end continue de servir `<html lang="en-US">` sur un site français.
- [ ] Passer `Sample Page` (page d'exemple de WordPress, ID 2) et `Hello world!`
  (article ID 1) en brouillon — ils sont publiés et donc listés dans le sitemap.
- [ ] Supprimer les articles de test (`/blog/test/`, `/blog/tesing-two/`,
  `/blog/another-test/`) encore publiés sur le site live.

### Au déploiement — archives de taxonomies

- [ ] Lier `/service/<term>/` et `/city/<term>/` depuis `/annuaire/` *(prévu
  manuellement)*. **Attention :** aujourd'hui elles sont orphelines (aucun lien
  entrant, absentes du sitemap) donc invisibles pour Google. Dès qu'elles seront
  liées, elles deviendront des pages indexables **rendues par le fallback
  `index.php` (18 lignes)**, sans template d'archive dédié et **sans balise
  canonical** (WordPress n'en ajoute pas sur les archives).

  Au moment de les lier, choisir :
  - soit créer un vrai `taxonomy-agency_service.php` / `taxonomy-agency_city.php`
    (et les ajouter au sitemap) ;
  - soit les passer en `noindex, follow` si elles restent minces — Google déclasse
    au niveau du **site entier** les domaines chargés de pages à faible valeur
    ([contenu utile](https://developers.google.com/search/docs/fundamentals/creating-helpful-content)).

  Ne pas les lier *et* les laisser en `index.php` : c'est le pire des deux mondes.

### Après déploiement

- [ ] Soumettre le sitemap dans Google Search Console :
  `https://agencemarketingdigital.com/sitemap.xml`.
- [ ] Vérifier qu'aucun `robots.txt` physique n'existe sur Hostinger (sinon y
  ajouter manuellement la ligne `Sitemap:`).
- [ ] Vérifier dans la Search Console qu'aucune des 22 anciennes URL de CPT
  (`/agency/…`, `/testimonial/…`, `/partner_logo/…`, `/stat_metric/…`,
  `/specialty_hub/…`) n'était indexée. Elles renvoient désormais un 404 natif.
