<?php
/**
 * Layout: Search Filter Section
 * Standalone flexible content layout for the search filter block.
 */
$service_terms = get_terms(array(
    'taxonomy'   => 'agency_service',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
));
if (is_wp_error($service_terms)) {
    $service_terms = array();
}

$city_terms = get_terms(array(
    'taxonomy'   => 'agency_city',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
));
if (is_wp_error($city_terms)) {
    $city_terms = array();
}

$fallback_services = array(
    array('value' => 'SEO', 'label' => 'SEO (Référencement)'),
    array('value' => 'Paid Ads', 'label' => 'Publicité Payante'),
    array('value' => 'Social Media', 'label' => 'Réseaux Sociaux'),
    array('value' => 'Web Design', 'label' => 'Design Web'),
    array('value' => 'Branding', 'label' => 'Image de Marque'),
    array('value' => 'Content Marketing', 'label' => 'Marketing de Contenu'),
);

$fallback_cities = array(
    array('value' => 'Casablanca', 'label' => 'Casablanca'),
    array('value' => 'Rabat', 'label' => 'Rabat'),
    array('value' => 'Tangier', 'label' => 'Tanger'),
    array('value' => 'Marrakech', 'label' => 'Marrakech'),
    array('value' => 'Agadir', 'label' => 'Agadir'),
);
?>
<section class="relative z-10 py-6 bg-white/80 backdrop-blur-sm search-filter-section">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <!-- Search Block -->
        <div class="max-w-3xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm p-4 md:p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Service</label>
                    <div class="relative">
                        <select id="home-filter-service" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-[13px] text-slate-700 search-focus appearance-none cursor-pointer">
                            <option value="all">Tous les services</option>
                            <?php if (!empty($service_terms)) : ?>
                                <?php foreach ($service_terms as $term) : ?>
                                    <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <?php foreach ($fallback_services as $service) : ?>
                                    <option value="<?php echo esc_attr($service['value']); ?>"><?php echo esc_html($service['label']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Ville</label>
                    <div class="relative">
                        <select id="home-filter-city" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-[13px] text-slate-700 search-focus appearance-none cursor-pointer">
                            <option value="all">Toutes les villes</option>
                            <?php if (!empty($city_terms)) : ?>
                                <?php foreach ($city_terms as $term) : ?>
                                    <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <?php foreach ($fallback_cities as $city) : ?>
                                    <option value="<?php echo esc_attr($city['value']); ?>"><?php echo esc_html($city['label']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Note minimale</label>
                    <div class="relative">
                        <select id="home-filter-rating" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-[13px] text-slate-700 search-focus appearance-none cursor-pointer">
                            <option value="any">Toutes les notes</option>
                            <option value="4.5">4.5+ Étoiles</option>
                            <option value="4.0">4.0+ Étoiles</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-end">
                    <button onclick="triggerHomeSearch()" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 rounded-lg text-[13px] transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>Rechercher</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
