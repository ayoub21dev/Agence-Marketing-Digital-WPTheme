<?php
/**
 * Shared article template used by native WordPress posts.
 */

get_header();
?>

<style>
    /* Prevent body background conflict but keep grid look */
    body {
        background-color: oklch(98.5% 0.004 250);
        background-image: radial-gradient(oklch(88% 0.005 250) 1px, transparent 1px);
        background-size: 24px 24px;
        background-position: center top;
    }

    .article-prose p {
        margin-bottom: 1.25rem;
        font-size: 14.5px;
        line-height: 1.7;
        color: #475569;
    }
    .article-prose h2 {
        font-family: 'Space Grotesk', system-ui, sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }
    .article-prose h3 {
        font-family: 'Space Grotesk', system-ui, sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }
    .article-prose strong { font-weight: 600; color: #0f172a; }
</style>

<main class="flex-grow">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            // Fetch fields
            $badge = v5_digital_get_post_badge(get_the_ID(), 'Guide');
            
            $read_time = v5_digital_get_field('read_time');
            if (!$read_time) $read_time = '5 min de lecture';
            
            $author = get_the_author();

            $blog_page = get_page_by_path('blog');
            $blog_url  = $blog_page ? get_permalink($blog_page->ID) : home_url('/blog/');
            
            // Image logic
            $cover_image = v5_digital_get_field('cover_image_media');
            if (!$cover_image) {
                $cover_image = v5_digital_get_field('cover_image_url');
            }
            if (!$cover_image && has_post_thumbnail()) {
                $cover_image = get_the_post_thumbnail_url(null, 'large');
            }
            ?>
            <!-- ==================== ARTICLE DETAIL PAGE ==================== -->
            <div class="page" id="page-article">
                <div class="bg-white/80 border-b border-slate-200 backdrop-blur-sm">
                    <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8 py-8">
                        
                        <!-- Breadcrumbs & Back -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-1.5 text-[12px] text-slate-500 font-mono">
                                <a class="cursor-pointer hover:text-slate-900" href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
                                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                <a class="cursor-pointer hover:text-slate-900" href="<?php echo esc_url($blog_url); ?>">Blog</a>
                                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                <span class="text-slate-900 font-semibold truncate max-w-[150px] sm:max-w-xs"><?php the_title(); ?></span>
                            </div>
                            <a href="<?php echo esc_url($blog_url); ?>" class="bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg px-2.5 py-1 text-[11px] text-slate-600 flex items-center gap-1 transition-all no-underline">
                                <i data-lucide="arrow-left" class="w-3 h-3"></i> <span>retour aux articles</span>
                            </a>
                        </div>

                        <!-- Category Badge -->
                        <span class="inline-block bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-mono font-bold px-2.5 py-1 rounded mb-3 uppercase tracking-wider"><?php echo esc_html($badge); ?></span>
                        
                        <h1 class="text-[1.75rem] md:text-[2.25rem] font-extrabold text-slate-900 tracking-tight leading-tight mb-4 font-display"><?php the_title(); ?></h1>
                        
                        <div class="flex flex-wrap items-center gap-3 text-[12px] text-slate-500 font-mono">
                            <span class="flex items-center gap-1"><i data-lucide="user" class="w-3.5 h-3.5"></i> <span><?php echo esc_html(v5_t('Par')); ?> <?php echo esc_html($author); ?></span></span>
                            <span>&middot;</span>
                            <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> <span><?php echo get_the_date('d M Y'); ?></span></span>
                            <span>&middot;</span>
                            <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> <span><?php echo esc_html($read_time); ?></span></span>
                        </div>
                    </div>
                </div>

                <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8 py-8">
                    <?php if ($cover_image) : ?>
                        <!-- Cover Image -->
                        <div class="h-64 sm:h-80 overflow-hidden rounded-2xl mb-8 bg-slate-100 border border-slate-200">
                            <img src="<?php echo esc_url($cover_image); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>

                    <!-- Native editor content plus optional Flexible Content blocks -->
                    <div class="article-prose">
                        <?php
                        if (trim(get_the_content()) !== '') :
                            the_content();
                        endif;

                        $agency_reviews = array();

                        if (v5_digital_have_rows('blog_layouts')) :
                            while (v5_digital_have_rows('blog_layouts')) : v5_digital_the_row();
                                $layout = v5_digital_get_row_layout();
                                if ($layout === 'agency_reviews_block') :
                                    $reviews = v5_digital_get_sub_field('reviews_list');
                                    if (!empty($reviews) && is_array($reviews)) :
                                        foreach ($reviews as $review) {
                                            $agency_reviews[] = $review;
                                        }
                                    endif;
                                    endif;

                            endwhile;
                        endif;

                        if (!empty($agency_reviews)) :
                                        ?>
                                        <div class="mt-10 pt-8 border-t border-slate-200">
                                            <h3 class="font-extrabold text-[16px] text-slate-900 uppercase font-display tracking-wide mb-6">Analyses Éditoriales</h3>
                                            <div class="space-y-4">
                                                <?php foreach ($agency_reviews as $rev) :
                                                    $agency_id = $rev['agency'];
                                                    if (!$agency_id) continue;

                                                    $agency_post = get_post($agency_id);
                                                    if (!$agency_post) continue;

                                                    // Logo: ACF media → ACF URL field → fallback text avatar
                                                    $logo_image_field = v5_digital_get_field('logo_image', $agency_id);
                                                    $logo_image_url   = v5_digital_get_field('logo_image_url', $agency_id);
                                                    $logo_text        = v5_digital_get_field('logo_text', $agency_id) ?: strtoupper(substr($agency_post->post_title, 0, 3));
                                                    if (!empty($logo_image_field)) {
                                                        $logo_src = is_array($logo_image_field) ? $logo_image_field['url'] : $logo_image_field;
                                                    } elseif (!empty($logo_image_url)) {
                                                        $logo_src = $logo_image_url;
                                                    } else {
                                                        $logo_src = null;
                                                    }

                                                    // Agency website from postmeta
                                                    $website = get_post_meta($agency_id, 'website', true);
                                                    $rank    = intval($rev['rank']);
                                                    $link_text = isset($rev['link_text']) ? trim((string) $rev['link_text']) : '';
                                                    if ($link_text === '') {
                                                        $link_text = 'Visiter le Site';
                                                    }
                                                    ?>
                                                    <div class="border border-slate-200 rounded-xl p-5 md:p-6 bg-slate-50/30 hover:bg-slate-50/50 transition-colors shadow-sm relative">
                                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3 pb-3 border-b border-slate-100">
                                                            <div class="flex items-center gap-3">
                                                                <?php if ($logo_src) : ?>
                                                                    <img src="<?php echo esc_url($logo_src); ?>"
                                                                         alt="<?php echo esc_attr($agency_post->post_title); ?> Logo"
                                                                         class="w-10 h-10 rounded-lg object-cover border border-slate-200 bg-white">
                                                                <?php else : ?>
                                                                    <div class="w-10 h-10 rounded-lg border border-slate-200 bg-brand-600 flex items-center justify-center text-white font-extrabold text-[12px] font-mono tracking-wider">
                                                                        <?php echo esc_html($logo_text); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <h4 class="font-bold text-slate-900 text-[15px] font-display"><?php echo esc_html($agency_post->post_title); ?></h4>
                                                                    <span class="inline-block bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-mono font-bold px-2 py-0.5 rounded uppercase mt-0.5"><?php echo esc_html($rev['badge']); ?></span>
                                                                </div>
                                                            </div>
                                                            <span class="bg-amber-500 text-white px-2.5 py-0.5 rounded shadow-sm text-[11px] font-mono self-start uppercase font-bold">RANK <?php echo $rank; ?></span>
                                                        </div>
                                                        <p class="text-[13px] text-slate-500 leading-relaxed mb-4"><?php echo wp_kses_post($rev['description']); ?></p>
                                                        <div class="flex gap-2 flex-wrap">
                                                            <?php if ($website) : ?>
                                                                <a href="<?php echo esc_url(strpos($website, 'http') === 0 ? $website : 'https://' . $website); ?>"
                                                                   target="_blank" rel="noopener noreferrer"
                                                                   class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4 py-2 rounded-lg text-[12px] transition-colors font-mono flex items-center gap-1.5 no-underline">
                                                                    <span><?php echo esc_html($link_text); ?></span>
                                                                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-white/80"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php
                        endif;
                        ?>
                    </div>

                </div>
            </div>
            <?php
        endwhile;
    endif;
    ?>
</main>

<?php
get_footer();
