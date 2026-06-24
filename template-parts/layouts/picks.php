<?php
/**
 * Editor's Picks Layout (v5-digital)
 */
$eyebrow   = v5_get_field_default('eyebrow', "Choix de l'Éditeur");
$title     = v5_get_field_default('title', "Les agences les plus performantes ce mois-ci.");
$link_text = v5_get_field_default('link_text', "Comment nous évaluons");
$link_url  = v5_get_field_default('link_url', home_url('/methodologie/'));
$selected_agencies = v5_get_field_default('selected_agencies', null);

$has_eyebrow = !empty($eyebrow);
$has_title   = !empty($title);
$has_link    = !empty($link_text);
$has_picks   = $selected_agencies !== ""; // True if unset (null, query top 3) or array of post IDs

if ($has_eyebrow || $has_title || $has_link || $has_picks) :

// Check for manually selected agencies
if (!empty($selected_agencies) && is_array($selected_agencies)) {
    $picks_query = new WP_Query(array(
        'post_type'      => 'agency',
        'post__in'       => $selected_agencies,
        'posts_per_page' => count($selected_agencies),
        'post_status'    => 'publish',
        'orderby'        => 'post__in'
    ));
} else {
    // Default: Query top 3 agencies sorted by their rank (1 to 3)
    $picks_query = new WP_Query(array(
        'post_type'      => 'agency',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'meta_key'       => 'agency_rank',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC'
    ));
}

// Compute dynamic grid column classes based on post count
$picks_count = $picks_query->post_count;
$grid_cols_class = 'md:grid-cols-3';
if ($picks_count === 1) {
    $grid_cols_class = 'max-w-md mx-auto';
} elseif ($picks_count === 2) {
    $grid_cols_class = 'md:grid-cols-2 max-w-3xl mx-auto';
} elseif ($picks_count === 4) {
    $grid_cols_class = 'md:grid-cols-2 lg:grid-cols-4';
}

if (!function_exists('theme_render_stars_html')) {
    function theme_render_stars_html($rating) {
        $html = '';
        $floor = floor($rating);
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $floor) {
                $html .= '<i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400 inline"></i>';
            } else if ($i - $rating < 1) {
                // Lucide star-half or star depending on theme preference
                $html .= '<i data-lucide="star-half" class="w-3.5 h-3.5 text-amber-400 fill-amber-400 inline"></i>';
            } else {
                $html .= '<i data-lucide="star" class="w-3.5 h-3.5 text-slate-200 inline"></i>';
            }
        }
        return $html;
    }
}
?>

