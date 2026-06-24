<?php
/**
 * Guides Section Layout (v5-digital)
 */
$eyebrow   = v5_get_field_default('eyebrow', 'Éditorial');
$title     = v5_get_field_default('title', 'Derniers guides & analyses.');
$link_text = v5_get_field_default('link_text', 'Tous les articles');
$link_url  = v5_get_field_default('link_url', home_url('/blog/'));
$selected_post_ids = v5_get_field_default('posts', null);

$has_eyebrow = !empty($eyebrow);
$has_title   = !empty($title);
$has_link    = !empty($link_text);
$has_posts   = $selected_post_ids !== ""; // If cleared, it returns "". If unset, it returns null.

if ($has_eyebrow || $has_title || $has_link || $has_posts) :

$query_args = array(
    'post_type'      => 'blog',
    'post_status'    => 'publish'
);

if (!empty($selected_post_ids)) {
    if (!is_array($selected_post_ids)) {
        $selected_post_ids = array($selected_post_ids);
    }
    $query_args['post__in'] = $selected_post_ids;
    $query_args['orderby'] = 'post__in';
    $query_args['posts_per_page'] = count($selected_post_ids);
} else {
    $query_args['posts_per_page'] = 3;
}

$guides_query = new WP_Query($query_args);
?>

<section class="py-14 bg-white/70 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <?php if ($has_eyebrow || $has_title || $has_link) : ?>
        <div class="flex items-center justify-between mb-8">
            <div>
                <?php if ($has_eyebrow) : ?>
                    <span class="section-label text-slate-800 mb-1 block"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                <?php if ($has_title) : ?>
                    <h2 class="text-[1.5rem] font-bold text-slate-900 tracking-tight font-display"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
            <?php if ($has_link) : ?>
                <button onclick="window.location.href='<?php echo esc_url($link_url); ?>'" class="hidden sm:flex items-center gap-1 text-[13px] font-semibold text-brand-600 hover:text-brand-700">
                    <span><?php echo esc_html($link_text); ?></span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <?php 
            if ($guides_query->have_posts()) : 
                while ($guides_query->have_posts()) : $guides_query->the_post(); 
                    $post_id = get_the_ID();
                    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'medium');
                    if (empty($thumbnail_url)) {
                        // Attempt to fallback to seeded cover image url field
                        $seeded_image = get_field('cover_image_url', $post_id);
                        $thumbnail_url = !empty($seeded_image) ? $seeded_image : 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=400&fit=crop';
                    }
                    
                    // Retrieve category/tag name or fallback
                    $badge = get_field('badge', $post_id);
                    if (empty($badge)) {
                        $categories = get_the_category($post_id);
                        $badge = !empty($categories) ? $categories[0]->name : 'Analyse';
                    }
                    ?>
                    <div onclick="window.location.href='<?php the_permalink(); ?>'" class="card-hover bg-white border border-slate-200 rounded-xl overflow-hidden cursor-pointer group flex flex-col">
                        <div class="h-48 overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <span class="text-[11px] font-semibold text-slate-800 uppercase tracking-wider"><?php echo esc_html($badge); ?></span>
                            <h3 class="font-bold text-slate-900 mt-1 mb-2 text-[15px] font-display group-hover:text-brand-600 transition-colors line-clamp-2"><?php the_title(); ?></h3>
                            <p class="text-[13px] text-slate-500 leading-relaxed mb-4 line-clamp-3"><?php echo esc_html(get_the_excerpt()); ?></p>
                            <div class="flex items-center gap-2 mt-auto text-[11px] text-slate-400 font-mono">
                                <span>Mis à jour le <?php echo get_the_modified_date('j F Y'); ?></span>
                                <span>&middot;</span>
                                <span><?php echo esc_html(strval(max(3, ceil(str_word_count(strip_tags(get_the_content())) / 200)))); ?> min</span>
                            </div>
                        </div>
                    </div>
                    <?php 
                endwhile; 
                wp_reset_postdata(); 
            else :
                // Fallback static guides in French with smart dynamic link routing
                $fallback_links = array(
                    'top-agencies' => home_url('/blog/'),
                    'seo-casablanca' => home_url('/blog/'),
                    'social-media-compared' => home_url('/blog/')
                );
                foreach ($fallback_links as $slug => $default) {
                    $p = get_page_by_path($slug, OBJECT, 'blog');
                    if ($p) {
                        $fallback_links[$slug] = get_permalink($p->ID);
                    }
                }
                ?>
                <div onclick="window.location.href='<?php echo esc_url($fallback_links['top-agencies']); ?>'" class="card-hover bg-white border border-slate-200 rounded-xl overflow-hidden cursor-pointer group flex flex-col">
                    <div class="h-48 overflow-hidden bg-slate-100 flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=400&fit=crop" alt="Top Agences de Marketing" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <span class="text-[11px] font-semibold text-slate-800 uppercase tracking-wider">Classement</span>
                        <h3 class="font-bold text-slate-900 mt-1 mb-2 text-[15px] font-display group-hover:text-brand-600 transition-colors">Top Agences de Marketing Digital au Maroc</h3>
                        <p class="text-[13px] text-slate-500 leading-relaxed mb-4">Notre sélection définitive des meilleures agences de marketing basées sur les avis vérifiés et les données de performance.</p>
                        <div class="flex items-center gap-2 mt-auto text-[11px] text-slate-400 font-mono">
                            <span>Mis à jour Juin 2026</span>
                            <span>&middot;</span>
                            <span>8 min</span>
                        </div>
                    </div>
                </div>

                <div onclick="window.location.href='<?php echo esc_url($fallback_links['seo-casablanca']); ?>'" class="card-hover bg-white border border-slate-200 rounded-xl overflow-hidden cursor-pointer group flex flex-col">
                    <div class="h-48 overflow-hidden bg-slate-100 flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=400&fit=crop" alt="Agences SEO Casablanca" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <span class="text-[11px] font-semibold text-slate-800 uppercase tracking-wider">Guide</span>
                        <h3 class="font-bold text-slate-900 mt-1 mb-2 text-[15px] font-display group-hover:text-brand-600 transition-colors">Meilleures Agences SEO à Casablanca</h3>
                        <p class="text-[13px] text-slate-500 leading-relaxed mb-4">Une liste d'experts en référencement naturel aidant les marques marocaines à se positionner en tête des résultats de recherche.</p>
                        <div class="flex items-center gap-2 mt-auto text-[11px] text-slate-400 font-mono">
                            <span>Mis à jour Mai 2026</span>
                            <span>&middot;</span>
                            <span>6 min</span>
                        </div>
                    </div>
                </div>

                <div onclick="window.location.href='<?php echo esc_url($fallback_links['social-media-compared']); ?>'" class="card-hover bg-white border border-slate-200 rounded-xl overflow-hidden cursor-pointer group flex flex-col">
                    <div class="h-48 overflow-hidden bg-slate-100 flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&h=400&fit=crop" alt="Agences Réseaux Sociaux" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <span class="text-[11px] font-semibold text-slate-800 uppercase tracking-wider">Comparatif</span>
                        <h3 class="font-bold text-slate-900 mt-1 mb-2 text-[15px] font-display group-hover:text-brand-600 transition-colors">Comparatif des Agences Social Media</h3>
                        <p class="text-[13px] text-slate-500 leading-relaxed mb-4">Une comparaison détaillée des meilleures agences de gestion des réseaux sociaux au Maroc, budgets et expertises.</p>
                        <div class="flex items-center gap-2 mt-auto text-[11px] text-slate-400 font-mono">
                            <span>Mis à jour Avril 2026</span>
                            <span>&middot;</span>
                            <span>10 min</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
