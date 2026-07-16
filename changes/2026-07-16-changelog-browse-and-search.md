# Changelog navigable : sommaire des versions + recherche/filtre (2026-07-16)

<!-- changelog: Added -->

Demandé : pouvoir voir le contenu de chaque version et « surfer » dans tout le
journal, pas seulement lire la liste de haut en bas. Inspiré du sommaire de
versions que GitHub a lui-même ajouté à ses pages Releases (juin 2026, « pour
aider à parcourir et naviguer plus facilement ») et des chips de filtre par
catégorie qu'utilisent des changelogs SaaS comme celui de Shopify.

## Ce qui change

Sur Apparence → Nouveautés du thème uniquement (le widget du tableau de bord
n'affiche qu'une seule version, une recherche n'y a pas de sens) :

- **Sommaire des versions** (`.v5-cl-toc`) : une colonne collante à gauche qui
  liste toutes les versions ; cliquer saute directement à cette version dans
  le journal. La version en cours de lecture se surligne automatiquement au
  défilement (`IntersectionObserver`).
- **Recherche** : un champ texte qui filtre les entrées en direct (titre,
  nom de fichier `changes/*.md` inclus) sans recharger la page.
- **Filtres par catégorie** : des puces Ajouté/Modifié/Corrigé/… (une par
  catégorie réellement présente dans le journal) qu'on peut activer/désactiver
  pour ne voir, par exemple, que les correctifs de sécurité sur tout
  l'historique.
- Une version ou une section qui n'a plus aucune entrée visible se masque
  entièrement (et son entrée de sommaire aussi), avec un message si plus rien
  ne correspond.

## Implémentation

- Nouveau fichier `assets/admin/changelog.js` (vanilla, IIFE, même style que
  `section-preview.js` — aucune dépendance jQuery), chargé uniquement sur
  `appearance_page_v5-digital-changelog`.
- `assets/admin/changelog.css` : mise en page en grille sommaire/journal,
  puces de filtre, sommaire collant, `scroll-behavior: smooth` scopé via
  `html:has(.v5-changelog)` pour ne pas affecter le reste de wp-admin,
  désactivé sous `prefers-reduced-motion`.
- Deux nouvelles fonctions dans `functions.php` : `v5_digital_changelog_release_slug()`
  (identifiant d'ancre stable par version) et `v5_digital_changelog_sections_present()`
  (n'affiche une puce de filtre que pour une catégorie réellement utilisée).
- Toute la logique existante (cache par transient, échappement, traduction) est
  inchangée — uniquement de la présentation en plus par-dessus le HTML déjà
  généré par `v5_digital_get_changelog()`.

## Vérification

Exécution réelle de `v5_digital_render_changelog_page()` via le binaire
PHP 8.4 de cette machine (bouchons minimalistes pour les fonctions WP), sur le
vrai `CHANGELOG.md` et `style.css` : 2 versions détectées, sommaire et ancres
alignés, badge « installée » sur la bonne version, balises `<div>` équilibrées
(16 ouvrantes / 16 fermantes), HTML validé par `DOMDocument` (une seule
« erreur » signalée sur la balise `<nav>`, un faux positif connu du
validateur HTML4 de libxml qui ne connaît pas les éléments HTML5). Relecture
manuelle de `changelog.js` (pas de runtime Node disponible sur cette machine).

## Fichiers touchés

`functions.php`, `assets/admin/changelog.css`, `assets/admin/changelog.js` (nouveau).