<section class="py-14 md:py-18 bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-10">
            <div>
                <?php if ($has_eyebrow) : ?>
                    <span class="section-label text-slate-800 mb-2 block"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                <?php if ($has_title) : ?>
                    <h2 class="text-[1.75rem] font-bold text-slate-900 tracking-tight font-display"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
            <?php if ($has_link) : ?>
                <a href="<?php echo esc_url($link_url); ?>" class="mt-3 sm:mt-0 text-[13px] font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition-colors">
                    <span><?php echo esc_html($link_text); ?></span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            <?php endif; ?>
        </div>
        
        <div class="grid grid-cols-1 <?php echo esc_attr($grid_cols_class); ?> gap-5" id="home-picks-container">
            <?php 
            if ($picks_query->have_posts()) : 
                $index = 0;
                while ($picks_query->have_posts()) : $picks_query->the_post(); 
                    $post_id     = get_the_ID();
                    $rating      = get_field('rating_value', $post_id);
                    $reviews     = get_field('review_count', $post_id);
                    $rank        = get_field('agency_rank', $post_id);
                    $logo_image  = get_field('logo_image', $post_id);
                    $logo_url    = get_field('logo_image_url', $post_id);
                    $logo_src    = !empty($logo_image) ? (is_array($logo_image) ? $logo_image['url'] : $logo_image) : $logo_url;
                    $logo_text   = get_field('logo_text', $post_id);
                    
                    // Get taxonomy values
                    $services = wp_get_post_terms($post_id, 'agency_service', array('fields' => 'names'));
                    $cities   = wp_get_post_terms($post_id, 'agency_city', array('fields' => 'names'));
                    $city_name = !empty($cities) ? $cities[0] : 'Maroc';

                    // Podium indicators
                    $podiumBadges = array(
                        array('icon' => 'trophy', 'classes' => 'bg-amber-50 text-amber-700 border-amber-200'),
                        array('icon' => 'medal', 'classes' => 'bg-slate-50 text-slate-700 border-slate-200'),
                        array('icon' => 'award', 'classes' => 'bg-blue-50 text-blue-700 border-blue-200')
                    );
                    $podium = isset($podiumBadges[$index]) ? $podiumBadges[$index] : $podiumBadges[2];
                    $index++;
                    ?>
                    
                    <div class="card-hover min-h-[250px] bg-white rounded-xl border border-slate-200 p-6 relative shadow-sm hover:shadow-md transition-all flex flex-col group">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <?php if (!empty($logo_src)) : ?>
                                    <img src="<?php echo esc_url($logo_src); ?>" alt="<?php the_title_attribute(); ?>" class="w-11 h-11 rounded-lg object-cover border border-slate-100 bg-white">
                                <?php else : ?>
                                    <div class="w-11 h-11 rounded-lg border border-slate-100 bg-slate-50 flex items-center justify-center font-bold text-slate-400 uppercase font-mono text-[12px]">
                                        <?php echo esc_html($logo_text ? $logo_text : substr(get_the_title(), 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-[15px] text-slate-900 font-display truncate"><?php the_title(); ?></h3>
                                    <div class="flex items-center gap-1">
                                        <?php echo theme_render_stars_html($rating ? $rating : 4.5); ?>
                                        <span class="text-[13px] font-semibold text-slate-700 font-mono"><?php echo esc_html($rating ? $rating : '4.5'); ?></span>
                                        <span class="text-[11px] text-slate-400 font-mono">(<?php echo esc_html($reviews ? $reviews : '10'); ?>)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-xl border <?php echo esc_attr($podium['classes']); ?> flex flex-col items-center justify-center flex-shrink-0 shadow-sm">
                                <i data-lucide="<?php echo esc_attr($podium['icon']); ?>" class="w-4 h-4 mb-0.5"></i>
                                <span class="text-[13px] leading-none font-extrabold font-display"><?php echo esc_html($rank ? $rank : $index); ?></span>
                            </div>
                        </div>
                        <p class="text-[13px] text-slate-500 mb-4 leading-relaxed min-h-[42px] overflow-hidden">
                            <?php echo esc_html(get_the_excerpt()); ?>
                        </p>
                        <div class="flex flex-wrap gap-1 mb-4 min-h-[24px]">
                            <?php 
                            $tag_count = 0;
                            foreach ($services as $service_name) {
                                $tag_count++;
                                if ($tag_count > 3) break;
                                ?>
                                <span class="tag-pill bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-mono text-[11px] border border-slate-150"><?php echo esc_html($service_name); ?></span>
                                <?php
                            }
                            ?>
                        </div>
                        <?php 
                        $website = get_post_meta($post_id, 'website', true); 
                        $btn_text = $website ? "Visiter le site" : "Voir Profil";
                        $btn_link = $website ? (strpos($website, 'http') === 0 ? $website : 'https://' . $website) : home_url('/annuaire/?id=' . get_post_field('post_name', $post_id));
                        $is_external = !empty($website);
                        ?>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-auto">
                            <span class="text-[12px] text-slate-500 flex items-center gap-1 font-mono">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                <?php echo esc_html($city_name); ?>
                            </span>
                            <a href="<?php echo esc_url($btn_link); ?>" 
                               <?php echo $is_external ? 'target="_blank" rel="noopener"' : ''; ?> 
                               class="text-[12.5px] font-bold text-brand-600 hover:text-brand-700 transition-colors font-mono flex items-center gap-1">
                                <span><?php echo esc_html($btn_text); ?></span>
                                <?php if ($is_external) : ?>
                                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-brand-500"></i>
                                <?php else : ?>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-brand-500"></i>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php 
                endwhile; 
                wp_reset_postdata(); 
            else :
                // Static Fallback Agencies in French
                ?>
                <div class="text-center py-10 col-span-3 text-slate-400">Aucune agence répertoriée pour le moment.</div>
                <?php
            endif; 
            ?>
        </div>
    </div>
</section>
<?php endif; ?>
