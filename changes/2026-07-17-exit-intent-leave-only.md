# Pop-up exit-intent : uniquement en quittant l'article (2026-07-17)

<!-- changelog: Changed -->

Demandé : le pop-up newsletter ne doit apparaître QUE quand le visiteur quitte
l'article — jamais pendant qu'il est encore en train de lire.

## Ce qui change

Le déclencheur « défilement rapide vers le haut » (mobile) est supprimé. Il
était pensé comme substitut tactile de l'exit-intent (pas de curseur à lire
sur mobile), mais il se déclenchait pendant la **lecture normale** : remonter
d'un geste vif pour relire un passage suffisait à ouvrir le pop-up alors que
le visiteur restait sur la page — exactement le comportement signalé.

Les deux déclencheurs restants sont de vrais signaux de départ :

- **Desktop** : le curseur sort du viewport vers le haut (barre d'onglets /
  bouton retour), sans élément cible — le geste classique avant de fermer ou
  changer d'onglet.
- **Tous appareils** : clic sur « retour aux articles » — une navigation
  réelle, interceptée puis complétée à la fermeture du pop-up
  (`exitIntentPendingRedirect`).

Inchangé : la porte serveur (`is_singular('post')` uniquement), une seule
apparition par session (`sessionStorage`), plus jamais après inscription
(`localStorage`), le délai d'ouverture de 220 ms (commentaire mis à jour — sa
justification mobile vaut toujours pour le tap sur le lien retour).

## Compromis assumé

Sur mobile, sans le déclencheur de scroll, le pop-up ne peut plus se
déclencher que via « retour aux articles » — un visiteur mobile qui ferme
l'onglet directement ne le verra jamais. C'est le prix de « seulement en
quittant » : le signal de scroll était trop bruyant pour distinguer lecture
et départ.

## Vérification

`node --check` propre ; `grep` confirme zéro référence orpheline
(`onScroll`/`lastScrollY`/`lastScrollTime` toutes supprimées, les deux
listeners restants correctement ajoutés ET retirés dans `trigger()`).
Test navigateur réel à faire sur le site local.

## Fichiers touchés

`assets/js/theme-scripts.js`.
