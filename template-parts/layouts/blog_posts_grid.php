<?php
/**
 * Layout: Blog Posts Grid Section
 * Flexible content block that renders the full blog listing experience:
 * hero, topic filters, article card grid, and optional newsletter band.
 *
 * Sub-fields (from page_layouts flexible content):
 *   grid_title        — hero heading (HTML allowed)
 *   grid_subtitle     — hero lede text
 *   show_filters      — boolean: show category pills
 *   display_categories — optional WP categories to include
 *   posts_per_page    — how many posts to fetch (-1 = all)
 *   show_recent_rail  — boolean: show the "Articles récents" side rail
 *   rail_title        — rail heading
 *   rail_count        — how many posts the rail lists
 */

// ── Pull sub-fields ───────────────────────────────────────────────────────────
$grid_title       = v5_digital_get_sub_field('grid_title');
$grid_subtitle    = v5_digital_get_sub_field('grid_subtitle');
$show_filters     = v5_digital_get_sub_field('show_filters');
$display_categories = v5_digital_get_sub_field('display_categories');
$posts_per_page   = v5_digital_get_sub_field('posts_per_page');
$show_recent_rail = v5_digital_get_sub_field('show_recent_rail');
$rail_title       = v5_digital_get_sub_field('rail_title');
$rail_count       = v5_digital_get_sub_field('rail_count');

// Sensible defaults
if (!$grid_title)     $grid_title     = 'Intelligence <span class="quiet">Agences.</span>';
if (!$grid_subtitle)  $grid_subtitle  = 'Notes techniques sur les agences marocaines, la visibilité organique, Core Web Vitals, et les preuves qui séparent les vrais opérateurs SEO des argumentaires commerciaux polis.';
if ($show_filters    === null || $show_filters    === '') $show_filters    = true;
if (!$posts_per_page || $posts_per_page == 0) $posts_per_page = -1;
if ($show_recent_rail === null || $show_recent_rail === '') $show_recent_rail = true;
if (!$rail_title)  $rail_title = v5_t('Articles récents');
if (!$rail_count || (int) $rail_count < 1) $rail_count = 5;

$normalize_blog_category_ids = function ($categories) {
    if (empty($categories)) {
        return array();
    }

    if (!is_array($categories)) {
        $categories = array($categories);
    }

    $category_ids = array();
    foreach ($categories as $category) {
        if (is_object($category) && isset($category->term_id)) {
            $term_id = (int) $category->term_id;
        } elseif (is_array($category) && isset($category['term_id'])) {
            $term_id = (int) $category['term_id'];
        } else {
            $term_id = (int) $category;
        }

        if ($term_id > 0) {
            $category_ids[$term_id] = $term_id;
        }
    }

    return array_values($category_ids);
};

$display_category_ids = $normalize_blog_category_ids($display_categories);

$blog_page = get_page_by_path('blog');
$layouts = $blog_page ? v5_digital_get_field('page_layouts', $blog_page->ID) : v5_digital_get_field('page_layouts');
$has_common_hero = false;
if (is_array($layouts)) {
    foreach ($layouts as $l) {
        if (isset($l['acf_fc_layout']) && $l['acf_fc_layout'] === 'common_hero_section') {
            $has_common_hero = true;
            break;
        }
    }
}

// ── Query blog posts ──────────────────────────────────────────────────────────
$blog_query_args = array(
    'post_type'      => 'post',
    'posts_per_page' => (int) $posts_per_page,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
);
if (!empty($display_category_ids)) {
    $blog_query_args['category__in'] = $display_category_ids;
}
$blog_query = new WP_Query($blog_query_args);
$post_count = $blog_query->found_posts;

$is_default_blog_category = function ($category) {
    if (!$category || is_wp_error($category)) {
        return true;
    }

    $default_category_id = (int) get_option('default_category');

    return ($default_category_id && (int) $category->term_id === $default_category_id)
        || $category->slug === 'uncategorized'
        || strtolower($category->name) === 'uncategorized'
        || strtolower($category->name) === 'non classé';
};

$get_post_blog_categories = function ($post_id) use ($is_default_blog_category) {
    $categories = get_the_category($post_id);
    $valid_categories = array();

    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            if (!$is_default_blog_category($category)) {
                $valid_categories[$category->term_id] = $category;
            }
        }
    }

    if (!empty($valid_categories)) {
        return array_values($valid_categories);
    }

    $badge = v5_digital_get_post_badge($post_id, '');
    if (!empty($badge)) {
        $fallback = new stdClass();
        $fallback->term_id = 'badge-' . sanitize_title($badge);
        $fallback->name = $badge;
        $fallback->slug = sanitize_title($badge);
        return array($fallback);
    }

    return array();
};

