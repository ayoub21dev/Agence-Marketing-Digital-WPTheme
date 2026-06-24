<?php
/**
 * Hero Section Layout (v5-digital)
 * Now supports dynamic CTAs (with styling and icons) and dynamic stats.
 */
$eyebrow         = v5_get_field_default('eyebrow', '01 · MATCH & DISCOVER');
$title           = v5_get_field_default('title', 'Trouvez les <span class="hero-focus-word">meilleures</span> agences de marketing digital au <span class="hero-location-word">Maroc</span>');
$description     = v5_get_field_default('description', 'Comparez les agences digitales marocaines grâce à des recherches éditoriales, des scores techniques de vitesse et des avis clients vérifiés.');
$social_proof_1  = v5_get_field_default('social_proof_1', '150+ agences évaluées');
$social_proof_2  = v5_get_field_default('social_proof_2', 'Référencement 100% éditorial');
?>

<section class="relative z-10 bg-white/80 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 pt-8 pb-10 md:pt-12 md:pb-16">
        <div class="max-w-3xl mx-auto text-center mb-8">
            <?php if ($eyebrow) : ?>
                <span class="section-label text-slate-400 mb-3 block"><?php echo esc_html($eyebrow); ?></span>
            <?php endif; ?>
            
            <?php if ($title) : ?>
                <h1 class="hero-title text-[2.25rem] md:text-[3.5rem] font-extrabold text-slate-900 tracking-tight leading-[1.1] mb-5 font-display">
                    <?php echo wp_kses_post($title); ?>
                </h1>
            <?php endif; ?>
            
            <?php if ($description) : ?>
                <p class="text-[16px] md:text-[18px] text-slate-500 mb-8 leading-relaxed max-w-xl mx-auto">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>
            
            <!-- Dynamic CTA Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-10 hero-actions">
                <?php 
                if (have_rows('hero_ctas')) :
                    while (have_rows('hero_ctas')) : the_row();
                        $text       = get_sub_field('text');
                        $link_type  = get_sub_field('link_type');
                        $url        = get_sub_field('url');
                        $page       = get_sub_field('page');
                        $style      = get_sub_field('style');
                        $bg_color   = get_sub_field('bg_color');
                        $text_color = get_sub_field('text_color');
                        $icon       = get_sub_field('icon');

                        // Resolve link URL
                        $link = ($link_type === 'page' && !empty($page)) ? $page : $url;
                        if (empty($link)) $link = '#';

                        // Resolve styles & classes
                        $classes = '';
                        $styles = '';
                        if ($style === 'primary') {
                            $classes = 'bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-lg shadow-brand-600/10';
                        } elseif ($style === 'secondary') {
                            $classes = 'bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 hover:border-slate-300';
                        } elseif ($style === 'custom') {
                            $classes = 'font-bold transition-all';
                            if (!empty($bg_color)) $styles .= 'background-color:' . esc_attr($bg_color) . ';';
                            if (!empty($text_color)) $styles .= 'color:' . esc_attr($text_color) . ';';
                        }

                        // Determine if it is external
                        $is_external = (strpos($link, 'http') === 0 && strpos($link, home_url()) === false);
                        $target = $is_external ? ' target="_blank" rel="noopener"' : '';
                        ?>
                        <a href="<?php echo esc_url($link); ?>"
                           <?php echo $target; ?>
                           <?php if (!empty($styles)) echo 'style="' . $styles . '"'; ?>
                           class="w-full sm:w-auto px-7 py-3.5 rounded-xl text-[14px] transition-all flex items-center justify-center gap-2.5 <?php echo esc_attr($classes); ?>">
                            
                            <?php if ($icon && $icon !== 'none') : ?>
                                <i data-lucide="<?php echo esc_attr($icon); ?>" class="w-4 h-4"></i>
                            <?php endif; ?>
                            
                            <span><?php echo esc_html($text); ?></span>
                        </a>
                        <?php
                    endwhile;
                else :
                    // Fallback to static CTAs if not populated
                    ?>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="bg-brand-600 hover:bg-brand-700 text-white font-bold px-7 py-3.5 rounded-xl text-[14px] transition-all flex items-center justify-center gap-2.5 shadow-lg shadow-brand-600/10">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Trouver mon agence</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/?subject=listing')); ?>" class="bg-white hover:bg-slate-50 text-slate-800 font-bold px-7 py-3.5 rounded-xl text-[14px] border border-slate-200 hover:border-slate-300 transition-all flex items-center justify-center gap-2.5">
                        <i data-lucide="home" class="w-4 h-4 text-brand-600"></i>
                        <span>Faire référencer mon agence</span>
                    </a>
                    <?php
                endif;
                ?>
            </div>

            <!-- Social proof micro-line -->
            <div class="flex items-center justify-center gap-2 text-[12px] text-slate-400 font-mono mb-8 -mt-4">
                <span class="flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i>
                    <span><?php echo esc_html($social_proof_1); ?></span>
                </span>
                <span class="text-slate-200">·</span>
                <span class="flex items-center gap-1">
                    <i data-lucide="award" class="w-3.5 h-3.5 text-brand-400"></i>
                    <span><?php echo esc_html($social_proof_2); ?></span>
                </span>
            </div>

            <!-- Dynamic stats inside hero (aligned with site's visual identity) -->
            <?php if (have_rows('hero_stats')) : ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 max-w-3xl mx-auto pt-8 border-t border-slate-200/60 mt-8">
                    <?php while (have_rows('hero_stats')) : the_row();
                        $num = get_sub_field('number');
                        $lbl = get_sub_field('label');
                        ?>
                        <div class="text-center">
                            <div class="text-[2rem] md:text-[2.5rem] font-extrabold text-slate-900 tracking-tight font-display"><?php echo esc_html($num); ?></div>
                            <div class="text-[11px] text-slate-400 font-semibold mt-1 uppercase tracking-wider font-mono"><?php echo esc_html($lbl); ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
