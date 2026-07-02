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

    /* Reading progress bar */
    #reading-progress {
        position: fixed; top: 0; left: 0; height: 3px; width: 0;
        background: linear-gradient(90deg, #2563eb, #6366f1);
        z-index: 60; transition: width 0.1s linear; will-change: width;
    }

    /* Table of contents */
    .article-toc {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 18px 20px; margin-bottom: 28px;
    }
    .article-toc .toc-title {
        font-family: 'Space Grotesk', system-ui, sans-serif;
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.08em; color: #0f172a; margin: 0 0 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .article-toc ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
    .article-toc a {
        font-size: 13px; color: #475569; text-decoration: none;
        line-height: 1.4; transition: color 0.15s; display: block;
    }
    .article-toc a:hover { color: #2563eb; }
    .article-toc li.toc-h3 { padding-left: 14px; }
    .article-toc li.toc-h3 a { font-size: 12.5px; color: #64748b; }
    .article-prose h2, .article-prose h3 { scroll-margin-top: 80px; }

    /* In-content directory CTA */
    .article-cta {
        margin: 32px 0; padding: 24px; border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff, #f5f3ff);
        border: 1px solid #dbeafe;
    }

    /* End-of-article inline newsletter */
    .article-nl {
        margin-top: 40px; padding: 24px; border-radius: 14px;
        background: #0f172a; color: #fff;
    }

    /* Related posts */
    .related-card {
        display: block; background: #fff; border: 1px solid #e2e8f0;
        border-radius: 12px; overflow: hidden; text-decoration: none;
        transition: box-shadow 0.2s, transform 0.18s;
    }
    .related-card:hover { box-shadow: 0 8px 24px rgba(16,20,24,0.12); transform: translateY(-3px); }

    /* Prev/next article nav (self-contained; no Tailwind rebuild needed) */
    .article-nav-card { transition: border-color 0.15s; }
    .article-nav-card:hover { border-color: #2563eb; }
    .article-nav-card:hover .article-nav-title { color: #2563eb; }
    .article-nav-next { text-align: right; }
    .article-nav-next .article-nav-eyebrow { justify-content: flex-end; }
</style>

<div id="reading-progress" aria-hidden="true"></div>

<main class="flex-grow">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            // Fetch fields
            $badge = v5_digital_get_post_badge(get_the_ID(), 'Guide');
            
            $read_time = v5_digital_get_field('read_time');
            if (!$read_time) $read_time = '5 min de lecture';
            
            $author = get_the_author() ?: (v5_digital_get_field('author_name') ?: 'Rédaction');

            $blog_page = get_page_by_path('blog');
            $blog_url  = $blog_page ? get_permalink($blog_page->ID) : home_url('/blog/');
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

                    <!-- Auto-generated table of contents (filled by JS, hidden if no headings) -->
                    <nav id="article-toc" class="article-toc" aria-label="<?php echo esc_attr(v5_t('Sommaire')); ?>" hidden>
                        <p class="toc-title"><i data-lucide="list" class="w-3.5 h-3.5"></i> <?php echo esc_html(v5_t('Dans cet article')); ?></p>
                        <ul></ul>
                    </nav>

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

                <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8" style="padding-bottom:2.5rem;">
                <?php
                // ── In-content CTA into the agency directory ──────────────────
                $annuaire_url = home_url('/annuaire/');
                ?>
                <div class="article-cta">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:justify-between">
                        <div>
                            <p class="font-display font-extrabold text-slate-900 text-[16px] mb-1"><?php echo esc_html(v5_t('Vous cherchez une agence ?')); ?></p>
                            <p class="text-[13px] text-slate-600 leading-relaxed max-w-md"><?php echo esc_html(v5_t('Comparez les agences marocaines par service, ville et note dans notre annuaire vérifié.')); ?></p>
                        </div>
                        <a href="<?php echo esc_url($annuaire_url); ?>"
                           class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-2.5 rounded-lg text-[13px] transition-colors font-mono flex items-center gap-1.5 no-underline whitespace-nowrap self-start">
                            <span><?php echo esc_html(v5_t('Explorer l\'annuaire')); ?></span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>

                <?php // ── End-of-article inline newsletter ─────────────────────── ?>
                <div class="article-nl">
                    <p class="font-mono text-[11px] uppercase tracking-wider text-brand-300 mb-2" style="color:#93c5fd;"><?php echo esc_html(v5_t('Newsletter · Gratuite')); ?></p>
                    <p class="font-display font-extrabold text-[18px] mb-1"><?php echo esc_html(v5_t('Un signal SEO par quinzaine.')); ?></p>
                    <p class="text-[13px] mb-4" style="color:#cbd5e1;"><?php echo esc_html(v5_t('Recevez nos audits d\'agences et analyses directement par email.')); ?></p>
                    <form class="blg-nl-form" data-source="article-inline" onsubmit="v5SubmitNewsletter(event)" style="max-width:420px;">
                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <input type="email" required placeholder="votre@email.com" class="blg-nl-email"
                                   style="flex:1;border:1.5px solid rgba(255,255,255,0.18);border-radius:10px;padding:10px 14px;font-size:14px;color:#fff;background:rgba(255,255,255,0.06);outline:none;min-width:180px;">
                            <button type="submit" class="blg-nl-submit"
                                    style="display:inline-flex;align-items:center;gap:6px;min-height:44px;border-radius:10px;background:#2563eb;color:#fff;font-weight:700;font-size:13px;padding:0 18px;border:none;cursor:pointer;white-space:nowrap;">
                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                <span><?php echo esc_html(v5_t('S\'abonner')); ?></span>
                            </button>
                        </div>
                        <input type="text" name="website" class="blg-nl-hp" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
                        <p class="blg-nl-msg" role="status" aria-live="polite" style="font-size:12px;font-weight:600;margin:8px 0 0;min-height:1em;color:#cbd5e1;"></p>
                    </form>
                </div>

                <?php
                // ── Previous / next article navigation ────────────────────────
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                if ($prev_post || $next_post) : ?>
                    <nav class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8" aria-label="<?php echo esc_attr(v5_t('Navigation des articles')); ?>">
                        <?php if ($prev_post) : ?>
                            <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="article-nav-card block border border-slate-200 rounded-xl p-4 bg-white no-underline">
                                <span class="article-nav-eyebrow text-[11px] font-mono text-slate-400 flex items-center gap-1 mb-1"><i data-lucide="arrow-left" class="w-3 h-3"></i> <?php echo esc_html(v5_t('Article précédent')); ?></span>
                                <span class="article-nav-title block text-[14px] font-semibold text-slate-900 leading-snug line-clamp-2" style="transition:color 0.15s;"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                            </a>
                        <?php else : ?><span></span><?php endif; ?>
                        <?php if ($next_post) : ?>
                            <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="article-nav-card article-nav-next block border border-slate-200 rounded-xl p-4 bg-white no-underline">
                                <span class="article-nav-eyebrow text-[11px] font-mono text-slate-400 flex items-center gap-1 mb-1"><?php echo esc_html(v5_t('Article suivant')); ?> <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                                <span class="article-nav-title block text-[14px] font-semibold text-slate-900 leading-snug line-clamp-2" style="transition:color 0.15s;"><?php echo esc_html(get_the_title($next_post)); ?></span>
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>

                <?php
                // ── Related articles ("À lire ensuite") ───────────────────────
                $current_id = get_the_ID();
                $cat_terms  = get_the_category($current_id);
                $cat_ids    = array();
                foreach ($cat_terms as $ct) { $cat_ids[] = $ct->term_id; }

                $related_args = array(
                    'post_type'           => 'post',
                    'posts_per_page'      => 3,
                    'post__not_in'        => array($current_id),
                    'ignore_sticky_posts' => true,
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                );
                if (!empty($cat_ids)) {
                    $related_args['category__in'] = $cat_ids;
                }
                $related = new WP_Query($related_args);
                if (!$related->have_posts()) {
                    // Fall back to most recent posts if the category yields nothing.
                    $related = new WP_Query(array(
                        'post_type'      => 'post',
                        'posts_per_page' => 3,
                        'post__not_in'   => array($current_id),
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ));
                }
                if ($related->have_posts()) : ?>
                    <section class="mt-10 pt-8 border-t border-slate-200" aria-label="<?php echo esc_attr(v5_t('À lire ensuite')); ?>">
                        <h2 class="font-display font-extrabold text-[16px] text-slate-900 uppercase tracking-wide mb-5"><?php echo esc_html(v5_t('À lire ensuite')); ?></h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <?php while ($related->have_posts()) : $related->the_post();
                                $r_id    = get_the_ID();
                                $r_badge = v5_digital_get_post_badge($r_id, 'Guide');
                                $r_read  = v5_digital_get_field('read_time') ?: '5 min';
                                $r_cover = v5_digital_get_field('cover_image_media');
                                if (!$r_cover) $r_cover = v5_digital_get_field('cover_image_url');
                                if (!$r_cover && has_post_thumbnail()) $r_cover = get_the_post_thumbnail_url(null, 'medium');
                                ?>
                                <a href="<?php the_permalink(); ?>" class="related-card">
                                    <?php if ($r_cover) : ?>
                                        <div style="height:120px;overflow:hidden;background:#f1f5f9;">
                                            <img src="<?php echo esc_url($r_cover); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                                        </div>
                                    <?php endif; ?>
                                    <div style="padding:14px;">
                                        <span style="font-size:10px;font-weight:700;font-family:monospace;text-transform:uppercase;letter-spacing:0.05em;color:#2563eb;"><?php echo esc_html($r_badge); ?></span>
                                        <h3 style="font-weight:700;font-size:13.5px;color:#0f172a;line-height:1.35;margin:6px 0 0;font-family:'Space Grotesk',system-ui,sans-serif;" class="line-clamp-3"><?php the_title(); ?></h3>
                                        <span style="font-size:11px;color:#94a3b8;font-family:monospace;display:flex;align-items:center;gap:4px;margin-top:8px;"><i data-lucide="clock" class="w-3 h-3"></i><?php echo esc_html($r_read); ?></span>
                                    </div>
                                </a>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </section>
                <?php endif; ?>
                </div><!-- /.article-extras (max-w-3xl) -->

            </div>
            <?php
        endwhile;
    endif;
    ?>
</main>

<?php
get_footer();
