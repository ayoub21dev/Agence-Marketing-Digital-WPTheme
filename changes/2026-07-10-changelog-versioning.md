# Changelog et versionnage dynamiques (2026-07-10)

<!-- changelog: Added -->

Demandé : un changelog et un versionnage **dynamiques**. Trois pièces : un
générateur (`npm run release`), un fichier généré (`CHANGELOG.md`), et deux
écrans dans wp-admin qui le lisent à l'exécution.

## 1. Une seule source de vérité pour la version

L'en-tête `Version:` de `style.css`. `package.json` la reflète, rien d'autre ne
la code en dur.

Le déploiement ajoute déjà le numéro de build sur la **copie déployée**
(`Version: 1.2.0.57`, via `github.run_number`). C'est pourquoi le thème lit
l'en-tête **installé** à l'exécution, `v5_digital_theme_version()`, au lieu de
définir une constante que le déploiement ne pourrait pas modifier : l'admin
affiche donc le build exact qui est en ligne.

## 2. `npm run release` — le générateur

`bin/release.mjs`, zéro dépendance (Node ≥ 18, `git` sur le PATH).

| Commande | Effet |
| --- | --- |
| `npm run release:dry` | aperçu — n'écrit rien |
| `npm run release` | bump + CHANGELOG + commit + tag |
| `npm run release -- --bump minor` | impose le niveau |
| `npm run changelog:check` | audit : quels commits ne livrent pas de fiche `changes/` |

**Le bump vient des Conventional Commits** depuis le dernier tag : `!` ou
`BREAKING CHANGE:` → *major*, `feat:` → *minor*, sinon *patch*.

**Il refuse de deviner.** Sur un commit non conventionnel il s'arrête, les
liste, et propose trois issues (`--bump`, `--allow-unconventional`, ou
reformuler). Sur ce dépôt seuls 24 commits sur 164 sont conventionnels : un
`feat:` manqué partirait silencieusement en *patch*. Un changelog faux est pire
que pas de changelog.

**Il ne pousse jamais.** Il imprime la commande et s'arrête — pousser `main`
est un déploiement FTP en production (cf. `CLAUDE.md`).

### D'où viennent les entrées

Une entrée par fiche `changes/*.md` **ajoutée** depuis le dernier tag ; son
titre `# H1` devient le texte (le ` (2026-07-10)` final est retiré).

La rubrique Keep a Changelog est décidée dans cet ordre :

1. un marqueur dans la fiche : `<!-- changelog: Fixed -->` (invisible au rendu),
   **dans les 10 premières lignes** ;
2. sinon le type du commit qui a ajouté la fiche : `feat`→Ajouté, `fix`→Corrigé,
   `perf`→Performance, `docs`→Documentation, `revert`→Supprimé ;
3. sinon *Modifié*.

Le marqueur existe parce que l'étape 2 ment parfois : la fiche
`2026-07-10-cpt-urls-and-crawlability.md` décrit un correctif SEO mais a été
ajoutée par un commit `docs:` — sans marqueur elle atterrissait dans
« Documentation ». Les six fiches de cette branche sont donc marquées.

**Pourquoi seulement l'en-tête ?** Cette fiche-ci *documente l'outil* : elle cite
la syntaxe du marqueur dans son texte, dont un exemple volontairement invalide
(`Bogus`). Une recherche sur tout le fichier prendrait le premier exemple venu et
classerait mal l'entrée — ou ferait échouer la release. La recherche s'arrête
donc à la ligne 10.

## 3. `CHANGELOG.md`

