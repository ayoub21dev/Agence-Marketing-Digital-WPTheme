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
        margin-bottom: 1.5rem;
        font-size: 16px;
        line-height: 1.8;
        color: #475569;
    }
    .article-prose h2 {
        font-family: 'Space Grotesk', system-ui, sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 2.25rem;
        margin-bottom: 0.85rem;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }
    .article-prose h3 {
        font-family: 'Space Grotesk', system-ui, sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.75rem;
        margin-bottom: 0.6rem;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }
    .article-prose strong { font-weight: 600; color: #0f172a; }

    /* Image hero: the cover image is the hero backdrop; text sits on a slate
       gradient scrim so the white title stays readable on any photo. */
    .article-hero-media {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .article-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top,
            rgba(2, 6, 23, 0.92) 0%,
            rgba(2, 6, 23, 0.55) 45%,
            rgba(2, 6, 23, 0.28) 100%);
    }
    .article-hero-tall { min-height: 420px; }
    @media (min-width: 768px) {
        .article-hero-tall { min-height: 520px; }
    }
    .article-hero-title { text-shadow: 0 2px 24px rgba(2, 6, 23, 0.45); }
    .article-hero-dim { color: rgba(255, 255, 255, 0.78); }
    .article-hero-dim a:hover { color: #fff; }
    /* Frosted chips (badge + back button) on top of the image */
    .article-hero-chip {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.25);
        color: #fff;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }
    a.article-hero-chip:hover { background: rgba(255, 255, 255, 0.22); }
</style>

<main class="flex-grow">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            // Fetch fields
            $badge = v5_digital_get_post_badge(get_the_ID(), 'Guide');
            
            $read_time = v5_digital_get_field('read_time');
            if (!$read_time) $read_time = v5_t('5 min de lecture');
            
            $author = get_the_author() ?: (v5_digital_get_field('author_name') ?: 'Rédaction');

            $blog_page = get_page_by_path('blog');
            $blog_url  = $blog_page ? get_permalink($blog_page->ID) : home_url('/blog/');

            // Cover image priority: featured image -> legacy ACF media/URL fields. Empty means no image.
            $cover_image = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'full') : '';
            if (!$cover_image) $cover_image = v5_digital_get_field('cover_image_media');
            if (!$cover_image) $cover_image = v5_digital_get_field('cover_image_url');
            ?>
            <!-- ==================== ARTICLE DETAIL PAGE ==================== -->
            <?php $on_image = (bool) $cover_image; // cover image becomes a contained hero card ?>
            <div class="page" id="page-article">
                <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 pt-6 md:pt-8">

                    <!-- Breadcrumbs & Back -->
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-1.5 text-[12px] text-slate-500 font-mono">
                            <a class="cursor-pointer hover:text-slate-900" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(v5_t('Accueil')); ?></a>
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            <a class="cursor-pointer hover:text-slate-900" href="<?php echo esc_url($blog_url); ?>"><?php echo esc_html(v5_t('Blog')); ?></a>
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            <span class="text-slate-900 font-semibold truncate max-w-[150px] sm:max-w-xs"><?php the_title(); ?></span>
                        </div>
                        <a id="v5-back-to-articles" href="<?php echo esc_url($blog_url); ?>" class="bg-white hover:bg-slate-100 border border-slate-200 rounded-lg px-2.5 py-1 text-[11px] text-slate-600 flex items-center gap-1 transition-all no-underline">
                            <i data-lucide="arrow-left" class="w-3 h-3"></i> <span><?php echo esc_html(v5_t('retour aux articles')); ?></span>
                        </a>
                    </div>

                    <?php if ($on_image) : ?>
                    <!-- Hero card: cover image as backdrop, title pinned to its bottom -->
                    <div class="relative overflow-hidden rounded-2xl border border-slate-200 shadow-lg bg-slate-900 flex flex-col article-hero-tall">
                        <img src="<?php echo esc_url($cover_image); ?>" alt="" aria-hidden="true" class="article-hero-media">
                        <div class="article-hero-overlay" aria-hidden="true"></div>

                        <div class="relative z-10 mt-auto p-6 md:p-10">
                            <span class="inline-block border text-[10px] font-mono font-bold px-2.5 py-1 rounded mb-3 uppercase tracking-wider article-hero-chip"><?php echo esc_html($badge); ?></span>

                            <h1 class="text-[2rem] md:text-[2.75rem] font-extrabold tracking-tight leading-[1.15] mb-5 font-display text-white article-hero-title"><?php the_title(); ?></h1>

                            <div class="flex flex-wrap items-center gap-3 text-[12px] font-mono article-hero-dim">
                                <span class="flex items-center gap-1"><i data-lucide="user" class="w-3.5 h-3.5"></i> <span><?php echo esc_html(v5_t('Par')); ?> <?php echo esc_html($author); ?></span></span>
                                <span>&middot;</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> <span><?php echo get_the_date('d M Y'); ?></span></span>
                                <span>&middot;</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> <span><?php echo esc_html($read_time); ?></span></span>
                            </div>
                        </div>
                    </div>
                    <?php else : ?>
                    <!-- No cover image: plain heading block -->
                    <div class="pt-2 pb-2 border-b border-slate-200">
                        <span class="inline-block bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-mono font-bold px-2.5 py-1 rounded mb-3 uppercase tracking-wider"><?php echo esc_html($badge); ?></span>

                        <h1 class="text-[2rem] md:text-[2.75rem] font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-5 font-display"><?php the_title(); ?></h1>

                        <div class="flex flex-wrap items-center gap-3 text-[12px] text-slate-500 font-mono mb-6">
                            <span class="flex items-center gap-1"><i data-lucide="user" class="w-3.5 h-3.5"></i> <span><?php echo esc_html(v5_t('Par')); ?> <?php echo esc_html($author); ?></span></span>
                            <span>&middot;</span>
                            <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> <span><?php echo get_the_date('d M Y'); ?></span></span>
                            <span>&middot;</span>
                            <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> <span><?php echo esc_html($read_time); ?></span></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 pt-10 pb-12 lg:grid lg:grid-cols-12 lg:gap-12">

                    <div class="lg:col-span-8 min-w-0">
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
                            // ItemList JSON-LD mirroring the ranked agency list below — this is
                            // the schema Google reads for "Top X" ranking articles.
                            //
                            // Only the JSON-LD is behind v5_digital_schema_enabled() (see
                            // functions.php); the visible "Analyses Éditoriales" list further
                            // down always renders. Keep the gate inside this block, never on
                            // the `if (!empty($agency_reviews))` above it.
                            if (v5_digital_schema_enabled()) {
                                $schema_items = array();
                                foreach ($agency_reviews as $schema_rev) {
                                    if (empty($schema_rev['agency'])) continue;
                                    $schema_agency = get_post($schema_rev['agency']);
                                    if (!$schema_agency) continue;

                                    $item = array(
                                        '@type'    => 'ListItem',
                                        // Sort key only — replaced below by the item's real
                                        // position in the list. See the renumbering note.
                                        'position' => intval($schema_rev['rank']) ?: count($schema_items) + 1,
                                        'name'     => $schema_agency->post_title,
                                    );
                                    $schema_site = get_post_meta($schema_agency->ID, 'website', true);
                                    if ($schema_site) {
                                        $item['url'] = strpos($schema_site, 'http') === 0 ? $schema_site : 'https://' . $schema_site;
                                    }
                                    $schema_items[] = $item;
                                }
                                if (!empty($schema_items)) {
                                    usort($schema_items, function ($a, $b) {
                                        return $a['position'] - $b['position'];
                                    });

                                    // `ListItem.position` is the item's place IN THIS LIST — it
                                    // must be 1-based and consecutive. It is NOT the agency's
                                    // RANK badge: an article ranking agencies #4 and #6 would
                                    // otherwise emit positions [4, 6] with numberOfItems = 2,
                                    // which schema.org and Google reject. The rank is still what
                                    // orders the list (usort above); it just isn't the position.
                                    $schema_position = 0;
                                    foreach ($schema_items as &$schema_item) {
                                        $schema_item['position'] = ++$schema_position;
                                    }
                                    unset($schema_item); // break the reference from foreach-by-ref

                                    $item_list = array(
                                        '@context'        => 'https://schema.org',
                                        '@type'           => 'ItemList',
                                        'name'            => get_the_title(),
                                        'url'             => get_permalink(),
                                        'numberOfItems'   => count($schema_items),
                                        'itemListElement' => $schema_items,
                                    );
                                    echo '<script type="application/ld+json">'
                                        . wp_json_encode($item_list, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                                        . '</script>' . "\n";
                                }
                            }
                                        ?>
                                        <div class="mt-10 pt-8 border-t border-slate-200">
                                            <h3 class="font-extrabold text-[16px] text-slate-900 uppercase font-display tracking-wide mb-6"><?php echo esc_html(v5_t('Analyses Éditoriales')); ?></h3>
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
                                                        $link_text = v5_t('Visiter le Site');
                                                    }
                                                    ?>
                                                    <div class="border border-slate-200 rounded-xl p-5 md:p-6 bg-slate-50/30 hover:bg-slate-50/50 transition-colors shadow-sm relative">
                                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3 pb-3 border-b border-slate-100">
                                                            <div class="flex items-center gap-3">
                                                                <?php if ($logo_src) : ?>
                                                                    <img src="<?php echo esc_url($logo_src); ?>"
                                                                         alt="<?php echo esc_attr(sprintf(v5_t('Logo de %s'), $agency_post->post_title)); ?>"
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

                    <!-- Right rail: latest articles (stacks below the article on mobile) -->
                    <aside class="lg:col-span-4 mt-10 lg:mt-0">
                        <div class="lg:sticky lg:top-24">
                            <?php get_template_part('template-parts/components/recent-posts-rail', null, array(
                                'count'   => 4,
                                'exclude' => get_the_ID(),
                            )); ?>
                        </div>
                    </aside>

                </div>
            </div>
            <?php
        endwhile;
    endif;
    ?>
</main>

<?php
get_footer();
