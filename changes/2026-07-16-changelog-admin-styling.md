# Habillage visuel de l'écran « Nouveautés du thème » (2026-07-16)

<!-- changelog: Changed -->

Demandé : améliorer l'apparence de l'écran Apparence → Nouveautés du thème et
du widget du tableau de bord. Aucune logique touchée — `v5_digital_get_changelog()`,
le cache par transient, l'échappement (`esc_html()`/`wp_kses_post()`) et les
chaînes traduites via `v5_digital_translate_admin_string()` sont inchangés.

## Ce qui change

- Nouvelle feuille de style dédiée `assets/admin/changelog.css`, chargée
  uniquement sur les deux écrans concernés (`appearance_page_v5-digital-changelog`
  et `index.php`) — même convention que `assets/admin/section-preview.css`.
- Les releases s'affichent en frise chronologique (`.v5-cl-timeline`) avec un
  point coloré par version : bleu (couleur du thème admin, via
  `var(--wp-admin-theme-color)`) pour la version installée, gris pour les
  autres — plus lisible que la simple bordure gauche précédente.
- Les sections Keep a Changelog (Ajouté/Corrigé/Modifié/…) deviennent des
  badges colorés avec icône dashicon au lieu de simples `<h3>` — nouvelle
  fonction `v5_digital_changelog_section_meta()`, qui associe chaque section à
  un slug CSS + un dashicon (repli neutre pour une section inconnue, donc un
  futur marqueur `<!-- changelog: X -->` non prévu dans la palette ne casse
  rien).
- Le bandeau « Version installée » devient une petite carte au lieu d'un `<p>`
  brut, répété en version compacte dans le widget.
- Remplacement de tous les `style="…"` inline par des classes (`v5-cl-*`),
  plus simple à maintenir que la version précédente.

## Pourquoi pas de nouvelles chaînes traduites

Volontaire : c'est un changement purement visuel. Tout le texte affiché existait
déjà et passait déjà par `v5_digital_translate_admin_string()` — aucune entrée
supplémentaire à ajouter dans `v5_digital_ui_strings()` ni dans les fichiers
`.po`/`.mo`.

## Vérification

`php -l` sur `functions.php` après modification — aucune erreur de syntaxe.
Hook suffix `appearance_page_v5-digital-changelog` confirmé en lisant
`get_plugin_page_hookname()` dans le cœur WordPress réel installé sur cette
machine (`wp-admin/includes/plugin.php`), pour éviter un enqueue qui échoue
silencieusement sur un hook mal deviné.

## Fichiers touchés

`functions.php`, `assets/admin/changelog.css` (nouveau).
