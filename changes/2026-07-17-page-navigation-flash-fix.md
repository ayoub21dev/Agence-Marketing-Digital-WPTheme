# Fix : flash de contenu à chaque changement de page (2026-07-17)

<!-- changelog: Fixed -->

Signalé : la page « flashe » à chaque navigation. Diagnostic confirmé dans le
code : le contenu apparaissait, **disparaissait**, puis se fondait à nouveau —
à chaque chargement de page.

## La cause

La garde anti-flash de `header.php` était inversée par sa propre règle de
repli : `body:not(.motion-enhanced) … { opacity: 1 }` (spécificité 0,2,1) bat
la règle de masquage `.hero-title { opacity: 0 }` (0,1,0) — et comme la classe
`motion-enhanced` n'est ajoutée qu'au `DOMContentLoaded` (bien APRÈS le premier
rendu, contrairement à ce qu'affirmait le commentaire « runs immediately
(before first paint) »), le premier rendu était toujours **visible**. Puis
`gsap.from(…, autoAlpha: 0)` arrivait : `from()` fige d'abord l'élément à
invisible avant de l'animer vers son état courant → apparition → clignotement
→ fondu. Le bandeau sticky subissait le même `from()` à chaque navigation —
la partie la plus visible du flash, l'élément censé rester stable.

## Le correctif

**1. La garde fonctionne vraiment maintenant** (`header.php`) : un script
inline d'une ligne en tout début de `<head>` ajoute `v5-motion-pending` sur
`<html>` — exécuté de façon synchrone, donc garanti AVANT tout rendu (une
classe sur `<body>` ne peut pas offrir cette garantie). La CSS de garde ne
masque que sous cette classe : les visiteurs sans JS et les crawlers ne la
reçoivent jamais, donc rien ne leur est jamais caché.

**2. Libération synchrone avant la construction des tweens**
(`theme-scripts.js`) : `initMotionSystem()` retire la classe juste avant de
créer la timeline, dans le même bloc synchrone. Ordre critique :
`gsap.from()` capture ses valeurs d'ARRIVÉE depuis le style calculé courant —
si la garde s'appliquait encore, les tweens animeraient 0 → 0 et le contenu
resterait invisible. Aucun rendu ne peut s'intercaler entre la libération et
les tweens (même tâche JS).

**3. L'intro du bandeau ne joue qu'une fois par session** : le `from()` sur
`<header>` (non couvert par la garde — masquer la navigation serait pire) ne
joue que sur la première page de la session (`sessionStorage`), plus jamais
aux navigations suivantes. En navigation privée (stockage indisponible) : pas
d'animation du tout — le défaut sûr.

**4. Trois filets de sécurité** : (a) une animation CSS pure retardée (1,2 s,
`forwards`) révèle le contenu même si tout le JS meurt après le script inline ;
(b) `prefers-reduced-motion` est exempté de la garde en CSS — jamais masqué,
même pas entre le rendu et le boot ; (c) le chemin `canUseMotion() === false`
(CDN GSAP mort, reduced motion) retire la classe immédiatement.

**5. L'iframe d'aperçu admin synchronisée** (`functions.php` §2b) : sa copie
des règles anti-flash (avertissement « keeping this copy in sync » dans le
code) reçoit le même mécanisme — sélecteurs non scopés à `main` car une
section prévisualisée est un enfant direct de `<body>`. L'iframe charge le
vrai `theme-scripts.js`, donc la même libération s'applique ; l'intro bandeau
y est sans objet (pas de `<header>`, garde nulle existante).

## Comportement résultant

| Scénario | Avant | Après |
|---|---|---|
| Navigation normale | visible → clignote → fondu | fondu d'entrée propre (jamais visible avant) |
| Navigations suivantes | idem, bandeau compris | bandeau stable ; contenu en fondu simple |
| JS désactivé / crawlers | visible | visible (classe jamais posée) |
| Reduced motion | visible, sans animation | identique |
| CDN GSAP en panne | visible (pas d'animation) | caché jusqu'au DOMContentLoaded puis révélé |
| theme-scripts.js en panne | visible | révélé par le filet CSS à 1,2 s |

## Vérification

`php -l` sur `header.php` + `functions.php`, `node --check` sur
`theme-scripts.js` — tous propres. Contrat de classe vérifié par grep :
`v5-motion-pending` présent aux 3 endroits requis, retirée dans les DEUX
chemins de boot, anciennes règles `body:not(.motion-enhanced)` toutes
supprimées, seule dépendance CSS restante à `motion-enhanced` un
`will-change` inoffensif (la classe est toujours posée par le JS). Aucune
classe Tailwind ajoutée : pas de rebuild. Test navigateur réel encore à
faire sur le site local (serveur non lancé pendant l'édition).

## Fichiers touchés

`header.php`, `assets/js/theme-scripts.js`, `functions.php`.
