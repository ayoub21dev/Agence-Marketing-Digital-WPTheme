<?php
/**
 * Specialties Section Layout (v5-digital)
 */
$eyebrow     = v5_get_field_default('eyebrow', 'Spécialités');
$title       = v5_get_field_default('title', 'Trouvez votre besoin exact.');
$description = v5_get_field_default('description', 'Explorez notre annuaire par canal marketing ou par spécialité.');
$cta_text    = v5_get_field_default('view_all_cta_text', 'Voir tous les classements');

$hubs_query = new WP_Query(array(
    'post_type'      => 'specialty_hub',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
));
?>

<section class="py-14 bg-white/70 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <?php if (!empty($eyebrow) || !empty($title)) : ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <?php if (!empty($eyebrow)) : ?>
                    <span class="section-label text-slate-800 mb-1 block"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                <?php if (!empty($title)) : ?>
                    <h2 class="text-[1.5rem] font-bold text-slate-900 tracking-tight font-display"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php 
            if ($hubs_query->have_posts()) : 
                while ($hubs_query->have_posts()) : $hubs_query->the_post(); 
                    $post_id = get_the_ID();
                    $icon    = get_field('icon_svg', $post_id);
                    $param   = get_field('direct_link_parameter', $post_id);
                    ?>
                    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm transition-all">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-100 flex-shrink-0">
                                <?php 
                                if (!empty($icon)) {
                                    echo $icon; // Raw SVG icon
                                } else {
                                    echo '<i data-lucide="sparkles" class="w-4 h-4"></i>';
                                }
                                ?>
                            </div>
                            <h3 class="font-display text-[1.35rem] leading-tight font-bold text-slate-950"><?php the_title(); ?></h3>
                        </div>
                        
                        <?php if (have_rows('sub_services', $post_id)) : ?>
                            <div class="space-y-3 text-[14px]">
                                <?php while (have_rows('sub_services', $post_id)) : the_row(); ?>
                                    <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                                        <span><?php echo esc_html(get_sub_field('service_name')); ?></span>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php 
                endwhile; 
                wp_reset_postdata(); 
            else :
                // Fallback static hubs in French
                ?>
                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-100 flex-shrink-0">
                            <i data-lucide="search-check" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-display text-[1.35rem] leading-tight font-bold text-slate-950">SEO & Growth</h3>
                    </div>
                    <div class="space-y-3 text-[14px]">
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>SEO (Référencement)</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Publicité Google & Social</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Marketing de Contenu</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Optimisation des Conversions</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-100 flex-shrink-0">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-display text-[1.35rem] leading-tight font-bold text-slate-950">Marque & Réseaux</h3>
                    </div>
                    <div class="space-y-3 text-[14px]">
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Image de Marque (Branding)</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Gestion Réseaux Sociaux</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Campagnes d'Influence</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Publicités Sociales (SMM)</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-100 flex-shrink-0">
                            <i data-lucide="layout-template" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-display text-[1.35rem] leading-tight font-bold text-slate-950">Web & Conversion</h3>
                    </div>
                    <div class="space-y-3 text-[14px]">
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Conception de Sites Web</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Pages d'Atterrissage (Landing)</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>E-commerce (Boutiques)</span>
                        </div>
                        <div class="w-full flex items-center justify-between gap-3 text-left text-slate-600">
                            <span>Audits Techniques SEO & CRO</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
