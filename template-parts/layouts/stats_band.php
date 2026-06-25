<?php
/**
 * Stats Band Layout (agence-marketing-digital)
 *
 * Data source: the "Statistiques" CPT (stat_metric). The numbers/labels are
 * edited there. This flexible-content block only controls WHERE the band
 * appears, so it stays movable and can be reused on other pages.
 */
$stat_query = new WP_Query(array(
    'post_type'      => 'stat_metric',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'orderby'        => array('menu_order' => 'ASC', 'date' => 'ASC'),
));

if (!$stat_query->have_posts()) {
    wp_reset_postdata();
    return;
}
?>
<section class="relative z-20 -mt-11 md:-mt-16 pb-9 md:pb-12 bg-transparent">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 max-w-3xl mx-auto">
            <?php while ($stat_query->have_posts()) : $stat_query->the_post();
                $number = get_field('stat_number');
                $label  = get_field('stat_label');
                ?>
                <div class="text-center">
                    <div class="text-[1rem] md:text-[1.15rem] font-extrabold text-slate-900 tracking-tight font-display"><?php echo esc_html($number); ?></div>
                    <div class="text-[8.5px] md:text-[9.5px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono leading-snug"><?php echo esc_html($label); ?></div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
