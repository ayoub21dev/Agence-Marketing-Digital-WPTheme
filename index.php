<?php
/**
 * Main fallback index file
 */
get_header();
?>
<main class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 py-10">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article <?php post_class('prose max-w-none'); ?>>
            <h1 class="font-display text-[2.25rem] font-bold text-slate-900 leading-tight mb-5"><?php the_title(); ?></h1>
            <div class="entry-content text-[15px] text-slate-500 leading-relaxed">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; endif; ?>
</main>
<?php
get_footer();
