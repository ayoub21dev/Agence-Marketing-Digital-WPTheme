# Structured data (JSON-LD) désactivé (2026-07-10)

<!-- changelog: Changed -->

Demandé : *« deactivate the schema markup till i tell u »*. Le balisage n'est
**pas supprimé** — il passe derrière un interrupteur unique, à réactiver d'un
mot le jour venu.

## L'interrupteur

`functions.php` :

```php
function v5_digital_schema_enabled() {
    return (bool) apply_filters('v5_digital_schema_enabled', false);
}
```

Les deux — et seuls — émetteurs du thème le consultent :

| Émetteur | Fichier | Schéma |
| --- | --- | --- |
| `v5_digital_organization_schema()` | `functions.php` | `Organization`, sur **toutes** les pages |
| bloc `agency_reviews_block` | `single-blog.php` | `ItemList`, sur les articles classement |

Vérifié par `grep` : aucun autre `ld+json` / `schema.org` / `itemprop` dans le
thème. Rien d'autre à couper.

## Réactivation

Sans toucher au thème (mu-plugin, plugin, ou `functions.php` d'un thème enfant) :

```php
add_filter('v5_digital_schema_enabled', '__return_true');
```

Ou en basculant la valeur par défaut dans `functions.php` (`false` → `true`).

## Le piège évité : ne pas emporter la liste visible

Premier essai — **faux**. Le `if` qui ouvre le bloc *Analyses Éditoriales*
enveloppe à la fois le JSON-LD **et** le HTML visible :

```php
// ✗ supprime AUSSI les cartes agences visibles par le lecteur
if (v5_digital_schema_enabled() && !empty($agency_reviews)) :
    ... // JSON-LD
    ?>
    <div class="mt-10 pt-8 border-t border-slate-200">   ← le classement visible
```

Mesuré : `/blog/top-agencies/` perdait 7 753 octets, la section « Analyses
Éditoriales », les deux cartes (RMD, Pixagram), les logos, les badges RANK et
les boutons « voir le site de… ». Un désastre silencieux : la page rendait
toujours 200, le titre et le corps de l'article étaient là.

Correct — la garde vit **à l'intérieur** du bloc, autour du seul `echo` :

```php
if (!empty($agency_reviews)) :
    if (v5_digital_schema_enabled()) {
        ... // JSON-LD uniquement
    }
    ?>
    <div class="mt-10 pt-8 border-t border-slate-200">   ← toujours rendu
```

Un commentaire au-dessus le rappelle : *« Keep the gate inside this block,
never on the `if (!empty($agency_reviews))` above it. »*

## Vérification

Protocole : **le même fichier sur le disque**, rendu deux fois, seul le filtre
change (`add_filter('v5_digital_schema_enabled', '__return_true')` via un
mu-plugin temporaire). Cela isole l'interrupteur — aucune autre variable.

> Un premier protocole comparait le rendu de `git show HEAD:` (LF) à celui de
> la copie de travail (CRLF, `core.autocrlf=true`) : le diff était pollué par
> les fins de ligne et illisible. Écarté.

12 routes : `/`, `/blog/`, `/about/`, `/contact/`, `/methodologie/`,
`/annuaire/`, `/blog/top-agencies/`, `/blog/hello-world/`,
`/blog/seo-casablanca/`, `/service/web-design/`, `/city/casablanca/`, 404.

| Contrôle | Résultat |
| --- | --- |
| Blocs `ld+json`, interrupteur ON | 1 par page ; **2** sur les 2 articles classement |
| Blocs `ld+json`, interrupteur OFF | **0 partout** |
| Occurrences de `schema.org`, OFF | **0 partout** |
| Diff ON → OFF | **seules** les lignes `ld+json` disparaissent |
| Lignes ajoutées par la désactivation | **0**, sur les 12 routes |
| Classement visible sur `/blog/top-agencies/`, OFF | intact : `Analyses Éditoriales`, `RMD`, `Pixagram`, `rmd.ma`, 2× « voir le site de » |
| Réactivation → `ItemList` valide | `top-agencies` positions `[1,2]`, `numberOfItems=2` ; `seo-casablanca` `[1]`, `numberOfItems=1` |
| `php -l functions.php` / `single-blog.php` | propres |

Autrement dit : désactiver n'enlève que le balisage ; réactiver le restitue
tel quel, correctif `position` de ce matin compris (voir
`changes/2026-07-10-schema-itemlist-position.md`).

## Fichiers

- `functions.php` — `v5_digital_schema_enabled()` + garde dans `v5_digital_organization_schema()`
- `single-blog.php` — garde autour du seul `echo` du JSON-LD
- `STRUCTURED-DATA.md` — bandeau ⛔ DÉSACTIVÉ ; le corps décrit ce qui sera émis une fois réactivé
