<?php
/**
 * Component: Recent posts rail ("Articles récents")
 *
 * Compact vertical list of the latest blog posts, sized for a ~320px side
 * rail. Shared by single-blog.php (article sidebar) and the blog listing
 * layout (blog_posts_grid.php). Renders nothing when no posts match, so
 * callers never need a guard.
 *
 * Args (via get_template_part(..., null, $args) — WP 5.5+):
 *   count   int    how many posts to list (default 4)
 *   exclude int    a post ID to leave out, e.g. the article being read (default 0)
 *   title   string rail heading (default: translated "Articles récents")
 */

$args = wp_parse_args(is_array($args ?? null) ? $args : array(), array(
    'count'   => 4,
    'exclude' => 0,
    'title'   => v5_t('Articles récents'),
));

$rail_query = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => max(1, (int) $args['count']),
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
    'post__not_in'        => $args['exclude'] ? array((int) $args['exclude']) : array(),
));

if (!$rail_query->have_posts()) {
    return;
}

$rail_blog_page = get_page_by_path('blog');
$rail_blog_url  = $rail_blog_page ? get_permalink($rail_blog_page->ID) : home_url('/blog/');
?>
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5" aria-label="<?php echo esc_attr($args['title']); ?>">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <h2 class="font-display font-bold text-[14px] text-slate-900 uppercase tracking-wide"><?php echo esc_html($args['title']); ?></h2>
        <i data-lucide="newspaper" class="w-4 h-4 text-slate-400"></i>
    </div>

    <div class="space-y-4">
        <?php while ($rail_query->have_posts()) : $rail_query->the_post();
            $rail_badge = v5_digital_get_post_badge(get_the_ID(), 'Guide');

            // Cover image priority: featured image -> legacy ACF media/URL fields.
            $rail_image = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'medium') : '';
            if (!$rail_image) $rail_image = v5_digital_get_field('cover_image_media');
            if (!$rail_image) $rail_image = v5_digital_get_field('cover_image_url');
            ?>
            <a href="<?php the_permalink(); ?>" class="group flex gap-3 no-underline">
                <div class="w-20 h-20 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                    <?php if ($rail_image) : ?>
                        <img src="<?php echo esc_url($rail_image); ?>"
                             alt="<?php the_title_attribute(); ?>"
                             loading="lazy"
                             class="w-full h-full object-cover">
                    <?php else : ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i data-lucide="file-text" class="w-5 h-5 text-slate-300"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-[10px] font-mono font-bold text-brand-600 uppercase tracking-wider mb-0.5"><?php echo esc_html($rail_badge); ?></span>
                    <h3 class="font-display font-bold text-[13px] text-slate-900 leading-snug line-clamp-2 group-hover:text-brand-600 transition-colors"><?php the_title(); ?></h3>
                    <span class="block text-[11px] text-slate-500 font-mono mt-1"><?php echo get_the_date('d M Y'); ?></span>
                </div>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <a href="<?php echo esc_url($rail_blog_url); ?>" class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1 text-[12.5px] font-bold font-mono text-brand-600 hover:text-brand-700 transition-colors no-underline">
        <span><?php echo esc_html(v5_t('Tout le blog')); ?></span>
        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
    </a>
</div>
