<?php
/**
 * Stats Band Layout (v5-digital)
 */
$stats_query = new WP_Query(array(
    'post_type' => 'stat_metric',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));
?>
<section class="py-12 bg-white/60 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <?php 
            if ($stats_query->have_posts()) : 
                while ($stats_query->have_posts()) : $stats_query->the_post(); 
                    $number = get_field('stat_number');
                    $label  = get_field('stat_label');
                    ?>
                    <div class="text-left md:text-center">
                        <div class="text-[2.25rem] md:text-[3rem] font-extrabold text-slate-900 tracking-tight font-display"><?php echo esc_html($number); ?></div>
                        <div class="text-[12px] text-slate-500 font-semibold mt-1 uppercase tracking-wider"><?php echo esc_html($label); ?></div>
                    </div>
                    <?php 
                endwhile; 
                wp_reset_postdata(); 
            else :
                // Fallback static metrics in French
                ?>
                <div class="text-left md:text-center">
                    <div class="text-[2.25rem] md:text-[3rem] font-extrabold text-slate-900 tracking-tight font-display">150+</div>
                    <div class="text-[12px] text-slate-500 font-semibold mt-1 uppercase tracking-wider">Agences analysées et répertoriées</div>
                </div>
                <div class="text-left md:text-center">
                    <div class="text-[2.25rem] md:text-[3rem] font-extrabold text-slate-900 tracking-tight font-display">2 400+</div>
                    <div class="text-[12px] text-slate-500 font-semibold mt-1 uppercase tracking-wider">Avis clients vérifiés par notre équipe</div>
                </div>
                <div class="text-left md:text-center">
                    <div class="text-[2.25rem] md:text-[3rem] font-extrabold text-slate-900 tracking-tight font-display">6</div>
                    <div class="text-[12px] text-slate-500 font-semibold mt-1 uppercase tracking-wider">Villes marocaines couvertes</div>
                </div>
                <div class="text-left md:text-center">
                    <div class="text-[2.25rem] md:text-[3rem] font-extrabold text-slate-900 tracking-tight font-display">0</div>
                    <div class="text-[12px] text-slate-500 font-semibold mt-1 uppercase tracking-wider">Placements payants ou rangs sponsorisés</div>
                </div>
                <?php
            endif; 
            ?>
        </div>
    </div>
</section>
