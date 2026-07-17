# Correctifs issus de la relecture complète du thème (2026-07-17)

<!-- changelog: Fixed -->

Suite à une relecture exhaustive de tout le dépôt (14 lecteurs parallèles sur
chaque sous-système), cinq problèmes réels ont été identifiés et corrigés.

## 1. Désactiver ACF ré-exposait silencieusement les CPT en public

Le fallback PHP `v5_digital_register_cpts()` (§1 de functions.php) ne s'exécute
que quand ACF est désactivé — mais il enregistrait alors les CPT avec des
arguments périmés (`public` sans durcissement, `has_archive: true` sur agency,
taxonomies sans `with_front: false`), annulant les quatre drapeaux SEO
anti-soft-404 d'acf-json. Corrigé : un tableau `$v5_cpt_hardening` partagé
(`publicly_queryable/rewrite/query_var/has_archive` à false,
`exclude_from_search` à true) appliqué par union à chaque CPT ; `agency` garde
explicitement `exclude_from_search: false` (les archives de taxonomies en
dépendent — la recherche est exclue par `pre_get_posts`, §5c-ter) ; les deux
taxonomies reçoivent `with_front: false`.

## 2. L'élément de menu « Blog » ne s'activait jamais sur les articles

`v5_digital_menu_item_is_active()` testait encore `is_singular('blog')` /
`is_post_type_archive('blog')` — le CPT `blog` a été migré vers les articles
natifs, ces branches étaient mortes. Corrigé : `is_singular('post')` +
`is_home()` (le fallback `v5_digital_nav_fallback_is_active()` est nettoyé des
mêmes branches mortes).

## 3. Les sections méthodologie cassaient sans la section « process »

`.method-wrap` et `.section-title` n'existaient que dans le `<style>` inline de
`methodology_process.php` ; `methodology_evidence` et `methodology_monitor`
perdaient leur largeur et leur style de titre placées seules sur une page (et
les règles `.section-head`/`.section-copy`, scopées `#process`, n'atteignaient
jamais monitor même sur la même page). Corrigé : copies scopées
(`.blg-evidence-section .method-wrap`, etc.) dans le style de chaque section —
même motif « rendu autonome » que la copie `.blog-grid-wrap` de
`newsletter_cta.php`.

## 4. Documentation à jour

- **CLAUDE.md** : la liste des layouts compte maintenant les 21 fichiers
  (ajout de `form`), et `group_site_settings` + la page d'options « Site
  Settings » sont documentés.
- **ROADMAP.md** : les items déjà livrés portent un bandeau de statut
  (✅ #1 aperçu live, ◑ #2 champs conditionnels, ✅ #4 page d'options,
  ✅ #5 langue admin, ✅ #8 pop-up exit-intent) ; la référence au
  `REBUILD-PLAN.md` inexistant pointe vers le projet multisite Rhillane ;
  le parking lot documente les deux découvertes (backends de formulaires
  manquants, matchmaker/palette de recherche inaccessibles).
- **README.md** : l'URL de clone pointe vers le bon compte GitHub
  (`ayoub21dev`, celui du remote et des liens du changelog).

## 5. Petits correctifs

- **Troncature multi-octets** (`blog_posts_grid.php`) : `strlen`/`substr` →
  `mb_strlen`/`mb_substr` — la coupe à 115 octets pouvait scinder un caractère
  accentué et afficher un glyphe cassé.
- **`about_cta.php`** : une URL externe complète passait par `home_url()` et
  devenait `https://site.comhttps://externe.com` ; désormais seuls les chemins
  relatifs sont résolus.
- **`picks.php`** : `theme_render_stars_html()` renommée
  `v5_digital_render_stars_html()` (convention de préfixe du thème).
- **i18n** : 15 chaînes françaises encore en dur passent par `v5_t()` et sont
  enregistrées dans `v5_digital_ui_strings()` — fallback de la page d'accueil
  (2), libellés 404 (« 404 · Page introuvable », « Lire »), fil d'Ariane de
  l'article (« Accueil » ; « Blog » existait déjà), « 5 min de lecture »
  (3 gabarits), libellés de service fallback du matchmaker (4 — les *values*
  restent volontairement non traduites, le filtrage d'/annuaire/ les matche)
  et tranches de budget MAD (4).

## Vérification (deux passes)

**Passe 1 :** `php -l` (PHP 8.4 réel) sur les 10 fichiers touchés — tous
propres. Contrôle croisé par script PHP (pas de boucle bash, qui mutile les
accents) : chaque nouvelle chaîne existe à l'identique dans le registre ET au
point d'appel — ce contrôle a attrapé 4 libellés MAD enregistrés mais pas
encore enveloppés, corrigés dans la foulée. Aucune classe Tailwind ajoutée :
pas de rebuild nécessaire.

**Passe 2 — relecture adversariale (4 réviseurs indépendants sur le diff) :**
le correctif menu et le durcissement CPT sont sortis propres sur le code, mais
la passe a confirmé 3 vrais défauts dans les copies CSS méthodologie et
2 points annexes, tous corrigés :

- Les copies `.method-wrap` omettaient la règle mobile `@media (max-width:
  640px)` du fichier process — et comme une copie scopée (spécificité 0,2,0)
  bat la règle média non scopée (0,1,0), evidence/monitor seraient devenues
  12px plus étroites que process sur mobile, même sur la même page. Ajout de
  la règle média scopée identique dans les deux fichiers.
- Les « copies » `.section-head`/`.section-copy` de monitor n'étaient pas des
  copies : les originales sont scopées `#process` et n'ont jamais stylé cette
  section — les garder aurait modifié le rendu actuel de /methodologie/.
  Supprimées (le statu quo visuel est préservé), avec un commentaire
  expliquant pourquoi elles ne doivent pas être « complétées ».
- `.section-label` (couleur + marge, définies seulement dans le style inline
  de process) manquait aux copies — en rendu autonome, les labels perdaient
  couleur et espacement. Copie scopée ajoutée aux deux fichiers.
- `V5_DIGITAL_REWRITE_VERSION` bumpée (`2026-07-17-fallback-hardening`) : le
  durcissement change des arguments de réécriture du fallback, et la règle du
  projet impose un flush par version à chaque changement de ces drapeaux.
- La closure d'`about_cta` ne laissait passer que `https?://` — `mailto:`
  (l'entrée naturelle du CTA « Nous contacter »), `tel:` et `//hôte` étaient
  encore mutilés par `home_url()`. Élargie à tout schéma d'URI + les URLs
  protocole-relatives, comportement vérifié par harnais PHP réel.

Le 4e réviseur (i18n/renommage) est mort en cours de route (erreur API) ; ses
angles d'attaque ont été couverts à la main : correspondance octet à octet
registre↔appel (déjà validée en passe 1, y compris `&` et apostrophes
échappées), aucun JS ne compare les libellés traduits (le matchmaker n'utilise
que `data-value`), aucune référence restante à `theme_render_stars_html` dans
les `.php`/`.js`/`.md`.

## Fichiers touchés

`functions.php`, `front-page.php`, `404.php`, `single-blog.php`, `footer.php`,
`template-parts/layouts/blog_posts_grid.php`, `template-parts/layouts/picks.php`,
`template-parts/layouts/about_cta.php`,
`template-parts/layouts/methodology_evidence.php`,
`template-parts/layouts/methodology_monitor.php`, `CLAUDE.md`, `ROADMAP.md`,
`README.md`.
