# Accorder le tampon de déploiement et le versionnage sémantique (2026-07-10)

<!-- changelog: Fixed -->

Deux outils écrivent l'en-tête `Version:` de `style.css` :

- `npm run release` y pose la version sémantique (`1.2.0`), commitée ;
- le workflow de déploiement ajoute le numéro de run GitHub **sur la copie
  déployée uniquement** (`1.2.0.53`).

Ils ne se connaissaient pas. Trois désaccords, tous corrigés.

## 1. Le tampon pouvait doubler — et le déploiement ne s'en apercevait pas

L'ancien motif capturait `[0-9.]*`, qui matche aussi une version **déjà**
tamponnée :

```
Version: 1.2.0.99   --sed-->   Version: 1.2.0.99.53
```

Reproduit. Le nouveau motif exige trois composants et une fin de ligne, et le
workflow **vérifie l'en-tête avant de tamponner** :

```bash
grep -qE '^Version: [0-9]+\.[0-9]+\.[0-9]+$' style.css || exit 1   # avant
sed -i -E "s/^(Version: [0-9]+\.[0-9]+\.[0-9]+)$/\1.${RUN}/" style.css
grep -qE '^Version: [0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' style.css || exit 1  # après
```

La vérification **avant** est indispensable : un contrôle a posteriori ne sait
pas distinguer « je viens de tamponner » de « c'est arrivé tamponné ». Un
`style.css` tamponné commité par erreur passait le contrôle final et partait en
production tel quel.

| Cas | Avant | Maintenant |
| --- | --- | --- |
| `Version: 1.2.0` | `1.2.0.53` | `1.2.0.53` ✔ |
| `Version: 1.2.0.99` (déjà tamponné) | `1.2.0.99.53` ✖ | déploiement en erreur |
| `Version: 1.2` | `1.2.53` ✖ | déploiement en erreur |
| en-tête absent | tampon silencieusement ignoré | déploiement en erreur |

## 2. `npm run release` disait n'importe quoi devant un en-tête tamponné

Il exigeait `X.Y.Z` et échouait sur « no `Version: x.y.z` header » — faux et
inutile. Il reconnaît désormais le suffixe et nomme le vrai problème :

```
error  style.css carries a deploy build suffix: "Version: 1.2.0.53".
       That stamp belongs to the deployed copy only — a stamped style.css was committed.
       Restore the clean version first:  Version: 1.2.0
```

## 3. L'admin ne pouvait pas relier le build au changelog

`1.0.0.53 !== 1.0.0`, donc l'écran « Nouveautés du thème » affichait un numéro
que l'on ne retrouvait dans aucune entrée. `v5_digital_theme_version_parts()`
sépare les deux moitiés :

| En-tête installé | `semver` | `build` |
| --- | --- | --- |
| `1.2.0.53` (production) | `1.2.0` | `53` |
| `1.2.0` (local, dépôt) | `1.2.0` | *(vide)* |

L'admin affiche donc `1.2.0` puis un discret `build 53`, et la version installée
reçoit un badge **installée** sur sa carte dans le changelog. Tout ce qui ne
ressemble pas à `X.Y.Z[.B]` est rendu tel quel, sans build : mieux vaut une
version non rattachée qu'un mauvais rattachement.

## Pourquoi pas `1.2.0+build.53` (métadonnées SemVer) ?

Parce que PHP les classe **en dessous** de la version de base :

```php
version_compare('1.2.0+build.53', '1.2.0', '>');  // false
version_compare('1.2.0.53',       '1.2.0', '>');  // true
```

Le quatrième composant pointé est donc conservé. Un commentaire dans le
workflow interdit explicitement la conversion.

## Une note sur les fins de ligne

Le motif exige `$` juste après les chiffres : il suppose LF. Le runner Ubuntu
sort du checkout en LF et `style.css` est en LF (vérifié : les 9 lignes).