Format [Keep a Changelog](https://keepachangelog.com/), en-tête `## [Unreleased]`,
liens de comparaison GitHub en pied de fichier, tenus à jour par le script.

Amorcé à `## [1.0.0] - 2026-07-09` = l'état déployé en production, et le tag
`v1.0.0` a été posé sur `cc9dadb` (**en local, non poussé**) pour donner un point
de départ à `git describe`.

## 4. Dans wp-admin

Section `3c. VERSION & CHANGELOG` de `functions.php`.

- **Apparence → Nouveautés du thème** — tout le changelog.
- **Widget du tableau de bord** — la version installée et les 5 dernières
  entrées, avec un lien vers la page complète.

Les deux exigent `edit_theme_options`. Le markdown est analysé au runtime
(`v5_digital_parse_changelog()`) et mis en cache dans un transient.

Détails qui comptent :

- **Les liens relatifs sont aplatis.** `changes/**` est exclu du déploiement :
  sur la production ces `href` renverraient un 404. Seuls les liens `http(s)`
  restent des ancres ; le nom de fiche s'affiche en `<code>`.
- **La clé de cache est `mtime:filesize`, pas `mtime` seul.** `filemtime()` a une
  résolution d'une seconde — une réécriture dans la même seconde que la mise en
  cache reste invisible. Observé pendant les tests, puis reproduit
  (même `mtime`, tailles 42 → 48 octets : la clé `mtime` seul servait la version
  périmée, `mtime:filesize` détecte le changement).
- **Les enqueues gardent `filemtime()`.** Tentant de passer à la version du
  thème ; à ne pas faire : `WP_DEBUG` vaut `false` et `WP_ENVIRONMENT_TYPE` n'est
  pas défini sur le Studio local, donc tous les signaux « dev » y répondent
  « production », et une URL versionnée cesserait de se rafraîchir pendant qu'on
  édite le CSS.

## 5. i18n

13 nouveaux msgid français (`Nouveautés du thème`, `Ajouté`, `Corrigé`, …)
ajoutés à `languages/en_US.po` et au `.pot`, puis `en_US.mo` recompilé.

Recompilé avec les classes POMO de WordPress, après avoir vérifié qu'un
aller-retour du `.po` actuel reproduisait le `.mo` livré **octet pour octet**
(23 920 o) — la régénération est donc sans perte. Diff de contrôle après ajout :
**0 entrée perdue, 0 traduction modifiée, 13 ajoutées** (309 → 322).

## 6. Déploiement

`bin/**` ajouté aux trois listes d'exclusion de `deploy1.yml` : l'outillage ne
part pas en production. `CHANGELOG.md`, lui, **est** déployé — la page d'admin le
lit sur le serveur.

## Vérification

| Contrôle | Résultat |
| --- | --- |
| `--check`, `--dry-run`, `--bump`, flags invalides | conformes |
| Refus sur commits non conventionnels | s'arrête, exit 1, liste les 2 commits |
| Refus si l'arbre de travail est sale | exit 1 |
| Refus si aucun commit depuis le tag | exit 1 |
| Marqueur invalide en en-tête (`Bogus`) | exit 1, message explicite |
| Même marqueur cité en prose (ligne 120) | ignoré — la recherche s'arrête ligne 10 |
| Marqueur posé ligne 12 | non retenu → repli sur le type de commit |
| Release réelle (bac à sable, `origin` retiré) | style.css, package.json, CHANGELOG, commit, tag |
| Deux releases d'affilée | `1.1.0` puis `1.2.0`, la plus récente en haut |
| Bump auto depuis un `feat:` | `1.1.0 → 1.2.0` (minor) ✔ |
| Liens `[Unreleased]` / `[1.2.0]` | régénérés à chaque release |
| Page admin + widget (boot WP réel, user admin) | rendus, 8 assertions vertes |
| XSS : `<script>`, `<img onerror>` dans une entrée | échappés (`&lt;script&gt;`), `wp_kses_post` |
| Lien absolu | ancre `rel="noopener noreferrer"` |
| Capacité insuffisante | `wp_die()` (constaté en oubliant `wp_set_current_user`) |
| Cache : même `mtime`, taille différente | détecté ✔ |
| Admin `en_US` / `fr_FR` | anglais / français, 0 fuite |
| **Front end, 12 routes, avant/après** | **octet pour octet identique** |

## Le bug attrapé en exécutant le script

Première version : `text.indexOf('## [Unreleased]')`. Le fichier **cite** cette
chaîne dans son propre paragraphe d'intro (« Ne le modifiez pas à la main sous
`## [Unreleased]` ») — `indexOf` matchait la prose et insérait la release **au
milieu de la phrase**. Corrigé par une ancre en début de ligne,
`/^## \[Unreleased\][ \t]*$/m`. Trouvé uniquement parce que la release a été
jouée pour de vrai dans un clone jetable, pas seulement en `--dry-run`.

## Prochaine release

La branche porte 6 fiches. Comme deux commits ne sont pas conventionnels, le
script refusera de choisir seul :

```
npm run release -- --bump minor      # -> 1.1.0
```

Puis, seulement si vous le décidez : `git push --follow-tags`.
