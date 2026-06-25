<?php
/**
 * Stats Band Layout (agence-marketing-digital)
 */
$stats_query = new WP_Query(array(
    'post_type' => 'stat_metric',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

?>
<section class="py-12 bg-white/60 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-7 max-w-4xl mx-auto">
            <?php 
            if ($stats_query->have_posts()) : 
                while ($stats_query->have_posts()) : $stats_query->the_post(); 
                    $number = get_field('stat_number');
                    $label  = get_field('stat_label');
                    ?>
                    <div class="text-center">
                        <div class="text-[1.35rem] md:text-[1.65rem] font-extrabold text-slate-900 tracking-tight font-display"><?php echo esc_html($number); ?></div>
                        <div class="text-[10px] md:text-[11px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono"><?php echo esc_html($label); ?></div>
                    </div>
                    <?php 
                endwhile; 
                wp_reset_postdata(); 
            else :
                // Fallback static metrics in French
                ?>
                <div class="text-center">
                    <div class="text-[1.35rem] md:text-[1.65rem] font-extrabold text-slate-900 tracking-tight font-display">150+</div>
                    <div class="text-[10px] md:text-[11px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono">Agences analysées et répertoriées</div>
                </div>
                <div class="text-center">
                    <div class="text-[1.35rem] md:text-[1.65rem] font-extrabold text-slate-900 tracking-tight font-display">2 400+</div>
                    <div class="text-[10px] md:text-[11px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono">Avis clients vérifiés par notre équipe</div>
                </div>
                <div class="text-center">
                    <div class="text-[1.35rem] md:text-[1.65rem] font-extrabold text-slate-900 tracking-tight font-display">6</div>
                    <div class="text-[10px] md:text-[11px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono">Villes marocaines couvertes</div>
                </div>
                <div class="text-center">
                    <div class="text-[1.35rem] md:text-[1.65rem] font-extrabold text-slate-900 tracking-tight font-display">0</div>
                    <div class="text-[10px] md:text-[11px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono">Placements payants ou rangs sponsorisés</div>
                </div>
                <?php
            endif; 
            ?>
        </div>
    </div>
</section>
