<div align="center">
  <h1>Agence Marketing Digital</h1>
  <p><b>Thème WordPress Sur Mesure — Annuaire Éditorial & Marketplace des Agences au Maroc</b></p>

  [![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg?logo=wordpress&logoColor=white)](https://wordpress.org)
  [![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg?logo=php&logoColor=white)](https://php.net)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4%2B-38BDF8.svg?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
  [![ACF Pro](https://img.shields.io/badge/ACF_Pro-Flexible_Content-000000.svg?logo=advancedcustomfields&logoColor=white)](https://advancedcustomfields.com)
  [![License](https://img.shields.io/badge/License-GPLv2-green.svg)](LICENSE)

  <br />
  <a href="#présentation"><b>Présentation</b></a> •
  <a href="#fonctionnalités-clés"><b>Fonctionnalités</b></a> •
  <a href="#architecture--modèle-de-données"><b>Architecture</b></a> •
  <a href="#guide-de-développement-local"><b>Guide Dev Local</b></a> •
  <a href="#auteurs--crédits"><b>Auteurs</b></a>
</div>

---

## Présentation

**Agence Marketing Digital** est un thème WordPress d'ingénierie moderne conçu spécifiquement pour le marché marocain. Il alimente une plateforme éditoriale et un annuaire indépendant comparant les agences de marketing digital au Maroc (SEO, SEA, Social Media, Branding, Web Design).

Conçu selon des standards stricts de performance et de modularité, le thème intègre un constructeur de page dynamique basé sur **ACF Flexible Content**, des animations fluides **GSAP**, une typographie soignée et un système multilingue natif compatible **Polylang**.

---

## Fonctionnalités Clés

- **Design System Premium & Responsive** : Interface épurée et moderne développée avec Tailwind CSS et des composants UI harmonieux.
- **Constructeur de Page Modulaire (`page_layouts`)** : Plus de 15 blocs de contenu interchangeables (Heroes, Grilles d'agences, Comparatifs, Processus, FAQs).
- **Gestion des Agences & Filtres Dynamiques** : Fiches détaillées, notations vérifiées, badges de classement et filtres par ville et spécialité.
- **Tableau de Bord & Données Synchronisées** : Intégration ACF Local JSON (`acf-json/`) garantissant le versionnage Git de toutes les structures de données.
- **Prêt pour le Multilingue (i18n)** : Support complet de Polylang avec fonctions d'échappement sécurisées et registre de chaînes traduisibles (`v5_t()`).
- **Optimisation & Vitesse Extrême** : Compilation Tailwind en production, scripts différés (Lucide Icons, GSAP) et zéro dépendance lourde inutile.

---

## Stack Technique

| Composant | Technologie | Description |
| :--- | :--- | :--- |
| **CMS Core** | WordPress 6.x+ | Architecture thématique native PHP 8.1+ |
| **Styles (CSS)** | Tailwind CSS 3.4+ | Framework utilitaire avec pipeline de compilation |
| **Content Builder** | ACF Pro (Local JSON) | Flexible Content fields versionnés dans Git |
| **Animations** | GSAP 3.12+ & ScrollTrigger | Révélations au défilement fluides & optimisées |
| **Icônes** | Lucide Icons | Ensemble d'icônes vectorielles légères |
| **Multilingue** | Polylang | Gestion multilingue avec fallback natif en Français |

---

## Architecture & Structure du Projet

```text
Agence-Marketing-Digital-WPTheme/
├── acf-json/                 # Sync JSON des groupes de champs ACF (Source de vérité DB)
├── assets/
│   ├── css/
│   │   ├── tailwind.css      # CSS compilé final (mis en production)
│   │   └── theme-styles.css  # Styles personnalisés & règles spécifiques
│   └── js/
│       └── theme-scripts.js  # Script principal JS (Animations GSAP, Modales, Accordions)
├── src/
│   └── tailwind.css          # Fichier source CSS Tailwind
├── template-parts/
│   └── layouts/              # Blocs du constructeur de page (hero, picks, guides, etc.)
├── front-page.php            # Routeur dynamique de la page d'accueil
├── functions.php             # Moteur principal du thème (ACF Wrappers, CPTs, Assets, i18n)
├── header.php / footer.php   # En-tête et pied de page réactifs
├── single-blog.php           # Modèle d'article de blog enrichi
├── screenshot.png            # Image de couverture du thème (Admin WP)
└── style.css                 # Fichier d'en-tête et métadonnées du thème
```

### Workflow du Dynamic Page Builder
Le thème utilise un système de dispatch automatique. Tout bloc ajouté dans l'administration via le champ flexible `page_layouts` est automatiquement chargé depuis le fichier correspondant dans `template-parts/layouts/` :

`ACF Layout Slug: foo_section` ➔ `Fichier: template-parts/layouts/foo.php`

---

## Guide de Développement Local

### 1. Prérequis
- **PHP** >= 8.1
- **Node.js** >= 18.x & **npm**
- **WordPress** local (Studio, LocalWP, ou Valet)
- Extension **Advanced Custom Fields Pro** activée

### 2. Installation
Cloner le dépôt dans votre dossier de thèmes WordPress (`wp-content/themes/`) :
```bash
git clone https://github.com/BENYEKHLEF-Anouar/Agence-Marketing-Digital-WPTheme.git agence-marketing-digital
cd agence-marketing-digital
```

Installer les dépendances Node.js :
```bash
npm install
```

### 3. Compilation des Styles Tailwind
Pendant le développement local, lancez le compilateur en mode surveillance (`watch`) :
```bash
npm run watch
```

Pour générer le bundle CSS minifié avant un commit Git :
```bash
npm run build
```

> **Note** : Le fichier compilé `assets/css/tailwind.css` est volontairement versionné dans Git pour garantir un déploiement immédiat sur le serveur sans étape de build distante.

---

## Déploiement Continu (CI/CD)

Le projet intègre un déploiement automatisé via **GitHub Actions** (`.github/workflows/deploy1.yml`). À chaque push sur la branche principale `main`, les fichiers modifiés sont automatiquement synchronisés sur le serveur de production Hostinger.

---

## Auteurs & Crédits

Ce thème a été pensé, conçu et développé par :

- **Ayoub JALYTA** — [*Développeur Full Stack*](https://ayoub-jlita.vercel.app/)
- **Anouar BENYEKHLEF** — [*Développeur Full Stack*](https://anouar-benyekhlef-portfolio.vercel.app/)

---

<div align="center">
  <p>Thème sur mesure pour l'écosystème digital au Maroc.</p>
</div>
