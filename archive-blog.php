<?php
/**
 * The template for displaying the CPT blog archive page.
 * Renders flexible layouts from the static 'Blog' page dynamically.
 */

get_header();

$blog_page = get_page_by_path('blog');
if ($blog_page) :
    ?>
    <main class="flex-grow">
        <?php
        if (have_rows('page_layouts', $blog_page->ID)) :
            while (have_rows('page_layouts', $blog_page->ID)) : the_row();
                $layout = get_row_layout();
                // Strip '_section' suffix to match physical files (e.g. blog_posts_grid_section -> blog_posts_grid.php)
                $layout_clean = str_replace('_section', '', $layout);
                get_template_part('template-parts/layouts/' . $layout_clean);
            endwhile;
        endif;
        ?>
    </main>
    <?php
else :
    // Fallback if the static Blog page doesn't exist yet
    ?>
    <main class="flex-grow py-20 text-center">
        <h1 class="text-2xl font-bold">Blog</h1>
        <p class="text-slate-500 mt-2">Veuillez créer une page avec le slug "blog" et configurer ses mises en page.</p>
    </main>
    <?php
endif;

get_footer();
