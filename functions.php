<?php
/**
 * v5-digital Theme Functions and Definitions
 */

// Helper to get dynamic domain-based contact email
function v5_digital_get_dynamic_email() {
    $host = parse_url(home_url(), PHP_URL_HOST);
    if ($host) {
        $host = preg_replace('/^www\./i', '', $host);
        return 'contact@' . $host;
    }
    return 'contact@agencemarketingdigital.com';
}

// ----------------------------------------------------
// 1. REGISTER CUSTOM POST TYPES (FR LABELS)
// ----------------------------------------------------

function v5_digital_register_cpts() {
    // Partner Logos
    register_post_type('partner_logo', array(
        'labels' => array(
            'name' => __('Logos Partenaires', 'v5-digital'),
            'singular_name' => __('Logo Partenaire', 'v5-digital'),
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-format-image',
    ));

    // Stat Metrics
    register_post_type('stat_metric', array(
        'labels' => array(
            'name' => __('Statistiques', 'v5-digital'),
            'singular_name' => __('Statistique', 'v5-digital'),
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-chart-bar',
    ));

    // Specialty Hubs
    register_post_type('specialty_hub', array(
        'labels' => array(
            'name' => __('Spécialités', 'v5-digital'),
            'singular_name' => __('Spécialité', 'v5-digital'),
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-category',
    ));

    // Agencies
    register_post_type('agency', array(
        'labels' => array(
            'name' => __('Agences', 'v5-digital'),
            'singular_name' => __('Agence', 'v5-digital'),
            'all_items' => __('Toutes les Agences', 'v5-digital'),
            'add_new' => __('Ajouter', 'v5-digital'),
            'add_new_item' => __('Ajouter une Nouvelle Agence', 'v5-digital'),
            'edit_item' => __('Modifier l\'Agence', 'v5-digital'),
            'new_item' => __('Nouvelle Agence', 'v5-digital'),
            'view_item' => __('Voir l\'Agence', 'v5-digital'),
            'search_items' => __('Rechercher des Agences', 'v5-digital'),
            'not_found' => __('Aucune agence trouvée', 'v5-digital'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'excerpt'),
        'menu_icon' => 'dashicons-businessman',
        'taxonomies' => array('agency_service', 'agency_city'),
    ));

    // Register Services Taxonomy
    register_taxonomy('agency_service', 'agency', array(
        'labels' => array(
            'name' => __('Services', 'v5-digital'),
            'singular_name' => __('Service', 'v5-digital'),
            'all_items' => __('Tous les Services', 'v5-digital'),
            'edit_item' => __('Modifier le Service', 'v5-digital'),
            'view_item' => __('Voir le Service', 'v5-digital'),
            'update_item' => __('Mettre à jour le Service', 'v5-digital'),
            'add_new_item' => __('Ajouter un Nouveau Service', 'v5-digital'),
            'new_item_name' => __('Nom du Nouveau Service', 'v5-digital'),
            'search_items' => __('Rechercher des Services', 'v5-digital'),
        ),
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'service'),
    ));

    // Register Cities Taxonomy
    register_taxonomy('agency_city', 'agency', array(
        'labels' => array(
            'name' => __('Villes', 'v5-digital'),
            'singular_name' => __('Ville', 'v5-digital'),
            'all_items' => __('Toutes les Villes', 'v5-digital'),
            'edit_item' => __('Modifier la Ville', 'v5-digital'),
            'view_item' => __('Voir la Ville', 'v5-digital'),
            'update_item' => __('Mettre à jour la Ville', 'v5-digital'),
            'add_new_item' => __('Ajouter une Nouvelle Ville', 'v5-digital'),
            'new_item_name' => __('Nom de la Nouvelle Ville', 'v5-digital'),
            'search_items' => __('Rechercher des Villes', 'v5-digital'),
        ),
        'public' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'city'),
    ));

    // Testimonials CPT
    register_post_type('testimonial', array(
        'labels' => array(
            'name' => __('Témoignages', 'v5-digital'),
            'singular_name' => __('Témoignage', 'v5-digital'),
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-testimonial',
    ));

    // Blog CPT
    register_post_type('blog', array(
        'labels' => array(
            'name' => __('Articles (Blog)', 'v5-digital'),
            'singular_name' => __('Article', 'v5-digital'),
            'all_items' => __('Tous les Articles', 'v5-digital'),
            'add_new' => __('Ajouter', 'v5-digital'),
            'add_new_item' => __('Ajouter un Nouvel Article', 'v5-digital'),
            'edit_item' => __('Modifier l\'Article', 'v5-digital'),
            'new_item' => __('Nouvel Article', 'v5-digital'),
            'view_item' => __('Voir l\'Article', 'v5-digital'),
            'search_items' => __('Rechercher des Articles', 'v5-digital'),
            'not_found' => __('Aucun article trouvé', 'v5-digital'),
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'author'),
        'menu_icon' => 'dashicons-admin-post',
        'rewrite' => array('slug' => 'blog', 'with_front' => false),
        'show_in_rest' => true,
    ));
}
add_action('init', 'v5_digital_register_cpts');

// ----------------------------------------------------
// 2. REGISTER ACF LOCAL FIELD GROUPS
// ----------------------------------------------------

if (function_exists('acf_add_local_field_group')) {

    // 2.1 CPT Fields: Stat Metrics
    acf_add_local_field_group(array(
        'key' => 'group_stat_fields',
        'title' => 'Détails de la Statistique',
        'fields' => array(
            array(
                'key' => 'field_stat_number',
                'label' => 'Valeur / Nombre',
                'name' => 'stat_number',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_stat_label',
                'label' => 'Description / Libellé',
                'name' => 'stat_label',
                'type' => 'text',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'stat_metric',
                ),
            ),
        ),
    ));

    // 2.2 CPT Fields: Specialty Hubs
    acf_add_local_field_group(array(
        'key' => 'group_specialty_fields',
        'title' => 'Détails de la Spécialité',
        'fields' => array(
            array(
                'key' => 'field_specialty_icon_svg',
                'label' => 'Code SVG de l\'icône',
                'name' => 'icon_svg',
                'type' => 'textarea',
                'rows' => 4,
            ),
            array(
                'key' => 'field_specialty_link_param',
                'label' => 'Paramètre de filtre de lien direct',
                'name' => 'direct_link_parameter',
                'type' => 'text',
                'instructions' => 'e.g. "seo", "ppc", "social", "branding"',
            ),
            array(
                'key' => 'field_specialty_sub_services',
                'label' => 'Liste des sous-services',
                'name' => 'sub_services',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Ajouter un service',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sub_service_name',
                        'label' => 'Nom du service',
                        'name' => 'service_name',
                        'type' => 'text',
                        'required' => 1,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'specialty_hub',
                ),
            ),
        ),
    ));

    // 2.3 CPT Fields: Agencies
    acf_add_local_field_group(array(
        'key' => 'group_agency_fields',
        'title' => 'Détails de l\'Agence',
        'fields' => array(
            array(
                'key' => 'field_agency_logo_text',
                'label' => 'Texte / Initiales du Logo',
                'name' => 'logo_text',
                'type' => 'text',
                'required' => 0,
            ),
            array(
                'key' => 'field_agency_logo_image',
                'label' => 'Image du Logo',
                'name' => 'logo_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'required' => 0,
            ),
            array(
                'key' => 'field_agency_logo_image_url',
                'label' => 'Image du Logo (Lien URL externe)',
                'name' => 'logo_image_url',
                'type' => 'text',
                'required' => 0,
            ),
            array(
                'key' => 'field_agency_rating',
                'label' => 'Note globale',
                'name' => 'rating_value',
                'type' => 'text',
                'default_value' => '4.8',
            ),
            array(
                'key' => 'field_agency_reviews',
                'label' => 'Nombre d\'avis',
                'name' => 'review_count',
                'type' => 'text',
                'default_value' => '10',
            ),
            array(
                'key' => 'field_agency_rank',
                'label' => 'Rang de l\'Agence',
                'name' => 'agency_rank',
                'type' => 'text',
                'default_value' => '1',
            ),
            array(
                'key' => 'field_agency_website',
                'label' => 'Site Web de l\'Agence',
                'name' => 'website',
                'type' => 'url',
                'required' => 0,
                'instructions' => 'Entrez l\'URL complète du site web de l\'agence (ex: https://nom-agence.ma).',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'agency',
                ),
            ),
        ),
    ));

    // 2.4 CPT Fields: Testimonials
    acf_add_local_field_group(array(
        'key' => 'group_testimonial_fields',
        'title' => 'Détails du Témoignage',
        'fields' => array(
            array(
                'key' => 'field_testimonial_rating',
                'label' => 'Note / Étoiles (1-5)',
                'name' => 'rating',
                'type' => 'number',
                'min' => 1,
                'max' => 5,
                'default_value' => 5,
                'required' => 1,
            ),
            array(
                'key' => 'field_testimonial_author_role',
                'label' => 'Rôle / Poste de l\'auteur',
                'name' => 'author_role',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_testimonial_author_image_media',
                'label' => 'Photo de l\'auteur (Médiathèque)',
                'name' => 'author_image_media',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'required' => 0,
            ),
            array(
                'key' => 'field_testimonial_author_image',
                'label' => 'URL de la photo de l\'auteur',
                'name' => 'author_image',
                'type' => 'text',
                'required' => 0,
            ),
            array(
                'key' => 'field_testimonial_hired_agency_name',
                'label' => 'Nom de l\'agence recrutée',
                'name' => 'hired_agency_name',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_testimonial_hired_agency_slug',
                'label' => 'Slug de l\'agence recrutée',
                'name' => 'hired_agency_slug',
                'type' => 'text',
                'required' => 0,
                'instructions' => 'e.g. "rmd", "pixagram", "mediaboost". Laissez vide si non applicable.',
            ),
            array(
                'key' => 'field_testimonial_project',
                'label' => 'Projet réalisé',
                'name' => 'project',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_testimonial_result',
                'label' => 'Résultat obtenu',
                'name' => 'result',
                'type' => 'text',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'testimonial',
                ),
            ),
        ),
    ));

    // 2.4.2 CPT Fields: Partner Logos
    acf_add_local_field_group(array(
        'key' => 'group_partner_logo_fields',
        'title' => 'Détails du Logo Partenaire',
        'fields' => array(
            array(
                'key' => 'field_partner_logo_image_media',
                'label' => 'Image du Logo (Médiathèque)',
                'name' => 'logo_image_media',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'required' => 0,
            ),
            array(
                'key' => 'field_partner_logo_image_url',
                'label' => 'Image du Logo (Lien URL externe)',
                'name' => 'logo_image_url',
                'type' => 'text',
                'required' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'partner_logo',
                ),
            ),
        ),
    ));

    // 2.5 Page layouts: Page Flexible Content Builder
    acf_add_local_field_group(array(
        'key' => 'group_homepage_fields',
        'title' => 'Mises en page de la Page',
        'position' => 'normal',
        'fields' => array(
            array(
                'key' => 'field_page_layouts',
                'label' => 'Mises en page',
                'name' => 'page_layouts',
                'type' => 'flexible_content',
                'button_label' => 'Ajouter une Section',
                'layouts' => array(
                    // Layout 1: Hero
                    'hero_section' => array(
                        'key' => 'layout_hero_section',
                        'name' => 'hero_section',
                        'label' => '[Accueil] Section Hero',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_hero_eyebrow',
                                'label' => 'Texte de surbrillance (Sourcil)',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => '01 · MATCH & DISCOVER',
                            ),
                            array(
                                'key' => 'field_hero_title',
                                'label' => 'Titre de la Hero (HTML)',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Trouvez les <span class="hero-focus-word">meilleures</span> agences de marketing digital au <span class="hero-location-word">Maroc</span>',
                            ),
                            array(
                                'key' => 'field_hero_description',
                                'label' => 'Description lede',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Comparez les agences digitales marocaines grâce à des recherches éditoriales, des scores techniques de vitesse et des avis clients vérifiés.',
                            ),
                            array(
                                'key' => 'field_hero_ctas',
                                'label' => 'Boutons d\'action (CTA)',
                                'name' => 'hero_ctas',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter un bouton',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_hero_cta_text_val',
                                        'label' => 'Texte du bouton',
                                        'name' => 'text',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_link_type',
                                        'label' => 'Type de lien',
                                        'name' => 'link_type',
                                        'type' => 'select',
                                        'choices' => array(
                                            'page' => 'Lien vers page interne',
                                            'url' => 'Lien URL personnalisé',
                                        ),
                                        'default_value' => 'page',
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_url',
                                        'label' => 'URL personnalisée',
                                        'name' => 'url',
                                        'type' => 'text',
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_hero_cta_link_type',
                                                    'operator' => '==',
                                                    'value' => 'url',
                                                ),
                                            ),
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_page',
                                        'label' => 'Page interne',
                                        'name' => 'page',
                                        'type' => 'page_link',
                                        'allow_null' => 1,
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_hero_cta_link_type',
                                                    'operator' => '==',
                                                    'value' => 'page',
                                                ),
                                            ),
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_style',
                                        'label' => 'Style du bouton',
                                        'name' => 'style',
                                        'type' => 'select',
                                        'choices' => array(
                                            'primary' => 'Bouton Principal (Bleu)',
                                            'secondary' => 'Bouton Secondaire (Blanc)',
                                            'custom' => 'Couleurs personnalisées',
                                        ),
                                        'default_value' => 'primary',
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_bg_color',
                                        'label' => 'Couleur de fond',
                                        'name' => 'bg_color',
                                        'type' => 'color_picker',
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_hero_cta_style',
                                                    'operator' => '==',
                                                    'value' => 'custom',
                                                ),
                                            ),
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_text_color',
                                        'label' => 'Couleur du texte',
                                        'name' => 'text_color',
                                        'type' => 'color_picker',
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_hero_cta_style',
                                                    'operator' => '==',
                                                    'value' => 'custom',
                                                ),
                                            ),
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_hero_cta_icon',
                                        'label' => 'Icône Lucide',
                                        'name' => 'icon',
                                        'type' => 'select',
                                        'choices' => array(
                                            'none' => 'Pas d\'icône',
                                            'sparkles' => 'Étincelles (sparkles)',
                                            'home' => 'Maison (home)',
                                            'arrow-right' => 'Flèche (arrow-right)',
                                            'mail' => 'Enveloppe (mail)',
                                        ),
                                        'default_value' => 'none',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_hero_social_proof_1',
                                'label' => 'Preuve sociale 1',
                                'name' => 'social_proof_1',
                                'type' => 'text',
                                'default_value' => '150+ agences évaluées',
                            ),
                            array(
                                'key' => 'field_hero_social_proof_2',
                                'label' => 'Preuve sociale 2',
                                'name' => 'social_proof_2',
                                'type' => 'text',
                                'default_value' => 'Référencement 100% éditorial',
                            ),
                            array(
                                'key' => 'field_hero_stats',
                                'label' => 'Statistiques du Hero',
                                'name' => 'hero_stats',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'button_label' => 'Ajouter une statistique',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_hero_stat_number',
                                        'label' => 'Nombre / Chiffre',
                                        'name' => 'number',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_hero_stat_label',
                                        'label' => 'Étiquette / Libellé',
                                        'name' => 'label',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Layout 1.5: Search Filter
                    'search_filter_section' => array(
                        'key' => 'layout_search_filter_section',
                        'name' => 'search_filter_section',
                        'label' => '[Accueil] Barre de Recherche',
                        'display' => 'block',
                        'sub_fields' => array(),
                    ),
                    // Layout 2: Logos Band
                    'logos_band_section' => array(
                        'key' => 'layout_logos_band_section',
                        'name' => 'logos_band_section',
                        'label' => '[Commun] Bandeau des Logos',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_logos_title',
                                'label' => 'Titre de la section',
                                'name' => 'section_title',
                                'type' => 'text',
                                'default_value' => 'Annuaire de confiance utilisé par les acheteurs de :',
                            ),
                        ),
                    ),
                    // Layout 3: Challenge Grid
                    'challenge_section' => array(
                        'key' => 'layout_challenge_section',
                        'name' => 'challenge_section',
                        'label' => '[Accueil] Section Le Défi',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_challenge_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Le Défi',
                            ),
                            array(
                                'key' => 'field_challenge_title',
                                'label' => 'Titre (HTML)',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'La plupart des entreprises choisissent la <span class="challenge-focus-word">mauvaise agence</span> en <span class="challenge-time-word">48 heures</span>.',
                            ),
                            array(
                                'key' => 'field_challenge_desc',
                                'label' => 'Description de sous-titre',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Le marché des agences de marketing digital au Maroc est encombré, bruyant et opaque. Les beaux sites cachent souvent un travail médiocre. Les témoignages élogieux sont rarement vérifiés.',
                            ),
                            array(
                                'key' => 'field_challenge_cards',
                                'label' => 'Cartes de points faibles',
                                'name' => 'cards',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter une Carte',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_challenge_card_title',
                                        'label' => 'Titre de la carte',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_challenge_card_desc',
                                        'label' => 'Description',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_challenge_card_icon',
                                        'label' => 'Icône SVG',
                                        'name' => 'icon_svg',
                                        'type' => 'textarea',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_challenge_quote_text',
                                'label' => 'Texte de la Citation',
                                'name' => 'quote_text',
                                'type' => 'textarea',
                            ),
                            array(
                                'key' => 'field_challenge_quote_author',
                                'label' => 'Auteur de la citation',
                                'name' => 'quote_author',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_challenge_quote_role',
                                'label' => 'Rôle de l\'auteur',
                                'name' => 'quote_role',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_challenge_quote_verified',
                                'label' => 'Tag vérifié',
                                'name' => 'quote_verified',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_challenge_quote_scope',
                                'label' => 'Tag d\'étendue du projet',
                                'name' => 'quote_scope',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_challenge_quote_image_media',
                                'label' => 'Image de l\'auteur (Médiathèque)',
                                'name' => 'quote_image_media',
                                'type' => 'image',
                                'return_format' => 'url',
                                'preview_size' => 'thumbnail',
                                'library' => 'all',
                                'required' => 0,
                            ),
                            array(
                                'key' => 'field_challenge_quote_image',
                                'label' => 'Image de l\'auteur (Lien URL externe)',
                                'name' => 'quote_image',
                                'type' => 'text',
                                'required' => 0,
                            ),
                        ),
                    ),
                    // Layout 4: Approach
                    'approach_section' => array(
                        'key' => 'layout_approach_section',
                        'name' => 'approach_section',
                        'label' => '[Accueil] Section Notre Approche',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_app_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Notre Approche',
                            ),
                            array(
                                'key' => 'field_app_title',
                                'label' => 'Titre (HTML)',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Une <span class="approach-focus-word">meilleure façon</span> de <span class="approach-action-word">choisir</span> une agence.',
                            ),
                            array(
                                'key' => 'field_app_desc',
                                'label' => 'Description de sous-titre',
                                'name' => 'description',
                                'type' => 'text',
                                'default_value' => 'Nous faisons les recherches pour que vous puissiez vous concentrer sur la décision.',
                            ),
                            array(
                                'key' => 'field_app_points',
                                'label' => 'Étapes d\'approche',
                                'name' => 'points',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter une étape',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_app_point_number',
                                        'label' => 'Numéro',
                                        'name' => 'number',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_app_point_title',
                                        'label' => 'Titre',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_app_point_desc',
                                        'label' => 'Description',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'required' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Layout 5: Outcomes (Reviews Grid)
                    'outcomes_section' => array(
                        'key' => 'layout_outcomes_section',
                        'name' => 'outcomes_section',
                        'label' => '[Accueil] Section Témoignages & Avis',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_out_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Résultats clients vérifiés',
                            ),
                            array(
                                'key' => 'field_out_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Ce que disent les équipes après avoir choisi via Agence Marketing Digital',
                            ),
                            array(
                                'key' => 'field_out_desc',
                                'label' => 'Description',
                                'name' => 'description',
                                'type' => 'textarea',
                            ),
                            array(
                                'key' => 'field_out_metrics',
                                'label' => 'Données clés (Compteurs)',
                                'name' => 'metrics',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'button_label' => 'Ajouter une métrique',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_out_metric_val',
                                        'label' => 'Valeur (e.g. 92%)',
                                        'name' => 'value',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_out_metric_lbl',
                                        'label' => 'Libellé',
                                        'name' => 'label',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_out_reviews',
                                'label' => 'Grille d\'avis clients',
                                'name' => 'reviews',
                                'type' => 'relationship',
                                'post_type' => array('testimonial'),
                                'filters' => array('search'),
                                'elements' => array('title'),
                                'max' => 6,
                                'return_format' => 'id',
                            ),
                        ),
                    ),
                    // Layout 6: Stats Band (CPT Queries)
                    'stats_band_section' => array(
                        'key' => 'layout_stats_band_section',
                        'name' => 'stats_band_section',
                        'label' => '[Commun] Bandeau des Statistiques',
                        'display' => 'block',
                    ),
                    // Layout 7: Editor's Picks
                    'picks_section' => array(
                        'key' => 'layout_picks_section',
                        'name' => 'picks_section',
                        'label' => '[Accueil] Sélection de l\'Éditeur',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_picks_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => "Choix de l'Éditeur",
                            ),
                            array(
                                'key' => 'field_picks_title',
                                'label' => 'Titre de la section',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Les agences les plus performantes ce mois-ci.',
                            ),
                            array(
                                'key' => 'field_picks_link_text',
                                'label' => 'Texte du lien méthodologique',
                                'name' => 'link_text',
                                'type' => 'text',
                                'default_value' => 'Comment nous évaluons',
                            ),
                            array(
                                'key' => 'field_picks_link_url',
                                'label' => 'URL du lien méthodologique',
                                'name' => 'link_url',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_picks_selected_agencies',
                                'label' => 'Agences à afficher (Optionnel)',
                                'name' => 'selected_agencies',
                                'type' => 'relationship',
                                'post_type' => array('agency'),
                                'filters' => array('search'),
                                'elements' => array('featured_image'),
                                'return_format' => 'id',
                                'instructions' => 'Sélectionnez manuellement les agences à afficher. Laissez vide pour recommander automatiquement le top 3 trié par rang.',
                            ),
                        ),
                    ),
                    // Layout 8: Specialties Grid
                    'specialties_section' => array(
                        'key' => 'layout_specialties_section',
                        'name' => 'specialties_section',
                        'label' => '[Accueil] Grille des Spécialités',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_spec_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Spécialités',
                            ),
                            array(
                                'key' => 'field_spec_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Trouvez votre besoin exact.',
                            ),
                            array(
                                'key' => 'field_spec_desc',
                                'label' => 'Description de sous-titre',
                                'name' => 'description',
                                'type' => 'text',
                                'default_value' => 'Explorez notre annuaire par canal marketing ou par spécialité.',
                            ),
                            array(
                                'key' => 'field_spec_cta',
                                'label' => 'Texte du bouton CTA',
                                'name' => 'view_all_cta_text',
                                'type' => 'text',
                                'default_value' => 'Voir tous les classements',
                            ),
                        ),
                    ),
                    // Layout 9: Guides List
                    'guides_section' => array(
                        'key' => 'layout_guides_section',
                        'name' => 'guides_section',
                        'label' => '[Blog] Section Guides & Analyses',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_guides_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Éditorial',
                            ),
                            array(
                                'key' => 'field_guides_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Derniers guides & analyses.',
                            ),
                            array(
                                'key' => 'field_guides_link_text',
                                'label' => 'Texte du lien de blog',
                                'name' => 'link_text',
                                'type' => 'text',
                                'default_value' => 'Tous les articles',
                            ),
                            array(
                                'key' => 'field_guides_link_url',
                                'label' => 'Lien de blog',
                                'name' => 'link_url',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_guides_posts',
                                'label' => 'Sélectionner des articles à afficher',
                                'name' => 'posts',
                                'type' => 'post_object',
                                'post_type' => array('blog'),
                                'allow_null' => 1,
                                'multiple' => 1,
                                'return_format' => 'id',
                                'ui' => 1,
                            ),
                        ),
                    ),
                    // Layout 10: Blog Posts Grid
                    'blog_posts_grid_section' => array(
                        'key'     => 'layout_blog_posts_grid_section',
                        'name'    => 'blog_posts_grid_section',
                        'label'   => '[Blog] Grille d\'Articles Blog',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key'           => 'field_blog_grid_title',
                                'label'         => 'Titre de la section héro',
                                'name'          => 'grid_title',
                                'type'          => 'text',
                                'default_value' => 'Intelligence <span class="quiet">Agences.</span>',
                                'instructions'  => 'Vous pouvez utiliser des balises HTML simples comme <span>.',
                            ),
                            array(
                                'key'           => 'field_blog_grid_subtitle',
                                'label'         => 'Sous-titre / description',
                                'name'          => 'grid_subtitle',
                                'type'          => 'textarea',
                                'default_value' => 'Notes techniques sur les agences marocaines, la visibilité organique, Core Web Vitals, et les preuves qui séparent les vrais opérateurs SEO des argumentaires commerciaux polis.',
                                'rows'          => 3,
                            ),
                            array(
                                'key'           => 'field_blog_grid_show_filters',
                                'label'         => 'Afficher les filtres par catégorie',
                                'name'          => 'show_filters',
                                'type'          => 'true_false',
                                'default_value' => 1,
                                'ui'            => 1,
                            ),
                            array(
                                'key'           => 'field_blog_grid_show_newsletter',
                                'label'         => 'Afficher la section newsletter',
                                'name'          => 'show_newsletter',
                                'type'          => 'true_false',
                                'default_value' => 1,
                                'ui'            => 1,
                            ),
                            array(
                                'key'           => 'field_blog_grid_posts_per_page',
                                'label'         => 'Nombre d\'articles affichés',
                                'name'          => 'posts_per_page',
                                'type'          => 'number',
                                'default_value' => -1,
                                'min'           => -1,
                                'max'           => 100,
                                'instructions'  => 'Utilisez -1 pour afficher tous les articles.',
                            ),
                        ),
                    ),
                    // Layout 11: Footer CTA
                    'footer_cta_section' => array(
                        'key' => 'layout_footer_cta_section',
                        'name' => 'footer_cta_section',
                        'label' => '[Commun] Section CTA Bas de Page',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_fcta_eyebrow',
                                'label' => 'Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Matchmaker',
                            ),
                            array(
                                'key' => 'field_fcta_title',
                                'label' => 'Titre de la carte',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Prêt à trouver la bonne agence digitale ?',
                            ),
                            array(
                                'key' => 'field_fcta_desc',
                                'label' => 'Description',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Évitez les présentations commerciales génériques. Décrivez votre projet en 2 minutes et laissez nos audits indépendants identifier le partenaire idéal.',
                            ),
                            array(
                                'key' => 'field_fcta_primary_btn',
                                'label' => 'Bouton principal',
                                'name' => 'primary_cta_text',
                                'type' => 'text',
                                'default_value' => 'Lancer le Matchmaker',
                            ),
                            array(
                                'key' => 'field_fcta_secondary_btn',
                                'label' => 'Bouton secondaire',
                                'name' => 'secondary_cta_text',
                                'type' => 'text',
                                'default_value' => 'Explorer l\'annuaire',
                            ),
                        ),
                    ),
                    // Layout 12: Common Hero
                    'common_hero_section' => array(
                        'key' => 'layout_common_hero_section',
                        'name' => 'common_hero_section',
                        'label' => '[Commun] Section Hero',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_chero_home_text',
                                'label' => 'Texte lien d\'accueil',
                                'name' => 'home_text',
                                'type' => 'text',
                                'default_value' => 'Accueil',
                            ),
                            array(
                                'key' => 'field_chero_current_text',
                                'label' => 'Texte page active (Fil d\'ariane)',
                                'name' => 'current_text',
                                'type' => 'text',
                                'default_value' => 'Contact',
                            ),
                            array(
                                'key' => 'field_chero_title',
                                'label' => 'Titre de la section (HTML)',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Contactez-<span class="quiet">nous.</span>',
                            ),
                            array(
                                'key' => 'field_chero_description',
                                'label' => 'Description lede',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Des questions sur les données des agences, des corrections, des avis ou des recherches éditoriales ? Envoyez-nous les détails et notre équipe vous répondra.',
                            ),
                            array(
                                'key' => 'field_chero_cta_text',
                                'label' => 'Texte du bouton CTA',
                                'name' => 'cta_text',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_chero_cta_link',
                                'label' => 'Lien du bouton CTA',
                                'name' => 'cta_link',
                                'type' => 'text',
                            ),
                        ),
                    ),
                    // Layout 13: Contact Form & Info
                    'contact_form_section' => array(
                        'key' => 'layout_contact_form_section',
                        'name' => 'contact_form_section',
                        'label' => '[Contact] Formulaire & Infos',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_cform_title',
                                'label' => 'Titre du formulaire',
                                'name' => 'form_title',
                                'type' => 'text',
                                'default_value' => 'Envoyer un Message',
                            ),
                            array(
                                'key' => 'field_cform_desc',
                                'label' => 'Description du formulaire',
                                'name' => 'form_desc',
                                'type' => 'textarea',
                                'default_value' => 'Utilisez ce formulaire pour les demandes de référencement, les corrections, les questions des acheteurs ou les notes de partenariat.',
                            ),
                            array(
                                'key' => 'field_cform_office_title',
                                'label' => 'Titre Siège Social',
                                'name' => 'office_title',
                                'type' => 'text',
                                'default_value' => 'Siège Social',
                            ),
                            array(
                                'key' => 'field_cform_office_address',
                                'label' => 'Adresse Siège Social',
                                'name' => 'office_address',
                                'type' => 'text',
                                'default_value' => '8 rue de la Paix, 75002 Paris, France',
                            ),
                            array(
                                'key' => 'field_cform_office_city',
                                'label' => 'Ville Siège Social',
                                'name' => 'office_city',
                                'type' => 'text',
                                'default_value' => 'Casablanca, Maroc',
                            ),
                            array(
                                'key' => 'field_cform_email',
                                'label' => 'Adresse Email de contact',
                                'name' => 'email',
                                'type' => 'text',
                                'default_value' => v5_digital_get_dynamic_email(),
                            ),
                            array(
                                'key' => 'field_cform_guarantee_title',
                                'label' => 'Titre Bloc Garantie',
                                'name' => 'guarantee_title',
                                'type' => 'text',
                                'default_value' => 'Garantie d\'Indépendance',
                            ),
                            array(
                                'key' => 'field_cform_guarantee_desc',
                                'label' => 'Description Bloc Garantie',
                                'name' => 'guarantee_desc',
                                'type' => 'textarea',
                                'default_value' => 'Nous n\'acceptons pas de placements payants ni de classements sponsorisés. Les agences qui souhaitent être référencées passent par notre processus d\'évaluation standard et indépendant.',
                            ),
                        ),
                    ),
                    // Layout 14: Newsletter CTA
                    'newsletter_cta_section' => array(
                        'key' => 'layout_newsletter_cta_section',
                        'name' => 'newsletter_cta_section',
                        'label' => '[Commun] Section Newsletter',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_nl_section_label',
                                'label' => 'Surligné / Sourcil',
                                'name' => 'section_label',
                                'type' => 'text',
                                'default_value' => 'Newsletter · Gratuite',
                            ),
                            array(
                                'key' => 'field_nl_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Recevez nos analyses dans votre boîte mail.',
                            ),
                            array(
                                'key' => 'field_nl_description',
                                'label' => 'Description / Sous-titre',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Chaque quinzaine : un audit d\'agence, un signal SEO à surveiller, une sélection de ressources pour les fondateurs et directeurs marketing au Maroc.',
                            ),
                            array(
                                'key' => 'field_nl_placeholder',
                                'label' => 'Texte de saisie (Placeholder)',
                                'name' => 'email_placeholder',
                                'type' => 'text',
                                'default_value' => 'votre@email.com',
                            ),
                            array(
                                'key' => 'field_nl_btn_text',
                                'label' => 'Texte du bouton',
                                'name' => 'button_text',
                                'type' => 'text',
                                'default_value' => 'S\'abonner',
                            ),
                            array(
                                'key' => 'field_nl_footer_text',
                                'label' => 'Texte de bas de formulaire',
                                'name' => 'footer_text',
                                'type' => 'text',
                                'default_value' => 'Aucun spam · Désabonnement en 1 clic',
                            ),
                        ),
                    ),
                    // Layout 15: Methodology Process Validation
                    'methodology_process_section' => array(
                        'key' => 'layout_methodology_process_section',
                        'name' => 'methodology_process_section',
                        'label' => '[Méthodologie] Processus de Validation',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_mproc_label',
                                'label' => 'Surligné / Sourcil',
                                'name' => 'section_label',
                                'type' => 'text',
                                'default_value' => '01 / Processus de Validation',
                            ),
                            array(
                                'key' => 'field_mproc_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Quatre étapes. Un seul standard.',
                            ),
                            array(
                                'key' => 'field_mproc_desc',
                                'label' => 'Description / Sous-titre',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'La même séquence est appliquée à chaque agence répertoriée, qu\'elle ait postulé ou été découverte par notre équipe.',
                            ),
                            array(
                                'key' => 'field_mproc_stages',
                                'label' => 'Étapes du processus',
                                'name' => 'stages',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter une étape',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_mproc_stage_num',
                                        'label' => 'Numéro (e.g. 01)',
                                        'name' => 'stage_num',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_mproc_stage_title',
                                        'label' => 'Titre de l\'étape',
                                        'name' => 'stage_title',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_mproc_stage_desc',
                                        'label' => 'Description',
                                        'name' => 'stage_description',
                                        'type' => 'textarea',
                                    ),
                                    array(
                                        'key' => 'field_mproc_stage_tags',
                                        'label' => 'Tags (séparés par des virgules)',
                                        'name' => 'stage_tags',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_mproc_stage_image',
                                        'label' => 'Illustration personnalisée (Image/SVG)',
                                        'name' => 'stage_image',
                                        'type' => 'image',
                                        'return_format' => 'url',
                                        'preview_size' => 'medium',
                                    ),
                                    array(
                                        'key' => 'field_mproc_stage_icon',
                                        'label' => 'Icône Lucide alternative (e.g. check-circle)',
                                        'name' => 'stage_icon',
                                        'type' => 'text',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Layout 16: Methodology Evidence Matrix
                    'methodology_evidence_section' => array(
                        'key' => 'layout_methodology_evidence_section',
                        'name' => 'methodology_evidence_section',
                        'label' => '[Méthodologie] Carte des Preuves',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_mevid_label',
                                'label' => 'Surligné / Sourcil',
                                'name' => 'section_label',
                                'type' => 'text',
                                'default_value' => '02 / Carte des Preuves',
                            ),
                            array(
                                'key' => 'field_mevid_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Les affirmations nécessitent des preuves.',
                            ),
                            array(
                                'key' => 'field_mevid_rows',
                                'label' => 'Lignes de preuves',
                                'name' => 'evidence_rows',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter une ligne',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_mevid_row_title',
                                        'label' => 'Titre de la ligne',
                                        'name' => 'row_title',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_mevid_row_desc',
                                        'label' => 'Description',
                                        'name' => 'row_description',
                                        'type' => 'textarea',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Layout 17: Methodology Monitor Continu
                    'methodology_monitor_section' => array(
                        'key' => 'layout_methodology_monitor_section',
                        'name' => 'methodology_monitor_section',
                        'label' => '[Méthodologie] Suivi Continu',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_mmon_label',
                                'label' => 'Surligné / Sourcil',
                                'name' => 'section_label',
                                'type' => 'text',
                                'default_value' => '03 / Suivi Continu',
                            ),
                            array(
                                'key' => 'field_mmon_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Nous contrôlons régulièrement les agences.',
                            ),
                            array(
                                'key' => 'field_mmon_desc',
                                'label' => 'Description / Sous-titre',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Une agence classée peut monter, descendre ou être retirée si de nouvelles analyses indiquent une modification de sa qualité.',
                            ),
                            array(
                                'key' => 'field_mmon_steps',
                                'label' => 'Étapes de suivi',
                                'name' => 'steps',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter une étape',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_mmon_step_icon',
                                        'label' => 'Nom de l\'icône Lucide (e.g. calendar-sync)',
                                        'name' => 'step_icon',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_mmon_step_title',
                                        'label' => 'Titre',
                                        'name' => 'step_title',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_mmon_step_desc',
                                        'label' => 'Description',
                                        'name' => 'step_description',
                                        'type' => 'textarea',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Layout 18: About Grid Section
                    'about_grid_section' => array(
                        'key' => 'layout_about_grid_section',
                        'name' => 'about_grid_section',
                        'label' => '[À Propos] Grille de Présentation',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_agrid_cards',
                                'label' => 'Cartes de présentation',
                                'name' => 'cards',
                                'type' => 'repeater',
                                'layout' => 'block',
                                'button_label' => 'Ajouter une carte',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_agrid_card_icon',
                                        'label' => 'Icône Lucide (e.g. search-check, shield-check, users)',
                                        'name' => 'card_icon',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_agrid_card_title',
                                        'label' => 'Titre de la carte',
                                        'name' => 'card_title',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_agrid_card_desc',
                                        'label' => 'Description',
                                        'name' => 'card_description',
                                        'type' => 'textarea',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Layout 19: About CTA Section
                    'about_cta_section' => array(
                        'key' => 'layout_about_cta_section',
                        'name' => 'about_cta_section',
                        'label' => '[À Propos] Appels à l\'Action (CTA)',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_acta_eyebrow',
                                'label' => 'Surligné / Sourcil',
                                'name' => 'eyebrow',
                                'type' => 'text',
                                'default_value' => 'Processus & Inscription',
                            ),
                            array(
                                'key' => 'field_acta_title',
                                'label' => 'Titre principal',
                                'name' => 'title',
                                'type' => 'text',
                                'default_value' => 'Vous voulez comprendre notre processus ?',
                            ),
                            array(
                                'key' => 'field_acta_desc',
                                'label' => 'Description / Paragraphe',
                                'name' => 'description',
                                'type' => 'textarea',
                                'default_value' => 'Consultez la méthodologie pour voir les critères de recherche, ou contactez l\'équipe éditoriale pour une correction ou une demande d\'ajout.',
                            ),
                            array(
                                'key' => 'field_acta_primary_text',
                                'label' => 'Texte Bouton Principal',
                                'name' => 'primary_text',
                                'type' => 'text',
                                'default_value' => 'Lire la méthodologie',
                            ),
                            array(
                                'key' => 'field_acta_primary_link',
                                'label' => 'Lien Bouton Principal',
                                'name' => 'primary_link',
                                'type' => 'text',
                                'default_value' => '/methodologie/',
                            ),
                            array(
                                'key' => 'field_acta_secondary_text',
                                'label' => 'Texte Bouton Secondaire',
                                'name' => 'secondary_text',
                                'type' => 'text',
                                'default_value' => 'Nous contacter',
                            ),
                            array(
                                'key' => 'field_acta_secondary_link',
                                'label' => 'Lien Bouton Secondaire',
                                'name' => 'secondary_link',
                                'type' => 'text',
                                'default_value' => '/contact/',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
    ));

    // 2.6 CPT Fields: Blog Metadata
    acf_add_local_field_group(array(
        'key' => 'group_blog_meta',
        'title' => 'Métadonnées de l\'Article',
        'fields' => array(
            array(
                'key' => 'field_blog_badge',
                'label' => 'Badge / Catégorie',
                'name' => 'badge',
                'type' => 'text',
                'required' => 1,
                'default_value' => 'Guide',
                'instructions' => 'Exemples : Classement, Guide, Audit SEO, Comparatif',
            ),
            array(
                'key' => 'field_blog_read_time',
                'label' => 'Temps de lecture',
                'name' => 'read_time',
                'type' => 'text',
                'required' => 1,
                'default_value' => '5 min de lecture',
                'instructions' => 'Exemple : 8 min de lecture, 6 min read',
            ),
            array(
                'key' => 'field_blog_author_name',
                'label' => 'Nom de l\'auteur',
                'name' => 'author_name',
                'type' => 'text',
                'required' => 0,
                'instructions' => 'Nom de l\'auteur affiché. Si vide, affichera l\'auteur WordPress de l\'article.',
            ),
            array(
                'key' => 'field_blog_cover_image_url',
                'label' => 'Image de couverture (URL externe)',
                'name' => 'cover_image_url',
                'type' => 'text',
                'required' => 0,
                'instructions' => 'Optionnel. URL d\'une image externe (ex. Unsplash).',
            ),
            array(
                'key' => 'field_blog_cover_image_media',
                'label' => 'Image de couverture (Médiathèque)',
                'name' => 'cover_image_media',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 0,
                'instructions' => 'Optionnel. Image sélectionnée depuis la médiathèque locale. Prioritaire sur l\'URL externe.',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'blog',
                ),
            ),
        ),
    ));

    // 2.7 CPT Fields: Blog Content (Flexible Content)
    acf_add_local_field_group(array(
        'key' => 'group_blog_content',
        'title' => 'Contenu Flexible de l\'Article',
        'fields' => array(
            array(
                'key' => 'field_blog_layouts',
                'label' => 'Mises en page de l\'Article',
                'name' => 'blog_layouts',
                'type' => 'flexible_content',
                'button_label' => 'Ajouter un bloc',
                'layouts' => array(
                    // WYSIWYG Editor Block
                    'wysiwyg_block' => array(
                        'key' => 'layout_blog_wysiwyg',
                        'name' => 'wysiwyg_block',
                        'label' => 'Contenu Éditeur (Texte, Images, etc.)',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_blog_wysiwyg_content',
                                'label' => 'Contenu',
                                'name' => 'content',
                                'type' => 'wysiwyg',
                                'required' => 1,
                                'tabs' => 'all',
                                'toolbar' => 'full',
                                'media_upload' => 1,
                            ),
                        ),
                    ),
                    // Heading Block
                    'heading_block' => array(
                        'key' => 'layout_blog_heading',
                        'name' => 'heading_block',
                        'label' => 'Titre de Section (H2 / H3)',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_blog_heading_text',
                                'label' => 'Texte du titre',
                                'name' => 'heading_text',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_blog_heading_level',
                                'label' => 'Niveau du titre',
                                'name' => 'heading_level',
                                'type' => 'select',
                                'choices' => array(
                                    'h2' => 'En-tête H2',
                                    'h3' => 'En-tête H3',
                                ),
                                'default_value' => 'h2',
                                'required' => 1,
                            ),
                        ),
                    ),
                    // Embedded Listings / Recommended Agencies Block
                    'agency_reviews_block' => array(
                        'key' => 'layout_blog_reviews',
                        'name' => 'agency_reviews_block',
                        'label' => 'Analyses Éditoriales (Sélection d\'Agences)',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_blog_reviews_list',
                                'label' => 'Liste des agences recommandées',
                                'name' => 'reviews_list',
                                'type' => 'repeater',
                                'layout' => 'row',
                                'button_label' => 'Ajouter une analyse d\'agence',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_blog_review_agency',
                                        'label' => 'Agence',
                                        'name' => 'agency',
                                        'type' => 'post_object',
                                        'post_type' => array('agency'),
                                        'return_format' => 'id',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_blog_review_rank',
                                        'label' => 'Rang de recommandation',
                                        'name' => 'rank',
                                        'type' => 'number',
                                        'default_value' => 1,
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_blog_review_badge',
                                        'label' => 'Badge d\'analyse',
                                        'name' => 'badge',
                                        'type' => 'text',
                                        'default_value' => 'Leader SEO (Score : 98/100)',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_blog_review_desc',
                                        'label' => 'Description / Pourquoi ce choix',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'rows' => 3,
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_blog_review_link_text',
                                        'label' => 'Texte du lien d\'action',
                                        'name' => 'link_text',
                                        'type' => 'text',
                                        'default_value' => 'voir le site de l\'agence →',
                                        'required' => 1,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'blog',
                ),
            ),
        ),
    ));
}