Le comportement CRLF **n'a pas pu être testé ici** : le `sed` de MSYS ouvre les
fichiers en mode texte (`sed -n l` sur `a\r\n` affiche `a$`), il absorbe le CR à
la lecture et ne le réécrit pas. Une première version « tolérante au CRLF »
(`(\r?)$` recapturé) semblait passer alors qu'elle **supprimait** le CR — donc
elle a été retirée. Si le checkout devenait un jour CRLF, l'assertion échoue
bruyamment plutôt que de livrer un en-tête mal formé. C'est le bon échec.

## Durcissement : trois pannes possibles, supprimées

Trouvées en rejouant chaque cas limite **dans un processus neuf** — indispensable,
car le `static $cache` de `v5_digital_get_changelog()` servait la première
analyse à tous les cas suivants et rendait la première campagne de tests vide de
sens (17 « clean » qui ne prouvaient rien).

1. **UTF-8 invalide dans `CHANGELOG.md` → erreur SQL affichée dans l'admin.**
   Un octet malformé traversait l'analyseur, arrivait dans `set_transient()`, la
   base refusait l'écriture et l'erreur SQL brute s'imprimait dans la page.
   `esc_html()` aurait de toute façon renvoyé `''` pour l'entrée. L'analyseur
   passe désormais chaque chaîne par `wp_check_invalid_utf8($s, true)`.

2. **`wp_add_dashboard_widget()` n'existe pas hors de wp-admin.** Le cœur charge
   `wp-admin/includes/dashboard.php` avant de déclencher `wp_dashboard_setup`,
   donc le cas ne se produit pas aujourd'hui ; il suffit qu'une extension
   déclenche ce hook ailleurs pour obtenir un écran blanc. `function_exists()`
   garde l'appel. Vérifié : `do_action('wp_dashboard_setup')` depuis le front
   ne provoque plus rien.

3. **`sprintf()` sur une chaîne traduite peut lever `ArgumentCountError`.** Une
   traduction contenant un placeholder de trop (« build %s %d ») ferait planter
   l'admin en PHP 8. L'étiquette de build est désormais interpolée par
   `str_replace()` : une faute de traduction ne peut plus provoquer de fatale.

## Vérification

| Contrôle | Résultat |
| --- | --- |
| Étape de tampon, 5 cas (propre, tamponné, `1.2`, sans en-tête, vrai `style.css`) | conforme (2 succès, 3 échecs voulus) |
| `version_compare` : `.53` vs `+build.53` | `true` / `false` — le point l'emporte |
| `readThemeVersion()` sur un en-tête tamponné | message explicite, exit 1 |
| `v5_digital_theme_version_parts('1.0.0.53')` | `1.0.0` + `53` |
| Admin, install locale (`1.0.0`) | `1.0.0`, pas de build, badge sur `[1.0.0]` |
| Admin, install déployée (`1.0.0.53`) | `1.0.0` + `build 53`, badge sur `[1.0.0]` |
| `[9.9.9]` et `Unreleased` non badgés | ✔ |
| i18n : `build %s`, `installée` | ajoutés (322 → 324), 0 perdue, 0 modifiée |
| `php -l`, `node --check` | propres |
| **19 cas limites du changelog**, un processus par cas | 0 fatale, 0 diagnostic, 0 erreur SQL, 0 XSS |
| — fichier vide / absent / illisible / prose seule | notice « CHANGELOG.md introuvable ou vide » |
| — titre malformé, entrée orpheline, section sans version | ignorés proprement |
| — `<script>`, `[x](javascript:…)`, backtick ouvert, `**` déséquilibré | échappés, jamais d'ancre `javascript:` |
| — entrée de 100 ko, CRLF, section inconnue, UTF-8 invalide | rendus sans incident |
| **Front end, 12 routes sous `E_ALL`** | HTML réel, **0 notice / warning / deprecated / fatale** |
| Contrôle négatif du harnais (variable indéfinie plantée) | **détectée** — le harnais n'est pas aveugle |
| Admin (page + widget + chemin transient), `E_ALL` | 0 diagnostic |
| Admin `fr_FR` et `en_US`, `E_ALL` | 0 diagnostic |
| Abonné : page et widget | `wp_die()` / widget non enregistré |