$blog_categories = array();
if (!empty($display_category_ids)) {
    $selected_terms = get_terms(array(
        'taxonomy'   => 'category',
        'include'    => $display_category_ids,
        'hide_empty' => false,
        'orderby'    => 'include',
    ));
    if (!is_wp_error($selected_terms) && !empty($selected_terms)) {
        foreach ($selected_terms as $category) {
            if (!$is_default_blog_category($category)) {
                $blog_categories[$category->slug] = $category;
            }
        }
    }
} else {
    foreach ($blog_query->posts as $blog_post) {
        foreach ($get_post_blog_categories($blog_post->ID) as $category) {
            $blog_categories[$category->slug] = $category;
        }
    }
}
uasort($blog_categories, function ($a, $b) {
    return strcasecmp($a->name, $b->name);
});
?>

<style>
    /* ── Blog Grid Layout Block ── */
    :root {
        --blg-ink:  #101418;
        --blg-muted:#66717f;
        --blg-line: rgba(16, 20, 24, 0.12);
        --blg-blue: #2463eb;
        --blg-soft: #f6f8fb;
    }

    .blog-grid-block { background: #ffffff; }
    .blog-grid-wrap  { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }

    /* Hero */
    .blg-hero {
        min-height: auto;
        display: flex;
        align-items: center;
        padding: clamp(40px, 5vw, 60px) 0 clamp(24px, 3vw, 40px);
    }
    .blg-hero > div {
        text-align: center;
        max-width: 780px;
        margin: 0 auto;
        width: 100%;
    }
    .blg-crumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #7a8594;
        font-size: 12px;
        margin-bottom: 16px;
        justify-content: center;
    }
    .blg-crumbs a { color: inherit; transition: color 0.15s ease; text-decoration: none; }
    .blg-crumbs a:hover { color: var(--blg-ink); }

    .blg-hero-title {
        max-width: 860px;
        margin: 0 auto;
        font-size: clamp(32px, 4vw, 52px);
        line-height: 1.1;
        font-weight: 800;
        color: var(--blg-ink);
    }
    .blg-hero-title .quiet {
        color: var(--blg-blue);
        display: inline-block;
        position: relative;
        transform-origin: center bottom;
    }
    .blg-hero-title .quiet::after {
        content: '';
        position: absolute;
        left: 0.02em; right: 0.02em; bottom: 0.02em;
        height: 0.11em;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.16);
        transform: scaleX(1);
        transform-origin: left center;
        z-index: -1;
    }
    .blg-hero-lede {
        max-width: 560px;
        margin: 18px auto 0;
        color: #5d6876;
        font-size: clamp(16px, 1.5vw, 19px);
        line-height: 1.72;
    }

    /* Filters */
    .blg-filters-container {
        border-top: 1px solid var(--blg-line);
        background: white;
        position: sticky;
        top: 56px;
        z-index: 40;
        transition: top 0.2s ease;
    }
    .admin-bar .blg-filters-container {
        top: 88px;
    }
    @media (max-width: 782px) {
        .admin-bar .blg-filters-container {
            top: 102px;
        }
    }
    .blg-topic-pill {
        font-size: 11.5px; font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        padding: 5px 14px; border-radius: 999px;
        border: 1.5px solid #e2e8f0;
        background: white; color: #475569;
        cursor: pointer; white-space: nowrap;
        transition: all 0.15s ease;
    }
    .blg-topic-pill:hover { border-color: #2463eb; color: #2463eb; background: #eff6ff; }
    .blg-topic-pill.active { background: #101418; color: white; border-color: #101418; }

    /* Article Grid */
    .blg-list-section {
        border-top: 1px solid var(--blg-line);
        padding: clamp(32px, 4vw, 52px) 0;
        background: #ffffff;
    }

    /* Main column + "Articles récents" rail (rail stacks below on mobile) */
    .blg-listing-rail { margin-top: 40px; }
    @media (min-width: 1024px) {
        .blg-listing-cols {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 40px;
            align-items: start;
        }
        /* 56px sticky header + ~66px sticky filter bar */
        .blg-listing-rail { margin-top: 0; position: sticky; top: 122px; }
    }
    .blg-article-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(16,20,24,0.06), 0 0 0 1px rgba(16,20,24,0.07);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: box-shadow 0.2s ease, transform 0.18s ease;
    }
    .blg-article-card:hover {
        box-shadow: 0 8px 28px rgba(16,20,24,0.13), 0 0 0 1px rgba(16,20,24,0.09);
        transform: translateY(-3px);
    }
    .blg-card-img { overflow: hidden; flex-shrink: 0; }
    .blg-card-img img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform 0.35s ease;
    }
    .blg-article-card:hover .blg-card-img img { transform: scale(1.05); }

    /* Badge colours */
    .blg-badge-ranking    { background: #dbeafe; color: #1e40af; }
    .blg-badge-guide      { background: #ede9fe; color: #5b21b6; }
    .blg-badge-seo        { background: #fef3c7; color: #92400e; }
    .blg-badge-comparison { background: #d1fae5; color: #065f46; }
    .blg-badge-default    { background: #f1f5f9; color: #475569; }

    .blg-read-more {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 12.5px; font-weight: 600; color: #2563eb;
        transition: gap 0.15s ease;
    }
    .blg-article-card:hover .blg-read-more { gap: 7px; }



    @media (max-width: 640px) {
        .blog-grid-wrap { width: min(100% - 28px, 1180px); }
        .blg-hero  { min-height: auto; padding-top: 42px; }
    }
</style>

<div class="blog-grid-block">

    <?php /* ── HERO ── */ ?>
    <?php if (!$has_common_hero) : ?>
        <section class="blog-grid-wrap blg-hero" aria-labelledby="blg-title-el">
            <div>
                <div class="blg-crumbs">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span style="color:#101418;font-weight:600;">Blog</span>
                </div>
                <h1 class="blg-hero-title" id="blg-title-el">
                    <?php echo wp_kses($grid_title, array('span' => array('class' => array()))); ?>
                </h1>
                <?php if ($grid_subtitle) : ?>
                <p class="blg-hero-lede"><?php echo esc_html($grid_subtitle); ?></p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php /* ── TOPIC FILTERS ── */ ?>
    <?php if ($show_filters) : ?>
    <div class="blg-filters-container"<?php if ($has_common_hero) echo ' style="border-top:none;"'; ?>>
        <div class="blog-grid-wrap">
            <div style="display:flex;align-items:center;gap:8px;padding:12px 0;overflow-x:auto;" id="blg-topic-pills">
                <button class="blg-topic-pill active flex-shrink-0" onclick="blgFilterTopic(this,'all')">Tout voir</button>
                <?php foreach ($blog_categories as $category) : ?>
                    <button class="blg-topic-pill flex-shrink-0" onclick="blgFilterTopic(this,'<?php echo esc_js($category->slug); ?>')"><?php echo esc_html($category->name); ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php /* ── ARTICLE GRID ── */ ?>
    <section class="blg-list-section">
        <div class="blog-grid-wrap">
            <div<?php if ($show_recent_rail) echo ' class="blg-listing-cols"'; ?>>
            <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <p style="font-size:12.5px;color:#94a3b8;font-family:monospace;">
                    <span id="blg-showing-count"><?php echo $post_count; ?></span> articles
                </p>
                <a href="<?php echo esc_url(home_url('/contact/?subject=pitch')); ?>"
                   style="font-size:12px;font-weight:600;color:#2463eb;display:inline-flex;align-items:center;gap:4px;font-family:monospace;text-decoration:none;">
                    <i data-lucide="pen-line" class="w-3.5 h-3.5"></i>
                    <span>Proposer un article</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 <?php echo $show_recent_rail ? '' : 'lg:grid-cols-3'; ?> gap-6" id="blg-grid-container">
                <?php
                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post();

                        $badge      = v5_digital_get_post_badge(get_the_ID(), 'Guide');
                        $read_time  = v5_digital_get_field('read_time') ?: '5 min de lecture';
                        $author     = get_the_author() ?: v5_digital_get_field('author_name');

                        // Cover image priority: featured image -> legacy ACF media/URL fields. Empty means no image.
                        $cover_image = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'large') : '';
                        if (!$cover_image) $cover_image = v5_digital_get_field('cover_image_media');
                        if (!$cover_image) $cover_image = v5_digital_get_field('cover_image_url');

                        // Badge CSS class
                        $badge_lower = strtolower($badge);
                        $badge_cls   = 'blg-badge-default';
                        if (strpos($badge_lower, 'classement') !== false || strpos($badge_lower, 'ranking') !== false) {
                            $badge_cls = 'blg-badge-ranking';
                        } elseif (strpos($badge_lower, 'guide') !== false) {
                            $badge_cls = 'blg-badge-guide';
                        } elseif (strpos($badge_lower, 'audit') !== false || strpos($badge_lower, 'seo') !== false) {
                            $badge_cls = 'blg-badge-seo';
                        } elseif (strpos($badge_lower, 'comparatif') !== false || strpos($badge_lower, 'comparison') !== false) {
                            $badge_cls = 'blg-badge-comparison';
                        }

                        $post_categories = $get_post_blog_categories(get_the_ID());
                        $category_slugs = array();
                        foreach ($post_categories as $category) {
                            $category_slugs[] = $category->slug;
                        }

                        $excerpt = get_the_excerpt();
                        if (strlen($excerpt) > 115) $excerpt = substr($excerpt, 0, 115) . '…';
                        ?>
                        <article class="blg-article-card blg-post-item"
                                 data-badge="<?php echo esc_attr($badge); ?>"
                                 data-categories="<?php echo esc_attr(implode(' ', $category_slugs)); ?>"
                                 onclick="window.location.href='<?php the_permalink(); ?>'">
                            <?php if ($cover_image) : ?>
                                <div class="blg-card-img" style="height:210px;">
                                    <img src="<?php echo esc_url($cover_image); ?>"
                                         alt="<?php the_title_attribute(); ?>"
                                         loading="lazy"
                                         style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            <?php endif; ?>
                            <div style="display:flex;flex-direction:column;flex:1;padding:20px;">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                    <span style="font-size:11px;font-weight:700;font-family:monospace;text-transform:uppercase;letter-spacing:0.05em;padding:3px 10px;border-radius:4px;"
                                          class="<?php echo $badge_cls; ?>"><?php echo esc_html($badge); ?></span>
                                    <span style="font-size:11px;color:#94a3b8;font-family:monospace;display:flex;align-items:center;gap:4px;">
                                        <i data-lucide="clock" class="w-3 h-3"></i><?php echo esc_html($read_time); ?>
                                    </span>
                                </div>
                                <h3 style="font-weight:700;font-size:15.5px;color:#0f172a;line-height:1.35;margin-bottom:8px;font-family:'Space Grotesk',system-ui,sans-serif;">
                                    <?php the_title(); ?>
                                </h3>
                                <p style="font-size:13px;color:#64748b;line-height:1.65;flex:1;margin-bottom:16px;">
                                    <?php echo esc_html($excerpt); ?>
                                </p>
                                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f1f5f9;margin-top:auto;">
                                    <span style="font-size:11.5px;color:#94a3b8;font-family:monospace;display:flex;align-items:center;gap:4px;">
                                        <i data-lucide="user" class="w-3 h-3"></i>
                                        Par <?php echo esc_html($author); ?>
                                    </span>
                                    <span class="blg-read-more">
                                        Lire la suite
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </span>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <p style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:40px 0;font-family:monospace;font-size:13px;">
                        Aucun article trouvé.
                    </p>
                    <?php
                endif;
                ?>
            </div>

            <div id="blg-empty" style="display:none;padding:64px 0;text-align:center;">
                <i data-lucide="search-x" style="width:40px;height:40px;color:#cbd5e1;margin:0 auto 12px;display:block;"></i>
                <p style="font-size:13px;color:#94a3b8;font-family:monospace;">Aucun article dans cette catégorie.</p>
            </div>
            </div>

            <?php if ($show_recent_rail) : ?>
            <aside class="blg-listing-rail">
                <?php get_template_part('template-parts/components/recent-posts-rail', null, array(
                    'count'   => (int) $rail_count,
                    'exclude' => 0,
                    'title'   => $rail_title,
                )); ?>
            </aside>
            <?php endif; ?>
            </div>
        </div>
    </section>



</div>

<script>
(function () {
    'use strict';

    window.blgFilterTopic = function (btn, topic) {
        document.querySelectorAll('.blg-topic-pill').forEach(function (p) {
            p.classList.remove('active');
        });
        btn.classList.add('active');

        var cards        = document.querySelectorAll('.blg-post-item');
        var emptyEl      = document.getElementById('blg-empty');
        var countEl      = document.getElementById('blg-showing-count');
        var visibleCount = 0;

        cards.forEach(function (card) {
            var categories = (card.getAttribute('data-categories') || '').split(/\s+/);
            var match = (topic === 'all' || categories.indexOf(topic) !== -1);
            card.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        if (countEl) countEl.textContent = visibleCount;
        if (emptyEl) emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
    };


}());
</script>
