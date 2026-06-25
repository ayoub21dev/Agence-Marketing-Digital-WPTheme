<?php
/**
 * 404 Error Page Template (agence-marketing-digital)
 *
 * Built to match the HOME PAGE identity exactly:
 *   - plain white background (theme body default — no article-page dot grid)
 *   - hero headline uses .hero-title + .hero-focus-word, so the SAME GSAP
 *     entrance from theme-scripts.js (animateHeroTitle) drives it: the accent
 *     digit fades/scales in then gains the brand blue + text-shadow.
 *   - identical button language to hero.php (primary brand / secondary white)
 *   - "Recent articles" reuse the home picks-card identity:
 *     card-hover + border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-all
 */
get_header();

// A few recent posts so the dead end becomes a soft landing.
$suggested = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'ignore_sticky_posts' => true,
));
?>

<style>
    /* Oversized glyph — sizing only. Colour + animation come from the shared
       .hero-focus-word token and the theme's GSAP hero entrance, so the accent
       behaves identically to the home page headline. */
    .err-code {
        font-size: clamp(96px, 18vw, 200px);
        line-height: 0.9;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: #101418;
    }
</style>

<main class="flex-grow">

    <!-- Hero: same backdrop treatment as the home hero (bg-white/80 + blur) -->
    <section class="relative z-10 bg-white/80 backdrop-blur-sm">
        <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8 pt-20 pb-16 md:pt-28 md:pb-20 text-center">

            <span class="section-label text-slate-400 mb-4 block">404 · Page introuvable</span>

            <!-- .hero-title + .hero-focus-word → animated by theme-scripts.js exactly like home -->
            <h1 class="err-code hero-title font-display mb-2">
                4<span class="hero-focus-word">0</span>4
            </h1>

            <h2 class="text-[1.5rem] md:text-[2rem] font-extrabold text-slate-900 tracking-tight leading-tight mb-4 font-display">
                Cette page s'est égarée.
            </h2>

            <p class="text-[15px] md:text-[16px] text-slate-500 leading-relaxed max-w-md mx-auto mb-8">
                Le lien est peut-être rompu ou la page a été déplacée. Pas d'inquiétude — voici quelques pistes pour retrouver votre chemin.
            </p>

            <!-- Buttons: identical classes to hero.php primary/secondary CTAs -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center mb-10 hero-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>"
                   class="w-full sm:w-auto px-7 py-3.5 rounded-xl text-[14px] transition-all flex items-center justify-center gap-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-lg shadow-brand-600/10">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span>Retour à l'accueil</span>
                </a>
                <button onclick="openSearchPalette()" type="button"
                   class="w-full sm:w-auto px-7 py-3.5 rounded-xl text-[14px] transition-all flex items-center justify-center gap-2.5 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 hover:border-slate-300 font-bold cursor-pointer">
                    <i data-lucide="search" class="w-4 h-4 text-brand-600"></i>
                    <span>Rechercher</span>
                </button>
            </div>

            <!-- Mono quick-links, same hover language as home breadcrumbs/links -->
            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[12px] text-slate-400 font-mono">
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5"></i><span>Blog</span>
                </a>
                <span class="text-slate-200">·</span>
                <a href="<?php echo esc_url(home_url('/methodologie/')); ?>" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <i data-lucide="award" class="w-3.5 h-3.5"></i><span>Méthodologie</span>
                </a>
                <span class="text-slate-200">·</span>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i><span>Contact</span>
                </a>
            </div>
        </div>
    </section>

    <?php if ($suggested->have_posts()) : ?>
    <!-- Recent articles — home picks-card identity (same section + card + hover) -->
    <section class="py-14 md:py-18 bg-white border-t border-slate-200">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-10">
                <div>
                    <span class="section-label text-slate-800 mb-2 block">Continuer la lecture</span>
                    <h2 class="text-[1.75rem] font-bold text-slate-900 tracking-tight font-display">Articles récents</h2>
                </div>
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="mt-3 sm:mt-0 text-[13px] font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition-colors">
                    <span>Tout le blog</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <?php
                while ($suggested->have_posts()) : $suggested->the_post();
                    $badge      = v5_digital_get_post_badge(get_the_ID(), 'Guide');
                    $read_time  = get_field('read_time') ?: '5 min de lecture';

                    $cover_image = get_field('cover_image_media');
                    if (!$cover_image) $cover_image = get_field('cover_image_url');
                    if (!$cover_image && has_post_thumbnail()) $cover_image = get_the_post_thumbnail_url(null, 'large');
                    if (!$cover_image) $cover_image = 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=400&fit=crop';
                    ?>
                    <div class="card-hover bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col cursor-pointer"
                         onclick="window.location.href='<?php the_permalink(); ?>'">
                        <div class="h-40 overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="<?php echo esc_url($cover_image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center justify-between mb-3">
                                <span class="tag-pill bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-mono text-[11px] border border-slate-150 uppercase font-bold tracking-wider"><?php echo esc_html($badge); ?></span>
                                <span class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i><?php echo esc_html($read_time); ?>
                                </span>
                            </div>
                            <h3 class="font-bold text-[15px] text-slate-900 leading-snug font-display mb-4"><?php the_title(); ?></h3>
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-auto">
                                <span class="text-[12px] text-slate-500 flex items-center gap-1 font-mono">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    <?php echo esc_html(get_field('author_name') ?: get_the_author()); ?>
                                </span>
                                <span class="text-[12.5px] font-bold text-brand-600 hover:text-brand-700 transition-colors font-mono flex items-center gap-1">
                                    <span>Lire</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-brand-500"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
get_footer();