// ----------------------------------------------------
// 3. ENQUEUE THEME SCRIPTS AND STYLES
// ----------------------------------------------------

function v5_digital_enqueue_assets() {
    // Custom theme stylesheet
    wp_enqueue_style(
        'v5-digital-styles',
        get_template_directory_uri() . '/assets/css/theme-styles.css',
        array(),
        '1.0.0'
    );

    // Custom theme javascript enqueued in footer
    wp_enqueue_script(
        'v5-digital-scripts',
        get_template_directory_uri() . '/assets/js/theme-scripts.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'v5_digital_enqueue_assets');

// ----------------------------------------------------
// 4. THEME SWITCH AUTOMATION (INITIALIZE PAGES & CPT DATA)
// ----------------------------------------------------

function v5_digital_setup_theme_content() {
    // 4.1 Set up Polylang default language (French)
    if (class_exists('PLL_Model')) {
        $model = PLL();
        $languages = $model->model->get_languages_list();
        
        if (empty($languages)) {
            // Register French language
            $model->model->add_language(array(
                'name'       => 'Français',
                'locale'     => 'fr_FR',
                'slug'       => 'fr',
                'rtl'        => 0,
                'term_group' => 0,
            ));
            
            // Set options
            $options = get_option('polylang');
            if (!is_array($options)) $options = array();
            $options['default_lang'] = 'fr';
            update_option('polylang', $options);
        }
    }

    // 4.1.2 PURGE EXISTING SEEDED CONTENT TO AVOID CONFLICTS
    // Note: 'blog' CPT posts are intentionally excluded — they are user-managed
    $posts_to_purge = get_posts(array(
        'post_type' => array('partner_logo', 'stat_metric', 'specialty_hub', 'agency', 'testimonial'),
        'numberposts' => -1,
        'post_status' => 'any',
        'lang' => '', // Purge all languages to avoid duplicate items
    ));
    foreach ($posts_to_purge as $p) {
        wp_delete_post($p->ID, true);
    }

    $taxonomies = array('agency_service', 'agency_city');
    foreach ($taxonomies as $tax) {
        $terms = get_terms(array(
            'taxonomy' => $tax,
            'hide_empty' => false,
        ));
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, $tax);
            }
        }
    }

    // 4.2 Setup static homepage settings
    $homepage_title = 'Accueil';
    $homepage = get_page_by_title($homepage_title);

    if (!$homepage) {
        $homepage_id = wp_insert_post(array(
            'post_title'   => $homepage_title,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));
    } else {
        $homepage_id = $homepage->ID;
        if (isset($_GET['force_seed'])) {
            wp_update_post(array(
                'ID'           => $homepage_id,
                'post_content' => '',
            ));
        }
    }

    if (function_exists('pll_set_post_language')) {
        pll_set_post_language($homepage_id, 'fr');
    }

    // Assign front page setting
    update_option('show_on_front', 'page');
    update_option('page_on_front', $homepage_id);

    // Seed standard pages — blog is now a static Page (not a CPT archive)
    $pages_to_seed = array(
        'blog'         => 'Blog',
        'annuaire'     => 'Annuaire',
        'about'        => 'À propos',
        'methodologie' => 'Méthodologie',
        'contact'      => 'Contact',
    );
    $seeded_page_ids = array();
    $seeded_page_ids['accueil'] = $homepage_id;

    foreach ($pages_to_seed as $slug => $title) {
        $p_obj = get_page_by_path($slug);
        if (!$p_obj) {
            $p_id = wp_insert_post(array(
                'post_title'  => $title,
                'post_name'   => $slug,
                'post_content'=> '',
                'post_status' => 'publish',
                'post_type'   => 'page',
            ));
        } else {
            $p_id = $p_obj->ID;
            if (isset($_GET['force_seed'])) {
                wp_update_post(array(
                    'ID'           => $p_id,
                    'post_content' => '',
                ));
            }
        }
        if ($p_id) {
            $seeded_page_ids[$slug] = $p_id;
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($p_id, 'fr');
            }
            // Seed specific layout templates for core pages
            if (function_exists('update_field')) {
                $existing_layouts = get_field('field_page_layouts', $p_id);
                if (empty($existing_layouts) || isset($_GET['force_seed'])) {
                    if ($slug === 'blog') {
                        update_field('field_page_layouts', array(
                            array(
                                'acf_fc_layout' => 'common_hero_section',
                                'home_text'     => 'Accueil',
                                'current_text'  => 'Blog',
                                'title'         => 'Intelligence <span class="quiet">Agences.</span>',
                                'description'   => 'Notes techniques sur les agences marocaines, la visibilité organique, Core Web Vitals, et les preuves qui séparent les vrais opérateurs SEO des argumentaires commerciaux polis.',
                                'cta_text'      => '',
                                'cta_link'      => '',
                            ),
                            array(
                                'acf_fc_layout'   => 'blog_posts_grid_section',
                                'grid_title'      => 'Intelligence <span class="quiet">Agences.</span>',
                                'grid_subtitle'   => 'Notes techniques sur les agences marocaines, la visibilité organique, Core Web Vitals, et les preuves qui séparent les vrais opérateurs SEO des argumentaires commerciaux polis.',
                                'show_filters'    => 1,
                                'show_newsletter' => 1,
                                'posts_per_page'  => -1,
                            ),
                            array(
                                'acf_fc_layout'   => 'newsletter_cta_section',
                                'section_label'   => 'Newsletter · Gratuite',
                                'title'           => 'Recevez nos analyses dans votre boîte mail.',
                                'description'     => 'Chaque quinzaine : un audit d\'agence, un signal SEO à surveiller, une sélection de ressources pour les fondateurs et directeurs marketing au Maroc.',
                                'email_placeholder' => 'votre@email.com',
                                'button_text'     => 'S\'abonner',
                                'footer_text'     => 'Aucun spam · Désabonnement en 1 clic',
                            ),
                        ), $p_id);
                    } elseif ($slug === 'about') {
                        update_field('field_page_layouts', array(
                            array(
                                'acf_fc_layout' => 'common_hero_section',
                                'home_text'     => 'Accueil',
                                'current_text'  => 'À propos',
                                'title'         => 'Recherche agence <span class="quiet">indépendante.</span>',
                                'description'   => 'Agence Marketing Digital aide les équipes marocaines à comprendre le marché des agences avec des standards éditoriaux clairs, des guides pratiques et des notes de recherche transparentes.',
                                'cta_text'      => '',
                                'cta_link'      => '',
                            ),
                            array(
                                'acf_fc_layout' => 'about_grid_section',
                                'cards' => array(
                                    array(
                                        'card_icon'        => 'search-check',
                                        'card_title'       => 'Ce que nous faisons',
                                        'card_description' => 'Nous analysons les affirmations des agences, expliquons les critères de sélection et publions des conseils utiles pour poser les bonnes questions avant de choisir un partenaire.',
                                    ),
                                    array(
                                        'card_icon'        => 'shield-check',
                                        'card_title'       => 'Notre indépendance',
                                        'card_description' => 'Le processus éditorial est séparé des demandes commerciales. Nous ne vendons pas de positions, et les corrections sont examinées avec des preuves avant publication.',
                                    ),
                                    array(
                                        'card_icon'        => 'users',
                                        'card_title'       => 'Pour qui',
                                        'card_description' => 'Le site s\'adresse aux fondateurs, responsables marketing et opérations qui veulent évaluer plus sereinement les partenaires SEO, paid media, social, web et contenu au Maroc.',
                                    ),
                                ),
                            ),
                            array(
                                'acf_fc_layout'   => 'about_cta_section',
                                'eyebrow'        => 'Processus & Inscription',
                                'title'          => 'Vous voulez comprendre notre processus ?',
                                'description'    => 'Consultez la méthodologie pour voir les critères de recherche, ou contactez l\'équipe éditoriale pour une correction ou une demande d\'ajout.',
                                'primary_text'   => 'Lire la méthodologie',
                                'primary_link'   => '/methodologie/',
                                'secondary_text' => 'Nous contacter',
                                'secondary_link' => '/contact/',
                            ),
                        ), $p_id);
                    } elseif ($slug === 'contact') {
                        update_field('field_page_layouts', array(
                            array(
                                'acf_fc_layout' => 'common_hero_section',
                                'home_text'     => 'Accueil',
                                'current_text'  => 'Contact',
                                'title'         => 'Contactez-<span class="quiet">nous.</span>',
                                'description'   => 'Des questions sur les données des agences, des corrections, des avis ou des recherches éditoriales ? Envoyez-nous les détails et notre équipe vous répondra.',
                            ),
                            array(
                                'acf_fc_layout'   => 'contact_form_section',
                                'form_title'      => 'Envoyer un Message',
                                'form_desc'       => 'Utilisez ce formulaire pour les demandes de référencement, les corrections, les questions des acheteurs ou les notes de partenariat.',
                                'office_title'    => 'Siège Social',
                                'office_address'  => '8 rue de la Paix, 75002 Paris, France',
                                'office_city'     => 'Casablanca, Maroc',
                                'email'           => v5_digital_get_dynamic_email(),
                                'guarantee_title' => 'Garantie d\'Indépendance',
                                'guarantee_desc'  => 'Nous n\'acceptons pas de placements payants ni de classements sponsorisés. Les agences qui souhaitent être référencées passent par notre processus d\'évaluation standard et indépendant.',
                            ),
                        ), $p_id);
                    } elseif ($slug === 'methodologie') {
                        update_field('field_page_layouts', array(
                            array(
                                'acf_fc_layout' => 'common_hero_section',
                                'home_text'     => 'Accueil',
                                'current_text'  => 'Méthodologie',
                                'title'         => 'Nos Critères <span class="quiet">d\'Évaluation.</span>',
                                'description'   => 'Comment nous analysons, notons et classons les agences de marketing digital au Maroc. Mis à jour en juin 2026.',
                                'cta_text'      => '',
                                'cta_link'      => '',
                            ),
                            array(
                                'acf_fc_layout' => 'methodology_process_section',
                                'section_label' => '01 / Processus de Validation',
                                'title'         => 'Quatre étapes. Un seul standard.',
                                'description'   => 'La même séquence est appliquée à chaque agence répertoriée, qu\'elle ait postulé ou été découverte par notre équipe.',
                                'stages' => array(
                                    array(
                                        'stage_num'         => '01',
                                        'stage_title'       => '1. Présélection Administrative',
                                        'stage_description' => 'Nous validons l\'éligibilité de l\'agence sur des critères minimaux :',
                                        'stage_tags'        => '2+ ans, enregistrée, 5+ projets',
                                        'stage_image'       => '',
                                        'stage_icon'        => '',
                                    ),
                                    array(
                                        'stage_num'         => '02',
                                        'stage_title'       => '2. Audit de Portfolio',
                                        'stage_description' => 'Nos auditeurs décortiquent les cas clients présentés par l\'agence :',
                                        'stage_tags'        => 'preuve de ROI, SEO, mobile',
                                        'stage_image'       => '',
                                        'stage_icon'        => '',
                                    ),
                                    array(
                                        'stage_num'         => '03',
                                        'stage_title'       => '3. Enquête Client Directe',
                                        'stage_description' => 'C\'est l\'étape déterminante. Nous vérifions l\'expérience vécue par les clients :',
                                        'stage_tags'        => 'email, LinkedIn, téléphone',
                                        'stage_image'       => '',
                                        'stage_icon'        => '',
                                    ),
                                    array(
                                        'stage_num'         => '04',
                                        'stage_title'       => '4. Notation Composite',
                                        'stage_description' => 'Les agences retenues reçoivent une note globale indexant quatre piliers :',
                                        'stage_tags'        => '35%, 30%, 20%, 15%',
                                        'stage_image'       => '',
                                        'stage_icon'        => '',
                                    ),
                                ),
                            ),
                            array(
                                'acf_fc_layout' => 'methodology_evidence_section',
                                'section_label' => '02 / Carte des Preuves',
                                'title'         => 'Les affirmations nécessitent des preuves.',
                                'evidence_rows' => array(
                                    array(
                                        'row_title'       => 'Aspect Légal',
                                        'row_description' => 'L\'enregistrement au Registre du Commerce, la continuité d\'activité et la traçabilité des contacts sont contrôlés en premier.',
                                    ),
                                    array(
                                        'row_title'       => 'Livrables',
                                        'row_description' => 'Les cas clients sont examinés sous l\'angle des résultats réels, de la rigueur technique, du SEO et de l\'optimisation mobile.',
                                    ),
                                    array(
                                        'row_title'       => 'Avis Clients',
                                        'row_description' => 'Les avis sont rattachés à des identités réelles, et validés par entretien direct pour les budgets importants.',
                                    ),
                                    array(
                                        'row_title'       => 'Données',
                                        'row_description' => 'Les scores sont pondérés de sorte qu\'une marque bruyante ne puisse surclasser une agence plus performante.',
                                    ),
                                ),
                            ),
                            array(
                                'acf_fc_layout' => 'methodology_monitor_section',
                                'section_label' => '03 / Suivi Continu',
                                'title'         => 'Nous contrôlons régulièrement les agences.',
                                'description'   => 'Une agence classée peut monter, descendre ou être retirée si de nouvelles analyses indiquent une modification de sa qualité.',
                                'steps' => array(
                                    array(
                                        'step_icon'        => 'calendar-sync',
                                        'step_title'       => 'Tous les 3 mois',
                                        'step_description' => 'Nous réévaluons les agences répertoriées pour éviter les données obsolètes.',
                                    ),
                                    array(
                                        'step_icon'        => 'message-square-warning',
                                        'step_title'       => 'Signalements clients',
                                        'step_description' => 'Les plaintes fondées de clients vérifiés déclenchent un audit manuel.',
                                    ),
                                    array(
                                        'step_icon'        => 'file-search',
                                        'step_title'       => 'Preuves périmées',
                                        'step_description' => 'Un portfolio inactif, des litiges légaux ou des déclarations infondées font baisser la note.',
                                    ),
                                    array(
                                        'step_icon'        => 'shield-x',
                                        'step_title'       => 'Faux avis',
                                        'step_description' => 'Toute tentative de manipulation entraîne le retrait immédiat du classement.',
                                    ),
                                ),
                            ),
                        ), $p_id);
                    }
                }
            }
        }
    }
    // Cleanup old conflicting 'methodology' page and update menu items
    if (isset($_GET['force_seed'])) {
        $old_methodology = get_page_by_path('methodology');
        if ($old_methodology && isset($seeded_page_ids['methodologie']) && $old_methodology->ID !== $seeded_page_ids['methodologie']) {
            wp_delete_post($old_methodology->ID, true);
        }
        
        // Scan all menus and fix links pointing to methodology
        $menu_items = get_posts(array(
            'post_type' => 'nav_menu_item',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));
        if (!empty($menu_items)) {
            foreach ($menu_items as $item) {
                // If it pointed to the old methodology page, point it to the new one
                $object_id = get_post_meta($item->ID, '_menu_item_object_id', true);
                if ($old_methodology && $object_id == $old_methodology->ID && isset($seeded_page_ids['methodologie'])) {
                    update_post_meta($item->ID, '_menu_item_object_id', $seeded_page_ids['methodologie']);
                }
                
                // If it has a URL pointing to '/methodology/', change it to '/methodologie/'
                $url = get_post_meta($item->ID, '_menu_item_url', true);
                if (stripos($url, '/methodology/') !== false) {
                    $new_url = str_ireplace('/methodology/', '/methodologie/', $url);
                    update_post_meta($item->ID, '_menu_item_url', $new_url);
                }
            }
        }
    }

    // 4.3 Create and Assign Primary Navigation Menu
    $menu_name = 'Primary Navigation Menu';
    $all_menus = wp_get_nav_menus();
    if (!empty($all_menus)) {
        foreach ($all_menus as $m) {
            if ($m->name === $menu_name || $m->slug === sanitize_title($menu_name)) {
                wp_delete_nav_menu($m->term_id);
            }
        }
    }
    
    $menu_id = wp_create_nav_menu($menu_name);
    if (!is_wp_error($menu_id)) {
        if (function_exists('pll_set_term_language')) {
            pll_set_term_language($menu_id, 'fr');
        }
        
        $menu_structure = array(
            array('title' => __('accueil', 'v5-digital'), 'slug' => 'accueil'),
            array('title' => __('annuaire', 'v5-digital'), 'slug' => 'annuaire'),
            array('title' => __('blog', 'v5-digital'), 'slug' => 'blog'),
            array('title' => __('à propos', 'v5-digital'), 'slug' => 'about'),
            array('title' => __('méthodologie', 'v5-digital'), 'slug' => 'methodologie'),
            array('title' => __('contact', 'v5-digital'), 'slug' => 'contact'),
        );
        
        $position = 1;
        foreach ($menu_structure as $m_item) {
            if ($m_item['slug'] === 'blog') {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title'     => $m_item['title'],
                    'menu-item-url'       => home_url('/blog/'),
                    'menu-item-type'      => 'custom',
                    'menu-item-status'    => 'publish',
                    'menu-item-position'  => $position++,
                ));
            } elseif (isset($seeded_page_ids[$m_item['slug']])) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title'     => $m_item['title'],
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => $seeded_page_ids[$m_item['slug']],
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                    'menu-item-position'  => $position++,
                ));
            }
        }
        
        // Assign menu to the location
        $locations = get_theme_mod('nav_menu_locations');
        if (!is_array($locations)) {
            $locations = array();
        }
        $locations['primary'] = $menu_id;
        
        // Find existing footer menus to map them automatically
        if (!empty($all_menus)) {
            foreach ($all_menus as $m) {
                $is_explore = (stripos($m->slug, 'footer-explore') !== false || stripos($m->name, 'Footer Explore') !== false);
                $is_resources = (stripos($m->slug, 'footer-resources') !== false || stripos($m->name, 'Footer Resources') !== false);
                $is_villes = (stripos($m->slug, 'footer-company') !== false || stripos($m->name, 'Footer Company') !== false || stripos($m->slug, 'footer-villes') !== false || stripos($m->name, 'Footer Cities') !== false);
                $is_legal = (stripos($m->slug, 'footer-legal') !== false || stripos($m->name, 'Footer Legal') !== false);
                
                if ($is_explore) {
                    $locations['footer_explore'] = $m->term_id;
                }
                if ($is_resources) {
                    $locations['footer_resources'] = $m->term_id;
                }
                if ($is_villes) {
                    $locations['footer_villes'] = $m->term_id;
                }
                if ($is_legal) {
                    $locations['footer_legal'] = $m->term_id;
                }
            }
        }

        if (function_exists('pll_languages_list')) {
            foreach (pll_languages_list() as $lang) {
                $locations['primary___' . $lang] = $menu_id;
                if (!empty($all_menus)) {
                    foreach ($all_menus as $m) {
                        $is_explore = (stripos($m->slug, 'footer-explore') !== false || stripos($m->name, 'Footer Explore') !== false);
                        $is_resources = (stripos($m->slug, 'footer-resources') !== false || stripos($m->name, 'Footer Resources') !== false);
                        $is_villes = (stripos($m->slug, 'footer-company') !== false || stripos($m->name, 'Footer Company') !== false || stripos($m->slug, 'footer-villes') !== false || stripos($m->name, 'Footer Cities') !== false);
                        $is_legal = (stripos($m->slug, 'footer-legal') !== false || stripos($m->name, 'Footer Legal') !== false);

                        if ($is_explore) {
                            $locations['footer_explore___' . $lang] = $m->term_id;
                        }
                        if ($is_resources) {
                            $locations['footer_resources___' . $lang] = $m->term_id;
                        }
                        if ($is_villes) {
                            $locations['footer_villes___' . $lang] = $m->term_id;
                        }
                        if ($is_legal) {
                            $locations['footer_legal___' . $lang] = $m->term_id;
                        }
                    }
                }
            }
        }
        set_theme_mod('nav_menu_locations', $locations);
        update_option('nav_menu_locations', $locations);

        // Map locations in Polylang options if class exists
        if (class_exists('PLL_Model') || function_exists('pll_languages_list')) {
            $polylang_options = get_option('polylang');
            if (!is_array($polylang_options)) {
                $polylang_options = array();
            }
            if (!isset($polylang_options['nav_menus'])) {
                $polylang_options['nav_menus'] = array();
            }
            
            $theme_slug = get_stylesheet();
            if (!isset($polylang_options['nav_menus'][$theme_slug])) {
                $polylang_options['nav_menus'][$theme_slug] = array();
            }
            
            // Map primary
            $polylang_options['nav_menus'][$theme_slug]['primary']['fr'] = $menu_id;
            
            // Map footer menus
            if (isset($locations['footer_explore'])) {
                $polylang_options['nav_menus'][$theme_slug]['footer_explore']['fr'] = $locations['footer_explore'];
            }
            if (isset($locations['footer_resources'])) {
                $polylang_options['nav_menus'][$theme_slug]['footer_resources']['fr'] = $locations['footer_resources'];
            }
            if (isset($locations['footer_villes'])) {
                $polylang_options['nav_menus'][$theme_slug]['footer_villes']['fr'] = $locations['footer_villes'];
            }
            if (isset($locations['footer_legal'])) {
                $polylang_options['nav_menus'][$theme_slug]['footer_legal']['fr'] = $locations['footer_legal'];
            }
            
            update_option('polylang', $polylang_options);
        }
    }

    // 4.4 Seed Custom Post Types
    
    // Partner Logos seeding
    $logos = array('nestle', 'google', 'hyundai', 'l\'oreal', 'volvo', 'samsung');
    foreach ($logos as $logo) {
        $post_id = wp_insert_post(array(
            'post_title'  => $logo,
            'post_status' => 'publish',
            'post_type'   => 'partner_logo',
        ));
        if ($post_id && function_exists('pll_set_post_language')) {
            pll_set_post_language($post_id, 'fr');
        }
    }

    // Stat Metrics seeding
    $stats_data = array(
        array('num' => '150+', 'lbl' => 'Agences analysées et répertoriées'),
        array('num' => '2 400+', 'lbl' => 'Avis clients vérifiés par notre équipe'),
        array('num' => '6', 'lbl' => 'Villes marocaines couvertes'),
        array('num' => '0', 'lbl' => 'Placements payants ou rangs sponsorisés'),
    );
    foreach ($stats_data as $stat) {
        $post_id = wp_insert_post(array(
            'post_title'  => $stat['num'] . ' - ' . $stat['lbl'],
            'post_status' => 'publish',
            'post_type'   => 'stat_metric',
        ));
        if ($post_id) {
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($post_id, 'fr');
            }
            if (function_exists('update_field')) {
                update_field('field_stat_number', $stat['num'], $post_id);
                update_field('field_stat_label', $stat['lbl'], $post_id);
            }
        }
    }

    // Seed Taxonomies Terms (agency_service, agency_city)
    $service_slugs = array(
        'seo' => 'SEO (Référencement)',
        'paid-ads' => 'Publicité Payante',
        'social' => 'Réseaux Sociaux',
        'web-design' => 'Design Web',
        'branding' => 'Image de Marque',
        'content' => 'Marketing de Contenu',
        'cro' => 'Optimisation (CRO)',
        'analytics' => 'Données & Analytics'
    );
    $service_ids = array();
    foreach ($service_slugs as $slug => $name) {
        $inserted = wp_insert_term($name, 'agency_service', array('slug' => $slug));
        if (!is_wp_error($inserted)) {
            $term_id = $inserted['term_id'];
            $service_ids[$slug] = $term_id;
        } else {
            $term = get_term_by('slug', $slug, 'agency_service');
            if ($term) {
                $term_id = $term->term_id;
                $service_ids[$slug] = $term_id;
            }
        }
        if (isset($term_id) && function_exists('pll_set_term_language')) {
            pll_set_term_language($term_id, 'fr');
        }
    }

    $city_slugs = array(
        'casablanca' => 'Casablanca',
        'rabat' => 'Rabat',
        'tangier' => 'Tanger',
        'marrakech' => 'Marrakech',
        'agadir' => 'Agadir',
    );
    $city_ids = array();
    foreach ($city_slugs as $slug => $name) {
        $inserted = wp_insert_term($name, 'agency_city', array('slug' => $slug));
        if (!is_wp_error($inserted)) {
            $term_id = $inserted['term_id'];
            $city_ids[$slug] = $term_id;
        } else {
            $term = get_term_by('slug', $slug, 'agency_city');
            if ($term) {
                $term_id = $term->term_id;
                $city_ids[$slug] = $term_id;
            }
        }
        if (isset($term_id) && function_exists('pll_set_term_language')) {
            pll_set_term_language($term_id, 'fr');
        }
    }

    // Seed Specialty Hubs
    $hubs_data = array(
        array(
            'title' => 'SEO & Growth',
            'param' => 'seo',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="w-4 h-4"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            'subs' => array('SEO (Référencement naturel)', 'Publicité Google & Social', 'Marketing de Contenu', 'Optimisation des Conversions')
        ),
        array(
            'title' => 'Marque & Réseaux',
            'param' => 'social',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="w-4 h-4"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
            'subs' => array('Image de Marque (Branding)', 'Gestion Réseaux Sociaux', 'Campagnes d\'Influence', 'Publicités Sociales (SMM)')
        ),
        array(
            'title' => 'Web & Conversion',
            'param' => 'web-design',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="w-4 h-4"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>',
            'subs' => array('Conception de Sites Web', 'Pages d\'Atterrissage (Landing)', 'E-commerce (Boutiques)', 'Audits Techniques SEO & CRO')
        ),
    );
    foreach ($hubs_data as $hub) {
        $post_id = wp_insert_post(array(
            'post_title'  => $hub['title'],
            'post_status' => 'publish',
            'post_type'   => 'specialty_hub',
        ));
        if ($post_id) {
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($post_id, 'fr');
            }
            if (function_exists('update_field')) {
                update_field('field_specialty_icon_svg', $hub['icon'], $post_id);
                update_field('field_specialty_link_param', $hub['param'], $post_id);
                
                $subs_list = array();
                foreach ($hub['subs'] as $sub_name) {
                    $subs_list[] = array('service_name' => $sub_name);
                }
                update_field('field_specialty_sub_services', $subs_list, $post_id);
            }
        }
    }

    // Seed Agencies (Top rated Moroccan digital agencies)
    $agencies_data = array(
        array(
            'name'      => 'RMD',
            'logo'      => 'RMD',
            'logo_url'  => 'https://ui-avatars.com/api/?name=RMD&background=1a56db&color=ffffff&size=128&bold=true&font-size=0.35',
            'logo_color'=> '#1a56db',
            'website'   => 'https://rmd.ma',
            'rating'    => '4.9',
            'reviews'   => '42',
            'rank'      => '1',
            'excerpt'   => 'Partenaire de croissance digitale pour marques marocaines ambitieuses.',
            'content'   => '<p>RMD (Réseau Marketing Digital) est une agence de marketing digital intégrée basée à Tanger, au Maroc. Fondée en 2018, elle s\'est rapidement imposée comme l\'un des principaux partenaires de croissance pour les marques marocaines ambitieuses souhaitant développer leur présence en ligne.</p><p>Leur équipe de plus de 25 spécialistes allie expertise du marché local et meilleures pratiques internationales pour délivrer un ROI mesurable en SEO, publicité payante, réseaux sociaux et conception de sites web. RMD a accompagné plus de 150 clients à travers le Maroc, des startups aux grandes entreprises.</p>',
            'services'  => array('seo', 'paid-ads', 'social', 'web-design', 'content'),
            'cities'    => array('tangier'),
        ),
        array(
            'name'      => 'Pixagram',
            'logo'      => 'PIXA',
            'logo_url'  => 'https://ui-avatars.com/api/?name=PIXA&background=7c3aed&color=ffffff&size=128&bold=true&font-size=0.35',
            'logo_color'=> '#7c3aed',
            'website'   => 'https://pixagram.ma',
            'rating'    => '4.8',
            'reviews'   => '38',
            'rank'      => '2',
            'excerpt'   => 'Agence digitale créative spécialisée en réseaux sociaux et image de marque.',
            'content'   => '<p>Pixagram est une agence créative basée dans le cœur économique du Maroc, à Casablanca. Depuis notre création en 2019, nous combinons l\'art du storytelling et la rigueur du marketing de performance.</p><p>Nous collaborons avec de grandes marques de consommation, des réseaux de distribution et des startups en pleine croissance pour créer des identités de marque mémorables et déployer des campagnes sociales virales. Notre équipe se compose de designers, rédacteurs, community managers et acheteurs média.</p>',
            'services'  => array('social', 'branding', 'content', 'paid-ads'),
            'cities'    => array('casablanca'),
        ),
        array(
            'name'      => 'MediaBoost',
            'logo'      => 'BOOST',
            'logo_url'  => 'https://ui-avatars.com/api/?name=BOOST&background=059669&color=ffffff&size=128&bold=true&font-size=0.3',
            'logo_color'=> '#059669',
            'website'   => 'https://mediaboost.ma',
            'rating'    => '4.7',
            'reviews'   => '31',
            'rank'      => '3',
            'excerpt'   => 'Agence de performance axée sur l\'acquisition payante et le CRO.',
            'content'   => '<p>MediaBoost est l\'agence de performance de référence à Rabat, spécialisée dans la publicité à réponse directe et l\'optimisation des taux de conversion. Depuis 2020, nous aidons les sites e-commerce et les promoteurs immobiliers à maximiser leur ROI digital.</p><p>Nous ne nous concentrons pas sur des métriques de vanité mais sur les ventes, les leads et les revenus grâce à des tests continus et une configuration analytique experte.</p>',
            'services'  => array('paid-ads', 'seo', 'cro', 'analytics'),
            'cities'    => array('rabat'),
        ),
        array(
            'name'      => 'DigitalWave',
            'logo'      => 'WAVE',
            'logo_url'  => 'https://ui-avatars.com/api/?name=WAVE&background=0891b2&color=ffffff&size=128&bold=true&font-size=0.35',
            'logo_color'=> '#0891b2',
            'website'   => 'https://digitalwave.ma',
            'rating'    => '4.6',
            'reviews'   => '27',
            'rank'      => '4',
            'excerpt'   => 'Agence digitale globale avec une forte expertise en ingénierie web.',
            'content'   => '<p>DigitalWave est une agence digitale axée sur la technologie et basée à Casablanca. Nous sommes spécialisés dans le développement web sur mesure, les projets e-commerce complexes et les campagnes SEO intégrées.</p><p>Nous pensons qu\'un site web à haute conversion est le fondement de tout marketing digital et nous concevons des architectures rapides, sécurisées et optimisées pour le SEO.</p>',
            'services'  => array('web-design', 'seo', 'branding'),
            'cities'    => array('casablanca'),
        ),
        array(
            'name'      => 'NexaMedia',
            'logo'      => 'NEXA',
            'logo_url'  => 'https://ui-avatars.com/api/?name=NEXA&background=d97706&color=ffffff&size=128&bold=true&font-size=0.35',
            'logo_color'=> '#d97706',
            'website'   => 'https://nexamedia.ma',
            'rating'    => '4.5',
            'reviews'   => '22',
            'rank'      => '5',
            'excerpt'   => 'Agence éditoriale spécialisée dans le contenu et les relations presse.',
            'content'   => '<p>NexaMedia est l\'agence leader en marketing de contenu à Marrakech. Nous pensons que le contenu est l\'actif principal pour asseoir la confiance des clients et booster la visibilité naturelle.</p><p>Nous aidons les groupes hôteliers, les projets immobiliers et les commerces locaux à concevoir du contenu de blog engageant et à optimiser leur référencement local.</p>',
            'services'  => array('content', 'seo', 'social'),
            'cities'    => array('marrakech'),
        ),
        array(
            'name'      => 'Sahara Digital',
            'logo'      => 'SAHARA',
            'logo_url'  => 'https://ui-avatars.com/api/?name=SAHARA&background=dc2626&color=ffffff&size=128&bold=true&font-size=0.28',
            'logo_color'=> '#dc2626',
            'website'   => 'https://saharadigital.ma',
            'rating'    => '4.4',
            'reviews'   => '19',
            'rank'      => '6',
            'excerpt'   => 'Agence leader du Sud Marocain pour le tourisme et l\'hôtellerie.',
            'content'   => '<p>Sahara Digital est l\'agence digitale de référence dans le sud du Maroc, installée à Agadir. Nous accompagnons l\'écosystème touristique (surf, riads) et agroalimentaire de Souss-Massa.</p><p>Notre équipe conçoit des sites web réactifs et gère des récits sur les réseaux sociaux qui reflètent l\'esprit authentique de la région.</p>',
            'services'  => array('social', 'web-design', 'branding'),
            'cities'    => array('agadir'),
        ),
    );


    foreach ($agencies_data as $agency) {
        $post_id = wp_insert_post(array(
            'post_title'   => $agency['name'],
            'post_content' => $agency['content'],
            'post_excerpt' => $agency['excerpt'],
            'post_status'  => 'publish',
            'post_type'    => 'agency',
        ));
        if ($post_id) {
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($post_id, 'fr');
            }
            if (function_exists('update_field')) {
                update_field('field_agency_logo_text',     $agency['logo'],    $post_id);
                update_field('field_agency_logo_image_url', isset($agency['logo_url']) ? $agency['logo_url'] : '', $post_id);
                update_field('field_agency_rating',  $agency['rating'],  $post_id);
                update_field('field_agency_reviews',  $agency['reviews'], $post_id);
                update_field('field_agency_rank',   $agency['rank'],    $post_id);
                update_field('field_agency_website',  $agency['website'], $post_id);

                // Store website and brand color as postmeta
                if (!empty($agency['website'])) {
                    update_post_meta($post_id, 'website', $agency['website']);
                }
                if (!empty($agency['logo_color'])) {
                    update_post_meta($post_id, 'agency_color', $agency['logo_color']);
                }

                // Set terms
                $post_services = array();
                foreach ($agency['services'] as $s_slug) {
                    if (isset($service_ids[$s_slug])) {
                        $post_services[] = intval($service_ids[$s_slug]);
                    }
                }
                wp_set_post_terms($post_id, $post_services, 'agency_service');

                $post_cities = array();
                foreach ($agency['cities'] as $c_slug) {
                    if (isset($city_ids[$c_slug])) {
                        $post_cities[] = intval($city_ids[$c_slug]);
                    }
                }
                wp_set_post_terms($post_id, $post_cities, 'agency_city');
            }
        }
    }

    // Seed Testimonials
    $testimonials_data = array(
        array(
            'author' => 'Meryem Alaoui',
            'role' => 'Directrice Marketing - Dakhla Travel Co.',
            'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=96&h=96&fit=crop&crop=face',
            'quote' => 'Le profil RMD sur le site nous a montré exactement ce dont nous avions besoin : preuves SEO, budget, et commentaires vérifiés. Nous les avons shortlistés en un après-midi et nos leads organiques ont grimpé de 38% après l\'audit.',
            'rating' => 5,
            'agency_name' => 'RMD',
            'agency_slug' => 'rmd',
            'project' => 'Audit SEO technique et pages locales',
            'result' => 'Leads organiques +38% en 10 semaines'
        ),
        array(
            'author' => 'Yassine El Fassi',
            'role' => 'Co-fondateur - Casa Homeware',
            'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=96&h=96&fit=crop&crop=face',
            'quote' => 'Le profil de Pixagram a rendu notre choix beaucoup moins risqué. Leurs projets correspondaient à notre secteur, et leur équipe a reconstruit notre tunnel de vente social sans forcer sur des options superflues.',
            'rating' => 5,
            'agency_name' => 'Pixagram',
            'agency_slug' => 'pixagram',
            'project' => 'Rafraîchissement de marque et social commerce',
            'result' => 'Ventes Instagram +44% après lancement'
        ),
        array(
            'author' => 'Salma Bennani',
            'role' => 'Growth Manager - Rabat Fintech Lab',
            'image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=96&h=96&fit=crop&crop=face',
            'quote' => 'Nous avons comparé trois agences de paid media et avons choisi MediaBoost car les preuves d\'audit étaient claires. Le nettoyage du tracking a enfin montré quelles campagnes étaient réellement rentables.',
            'rating' => 5,
            'agency_name' => 'MediaBoost',
            'agency_slug' => 'mediaboost',
            'project' => 'Tracking d\'acquisition et sprint d\'optimisation CRO',
            'result' => 'Coût par lead qualifié en baisse de 31%'
        )
    );

    $testimonial_ids = array();
    foreach ($testimonials_data as $t) {
        $t_post_id = wp_insert_post(array(
            'post_title'   => $t['author'],
            'post_content' => $t['quote'],
            'post_status'  => 'publish',
            'post_type'    => 'testimonial',
        ));
        if ($t_post_id) {
            $testimonial_ids[] = $t_post_id;
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($t_post_id, 'fr');
            }
            if (function_exists('update_field')) {
                update_field('field_testimonial_rating', $t['rating'], $t_post_id);
                update_field('field_testimonial_author_role', $t['role'], $t_post_id);
                update_field('field_testimonial_author_image', $t['image'], $t_post_id);
                update_field('field_testimonial_hired_agency_name', $t['agency_name'], $t_post_id);
                update_field('field_testimonial_hired_agency_slug', $t['agency_slug'], $t_post_id);
                update_field('field_testimonial_project', $t['project'], $t_post_id);
                update_field('field_testimonial_result', $t['result'], $t_post_id);
            }
        }
    }

    // 4.5 Populate Homepage layouts
    if (function_exists('update_field')) {
        $layouts = array(
            // Hero
            array(
                'acf_fc_layout' => 'hero_section',
                'eyebrow' => '01 · MATCH & DISCOVER',
                'title' => 'Trouvez les <span class="hero-focus-word">meilleures</span> agences de marketing digital au <span class="hero-location-word">Maroc</span>',
                'description' => 'Comparez les agences digitales marocaines grâce à des recherches éditoriales, des scores techniques de vitesse et des avis clients vérifiés.',
                'social_proof_1' => '150+ agences évaluées',
                'social_proof_2' => 'Référencement 100% éditorial',
                'hero_ctas' => array(
                    array(
                        'text' => 'Trouver mon agence',
                        'link_type' => 'page',
                        'page' => isset($seeded_page_ids['contact']) ? $seeded_page_ids['contact'] : '',
                        'style' => 'primary',
                        'icon' => 'sparkles',
                    ),
                    array(
                        'text' => 'Faire référencer mon agence',
                        'link_type' => 'url',
                        'url' => '/contact/?subject=listing',
                        'style' => 'secondary',
                        'icon' => 'home',
                    ),
                ),
                'hero_stats' => array(
                    array(
                        'number' => '150+',
                        'label' => 'Agences analysées',
                    ),
                    array(
                        'number' => '2 400+',
                        'label' => 'Avis vérifiés',
                    ),
                    array(
                        'number' => '6',
                        'label' => 'Villes couvertes',
                    ),
                    array(
                        'number' => '0',
                        'label' => 'Placements payants',
                    ),
                ),
            ),
            // Search Filter
            array(
                'acf_fc_layout' => 'search_filter_section',
            ),
            // Logos
            array(
                'acf_fc_layout' => 'logos_band_section',
                'section_title' => 'Annuaire de confiance utilisé par les acheteurs de :',
            ),
            // Challenge
            array(
                'acf_fc_layout' => 'challenge_section',
                'eyebrow' => 'Le Défi',
                'title' => 'La plupart des entreprises choisissent la <span class="challenge-focus-word">mauvaise agence</span> en <span class="challenge-time-word">48 heures</span>.',
                'description' => 'Le marché des agences de marketing digital au Maroc est encombré, bruyant et opaque. Les beaux sites cachent souvent un travail médiocre. Les témoignages élogieux sont rarement vérifiés.',
                'cards' => array(
                    array(
                        'title' => 'Pas de comparaison indépendante',
                        'description' => 'Chaque agence se proclame "#1 au Maroc" sans preuve concrète.',
                        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="w-3.5 h-3.5 text-slate-800"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
                    ),
                    array(
                        'title' => 'Des avis non fiables',
                        'description' => 'Les témoignages sont sélectionnés à la main par les agences elles-mêmes.',
                        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="w-3.5 h-3.5 text-slate-800"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
                    ),
                    array(
                        'title' => 'Absence de contexte local',
                        'description' => 'Les annuaires mondiaux ne comprennent pas les spécificités du marché marocain.',
                        'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="w-3.5 h-3.5 text-slate-800"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
                    )
                ),
                'quote_text' => 'Nous avons brûlé deux agences et 200 000 dirhams avant de trouver une équipe qui a réellement livré des résultats. Si cet annuaire avait existé, nous aurions économisé six mois.',
                'quote_author' => 'Youssef Benali',
                'quote_role' => 'CMO, Craft Morocco',
                'quote_verified' => 'Avis vérifié',
                'quote_scope' => 'Projet : SEO & Publicité Payante',
                'quote_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face',
            ),
            // Approach
            array(
                'acf_fc_layout' => 'approach_section',
                'eyebrow' => 'Notre Approche',
                'title' => 'Une <span class="approach-focus-word">meilleure façon</span> de <span class="approach-action-word">choisir</span> une agence.',
                'description' => 'Nous faisons les recherches pour que vous puissiez vous concentrer sur la décision.',
                'points' => array(
                    array(
                        'number' => '1',
                        'title' => 'Recherche approfondie',
                        'description' => 'Notre équipe éditoriale audite les portefeuilles, interroge de vrais clients et vérifie les registres commerciaux de chaque agence avant qu\'elle n\'apparaisse sur notre plateforme.'
                    ),
                    array(
                        'number' => '2',
                        'title' => 'Évaluation vérifiée',
                        'description' => 'Chaque agence reçoit une note indépendante basée sur quatre critères pondérés : les avis clients, la qualité des réalisations, la présence sur le marché et la largeur de service.'
                    ),
                    array(
                        'number' => '3',
                        'title' => 'Mise en relation directe',
                        'description' => 'Contactez directement les agences via des profils vérifiés. Pas de frais de mise en relation, pas d\'intermédiaire, pas de commissions cachées. Juste des informations honnêtes.'
                    )
                )
            ),
            // Outcomes
            array(
                'acf_fc_layout' => 'outcomes_section',
                'eyebrow' => 'Résultats clients vérifiés',
                'title' => 'Ce que disent les équipes après avoir choisi via Agence Marketing Digital',
                'description' => 'Histoires réelles de sélection par des équipes marocaines ayant analysé les profils, les audits techniques et les avis vérifiés avant de contacter une agence.',
                'metrics' => array(
                    array('value' => '4.8', 'label' => 'Note moyenne vérifiée'),
                    array('value' => '92%', 'label' => 'Recommanderaient l\'annuaire'),
                    array('value' => '2 400+', 'label' => 'Avis clients vérifiés')
                ),
                'reviews' => $testimonial_ids
            ),
            // Stats Band
            array(
                'acf_fc_layout' => 'stats_band_section',
            ),
            // Picks
            array(
                'acf_fc_layout' => 'picks_section',
                'eyebrow' => "Choix de l'Éditeur",
                'title' => 'Les agences les plus performantes ce mois-ci.',
                'link_text' => 'Comment nous évaluons',
                'link_url' => home_url('/methodologie/')
            ),
            // Specialties
            array(
                'acf_fc_layout' => 'specialties_section',
                'eyebrow' => 'Spécialités',
                'title' => 'Trouvez votre besoin exact.',
                'description' => 'Explorez notre annuaire par canal marketing ou par spécialité.',
                'view_all_cta_text' => 'Voir tous les classements',
            ),
            // Guides
            array(
                'acf_fc_layout' => 'guides_section',
                'eyebrow' => 'Éditorial',
                'title' => 'Derniers guides & analyses.',
                'link_text' => 'Tous les articles',
                'link_url' => home_url('/blog/')
            ),
            // Footer CTA
            array(
                'acf_fc_layout' => 'footer_cta_section',
                'eyebrow' => 'Matchmaker',
                'title' => 'Prêt à trouver la bonne agence digitale ?',
                'description' => 'Évitez les présentations commerciales génériques. Décrivez votre projet en 2 minutes et laissez nos audits indépendants identifier le partenaire idéal.',
                'primary_cta_text' => 'Lancer le Matchmaker',
                'secondary_cta_text' => 'none',
            )
        );
        $existing_homepage_layouts = get_field('field_page_layouts', $homepage_id);
        if (empty($existing_homepage_layouts) || isset($_GET['force_seed'])) {
            update_field('field_page_layouts', $layouts, $homepage_id);
        }

        // 4.6 Seed Mockup Blog Articles
        $articles = array(
            array(
                'slug' => 'top-agencies',
                'title' => 'Top Agences de Marketing Digital au Maroc',
                'excerpt' => 'Notre classement définitif des meilleures agences basé sur les avis vérifiés et les métriques de performance.',
                'badge' => 'Classement',
                'read_time' => '8 min de lecture',
                'author' => 'Karim El Amrani',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=400&fit=crop',
                'content_layouts' => array(
                    array(
                        'acf_fc_layout' => 'wysiwyg_block',
                        'content' => '<p>Choisir la bonne agence de marketing digital au Maroc est une étape stratégique déterminante pour votre visibilité organique. Avec plusieurs centaines de prestataires sur le marché, nous avons audité techniquement et passé au crible les portefeuilles d\'agences afin de vous livrer un comparatif factuel.</p><p>Notre méthodologie se concentre sur les performances réelles (Core Web Vitals, conformité d\'indexation, audits de vitesse) et l\'analyse de l\'acquisition client. Voici notre classement des meilleures agences.</p>',
                    ),
                    array(
                        'acf_fc_layout' => 'heading_block',
                        'heading_text' => 'Prendre la décision finale',
                        'heading_level' => 'h2',
                    ),
                    array(
                        'acf_fc_layout' => 'wysiwyg_block',
                        'content' => '<p>Si vos objectifs sont orientés sur le trafic organique pur, l’optimisation technique ou les campagnes payantes (SEA), l’agence <strong>RMD</strong> l’emporte de loin. Si vous souhaitez axer votre stratégie sur la notoriété, le design et les réseaux sociaux, <strong>Pixagram</strong> saura répondre à vos besoins créatifs.</p>',
                    ),
                    array(
                        'acf_fc_layout' => 'agency_reviews_block',
                        'reviews_list' => array(
                            array(
                                'agency' => 'RMD',
                                'rank' => 1,
                                'badge' => 'Leader SEO (Score : 98/100)',
                                'description' => '<strong>Pourquoi RMD est classée #1 :</strong> ils représentent la référence absolue en matière de SEO technique et de performance globale. RMD se concentre sur le code propre, l\'intention de recherche et les campagnes SEA performantes.',
                                'link_text' => 'voir le site de rmd →',
                            ),
                            array(
                                'agency' => 'Pixagram',
                                'rank' => 2,
                                'badge' => 'Leader Branding (Score : 95/100)',
                                'description' => '<strong>Pourquoi Pixagram est classée #2 :</strong> Pixagram fait la transition parfaite entre image de marque et acquisition. Leurs tunnels de vente sociaux et créations visuelles sont exceptionnellement engageants.',
                                'link_text' => 'voir le site de pixagram →',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'slug' => 'seo-casablanca',
                'title' => 'Meilleures Agences SEO à Casablanca',
                'excerpt' => 'Une sélection rigoureuse d\'experts en référencement naturel pour booster votre positionnement.',
                'badge' => 'Guide',
                'read_time' => '6 min de lecture',
                'author' => 'Omar Fassi',
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=400&fit=crop',
                'content_layouts' => array(
                    array(
                        'acf_fc_layout' => 'wysiwyg_block',
                        'content' => '<p>Casablanca est le poumon économique du Maroc, et la concurrence sur les moteurs de recherche y est féroce. Apparaître en première page peut radicalement changer votre acquisition client. Malheureusement, de nombreuses agences locales vendent des abonnements SEO sans livrer de résultats mesurables.</p><p>Notre équipe a analysé les réalisations de plus de 30 agences basées à Casablanca. Nous avons évalué la qualité de leur SEO technique, la vitesse d\'indexation et l\'optimisation locale (Google Maps).</p>',
                    ),
                    array(
                        'acf_fc_layout' => 'heading_block',
                        'heading_text' => 'Les dangers de la surcharge technique (bloat)',
                        'heading_level' => 'h2',
                    ),
                    array(
                        'acf_fc_layout' => 'wysiwyg_block',
                        'content' => '<p>Un constat récurrent de nos audits : l\'usage abusif d\'éditeurs visuels lourds qui détériorent les signaux web essentiels (Core Web Vitals). Les leaders du SEO développent des sites web légers et performants pour offrir un avantage technique décisif.</p>',
                    ),
                    array(
                        'acf_fc_layout' => 'agency_reviews_block',
                        'reviews_list' => array(
                            array(
                                'agency' => 'DigitalWave',
                                'rank' => 4,
                                'badge' => 'Dév & SEO (Score : 89/100)',
                                'description' => '<strong>Pourquoi DigitalWave est recommandée :</strong> implantés à Casablanca, ils allient ingénierie web sur mesure et optimisation sémantique. Leurs architectures de code propre garantissent des indexations extrêmement rapides.',
                                'link_text' => 'voir le site de digitalwave →',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'slug' => 'social-media-compared',
                'title' => 'Les Agences Social Media sous le prisme du SEO',
                'excerpt' => 'Une comparaison des équipes sociales par demande de marque, vélocité du contenu et impact de recherche indirect.',
                'badge' => 'Comparatif',
                'read_time' => '10 min de lecture',
                'author' => 'Laila Bennani',
                'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=400&fit=crop',
                'content_layouts' => array(
                    array(
                        'acf_fc_layout' => 'wysiwyg_block',
                        'content' => '<p>Comparer des agences social media avec des lunettes SEO peut sembler paradoxal, mais le branding génère du search. Les marques qui animent activement leurs communautés sur Instagram, LinkedIn et TikTok constatent une croissance importante des requêtes de recherche directe sur leur nom de marque.</p><p>Dans cet article, nous analysons comment les meilleures agences créatives du Maroc intègrent cette dimension de découvrabilité dans leurs livrables.</p>',
                    ),
                    array(
                        'acf_fc_layout' => 'heading_block',
                        'heading_text' => 'Le trafic organique indirect',
                        'heading_level' => 'h2',
                    ),
                    array(
                        'acf_fc_layout' => 'wysiwyg_block',
                        'content' => '<p>La notoriété de marque générée par les campagnes sociales se traduit par une hausse des recherches directes. Ces signaux renforcent l\'autorité globale du domaine auprès de Google, augmentant le classement SEO général.</p>',
                    ),
                    array(
                        'acf_fc_layout' => 'agency_reviews_block',
                        'reviews_list' => array(
                            array(
                                'agency' => 'Pixagram',
                                'rank' => 2,
                                'badge' => 'Leader Branding (Score : 95/100)',
                                'description' => '<strong>Pourquoi Pixagram est classée #2 :</strong> Pixagram fait la transition parfaite entre image de marque et acquisition. Leurs tunnels de vente sociaux et créations visuelles sont exceptionnellement engageants.',
                                'link_text' => 'voir le site de pixagram →',
                            ),
                        ),
                    ),
                ),
            ),
        );

        // Fetch agency posts mapping
        $agency_ids_by_name = array();
        $agencies_query = get_posts(array('post_type' => 'agency', 'numberposts' => -1, 'post_status' => 'any'));
        foreach ($agencies_query as $a_post) {
            $agency_ids_by_name[strtolower(trim($a_post->post_title))] = $a_post->ID;
        }

        foreach ($articles as $art) {
            $art_post = get_page_by_path($art['slug'], OBJECT, 'blog');
            if (!$art_post) {
                $post_id = wp_insert_post(array(
                    'post_title'   => $art['title'],
                    'post_name'    => $art['slug'],
                    'post_excerpt' => $art['excerpt'],
                    'post_status'  => 'publish',
                    'post_type'    => 'blog',
                ));
            } else {
                $post_id = $art_post->ID;
            }

            if ($post_id) {
                if (function_exists('pll_set_post_language')) {
                    pll_set_post_language($post_id, 'fr');
                }

                // Update metadata fields
                update_field('field_blog_badge', $art['badge'], $post_id);
                update_field('field_blog_read_time', $art['read_time'], $post_id);
                update_field('field_blog_author_name', $art['author'], $post_id);
                update_field('field_blog_cover_image_url', $art['image'], $post_id);

                // Build layout fields
                $layout_data = array();
                foreach ($art['content_layouts'] as $layout) {
                    if ($layout['acf_fc_layout'] === 'wysiwyg_block') {
                        $layout_data[] = array(
                            'acf_fc_layout' => 'wysiwyg_block',
                            'content' => $layout['content'],
                        );
                    } elseif ($layout['acf_fc_layout'] === 'heading_block') {
                        $layout_data[] = array(
                            'acf_fc_layout' => 'heading_block',
                            'heading_text' => $layout['heading_text'],
                            'heading_level' => $layout['heading_level'],
                        );
                    } elseif ($layout['acf_fc_layout'] === 'agency_reviews_block') {
                        $reviews_list = array();
                        foreach ($layout['reviews_list'] as $rev) {
                            $agency_key = strtolower(trim($rev['agency']));
                            $agency_id = isset($agency_ids_by_name[$agency_key]) ? $agency_ids_by_name[$agency_key] : 0;
                            if ($agency_id) {
                                $reviews_list[] = array(
                                    'agency' => $agency_id,
                                    'rank' => $rev['rank'],
                                    'badge' => $rev['badge'],
                                    'description' => $rev['description'],
                                    'link_text' => $rev['link_text'],
                                );
                            }
                        }
                        $layout_data[] = array(
                            'acf_fc_layout' => 'agency_reviews_block',
                            'reviews_list' => $reviews_list,
                        );
                    }
                }
                update_field('field_blog_layouts', $layout_data, $post_id);
            }
        }
    }
}
add_action('after_switch_theme', 'v5_digital_setup_theme_content');

// 4.7. Unified force-seed URL trigger (runs on front-end/admin-end via ?force_seed=1 or ?force_seed=v5_digital_seed_2026)
add_action('init', function() {
    if (isset($_GET['force_seed'])) {
        $is_admin = current_user_can('manage_options');
        $is_secret = ($_GET['force_seed'] === 'v5_digital_seed_2026');
        
        if ($is_secret || ($is_admin && $_GET['force_seed'] === '1')) {
            // Execute the theme setup content function
            v5_digital_setup_theme_content();
            
            // If secret bypass token was used, print diagnostic JSON and exit
            if ($is_secret) {
                header('Content-Type: application/json');
                
                // Get pages
                $pages = array('accueil', 'blog', 'annuaire', 'about', 'methodologie', 'contact');
                $page_report = array();
                foreach ($pages as $slug) {
                    if ($slug === 'accueil') {
                        $p_id = get_option('page_on_front');
                        $title = 'Accueil';
                    } else {
                        $p_obj = get_page_by_path($slug);
                        $p_id = $p_obj ? $p_obj->ID : 0;
                        $title = $p_obj ? $p_obj->post_title : '';
                    }
                    $layouts = function_exists('get_field') ? get_field('page_layouts', $p_id) : array();
                    $page_report[$slug] = array(
                        'id' => $p_id,
                        'title' => $title,
                        'slug' => $p_id ? get_post_field('post_name', $p_id) : '',
                        'layout_count' => is_array($layouts) ? count($layouts) : 0,
                    );
                }
                
                // Get menus
                $menus = wp_get_nav_menus();
                $menu_list = array();
                foreach ($menus as $m) {
                    $menu_list[] = array(
                        'term_id' => $m->term_id,
                        'name' => $m->name,
                        'slug' => $m->slug,
                        'count' => $m->count,
                        'items' => array_map(function($i) { return array('title' => $i->title, 'url' => $i->url, 'object_id' => $i->object_id); }, wp_get_nav_menu_items($m->term_id))
                    );
                }
                
                echo json_encode(array(
                    'status' => 'FORCE_SEED_SUCCESS',
                    'theme_stylesheet' => get_stylesheet(),
                    'nav_menu_locations' => get_theme_mod('nav_menu_locations'),
                    'polylang' => get_option('polylang'),
                    'pages' => $page_report,
                    'menus' => $menu_list
                ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            // Otherwise redirect to dashboard with notice
            wp_safe_redirect(admin_url('?seeding_completed=1'));
            exit;
        }
    }
});

// Admin notice on successful seeding
add_action('admin_notices', function() {
    if (isset($_GET['seeding_completed']) && $_GET['seeding_completed'] === '1') {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Le contenu initial de la base de données a été ré-initialisé et configuré avec succès !', 'v5-digital') . '</p></div>';
    }
});

// ----------------------------------------------------
// 5. NAVIGATION MENUS & THEME SETUP
// ----------------------------------------------------

function v5_digital_theme_setup() {
    // Navigation registration
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'v5-digital'),
        'footer_explore' => __('Footer Explore (Découvrir)', 'v5-digital'),
        'footer_resources' => __('Footer Resources (Ressources)', 'v5-digital'),
        'footer_villes' => __('Footer Cities (Villes)', 'v5-digital'),
        'footer_legal' => __('Footer Legal (Légal)', 'v5-digital'),
    ));

    // Support standard dynamic title tag
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'v5_digital_theme_setup');

// ----------------------------------------------------
// 6. DYNAMIC XML SITEMAP
// ----------------------------------------------------

add_action('init', function() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?custom_sitemap=1', 'top');
});

add_filter('query_vars', function($vars) {
    $vars[] = 'custom_sitemap';
    return $vars;
});

// Prevent WordPress from redirecting /sitemap.xml to /sitemap.xml/
add_filter('redirect_canonical', function($redirect_url, $requested_url) {
    if (strpos($requested_url, 'sitemap.xml') !== false) {
        return false;
    }
    return $redirect_url;
}, 10, 2);

add_action('template_redirect', function() {
    if (get_query_var('custom_sitemap')) {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 1. Static/Core pages
        $urls = array(
            home_url('/'),
            home_url('/blog/'),
            home_url('/about/'),
            home_url('/methodologie/'),
            home_url('/contact/'),
        );
        foreach ($urls as $url) {
            echo '  <url>' . "\n";
            echo '    <loc>' . esc_url($url) . '</loc>' . "\n";
            echo '    <changefreq>daily</changefreq>' . "\n";
            echo '    <priority>1.0</priority>' . "\n";
            echo '  </url>' . "\n";
        }

        // 2. Query dynamic CPT posts
        $posts_query = new WP_Query(array(
            'post_type'      => array('blog', 'agency', 'specialty_hub'),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));

        if ($posts_query->have_posts()) {
            while ($posts_query->have_posts()) {
                $posts_query->the_post();
                $post_id  = get_the_ID();
                $type     = get_post_type();
                $priority = '0.8';

                if ($type === 'agency') {
                    $priority = '0.9';
                    $loc = home_url('/annuaire/?id=' . get_post_field('post_name', $post_id));
                } else {
                    $loc = get_permalink();
                }

                echo '  <url>' . "\n";
                echo '    <loc>' . esc_url($loc) . '</loc>' . "\n";
                echo '    <lastmod>' . get_the_modified_date('c') . '</lastmod>' . "\n";
                echo '    <changefreq>weekly</changefreq>' . "\n";
                echo '    <priority>' . $priority . '</priority>' . "\n";
                echo '  </url>' . "\n";
            }
            wp_reset_postdata();
        }

        echo '</urlset>' . "\n";
        exit;
    }
});

/**
 * Helper to get ACF field/sub-field value with a fallback default value
 * ONLY when the field is unset or newly created (i.e. returns null or false).
 * If the user explicitly cleared the field (returns empty string ""), we return "" to keep it empty/hidden.
 */
function v5_get_field_default($field_name, $default_value = '', $is_sub_field = true) {
    $value = $is_sub_field ? get_sub_field($field_name) : get_field($field_name);
    if ($value === null || $value === false) {
        return $default_value;
    }
    return $value;
}

