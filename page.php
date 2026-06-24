<?php
/**
 * Page Template for Flexible Content Builder
 */
get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        ?>
        <main class="flex-grow">
            <?php
            // Output normal WordPress content editor block if used
            if (get_the_content() && !have_rows('page_layouts')) {
                ?>
                <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 py-10">
                    <h1 class="font-display text-[2.25rem] font-bold text-slate-900 leading-tight mb-5 text-center"><?php the_title(); ?></h1>
                    <div class="entry-content text-[15px] text-slate-500 leading-relaxed mt-6">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php
            }

            // Output flexible layouts
            if (have_rows('page_layouts')) :
                while (have_rows('page_layouts')) : the_row();
                    $layout = get_row_layout();
                    $layout_clean = str_replace('_section', '', $layout);
                    get_template_part('template-parts/layouts/' . $layout_clean);
                endwhile;
            endif;
            ?>
        </main>
        <?php
    endwhile;
endif;

get_footer();
