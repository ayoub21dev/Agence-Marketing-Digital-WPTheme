<?php
/**
 * Static Front Page Template
 */
get_header();

// Setup post loop context for the page
if (have_posts()) :
    while (have_posts()) : the_post();
        ?>
        <main class="flex-grow">
            <?php
            if (have_rows('page_layouts')) :
                while (have_rows('page_layouts')) : the_row();
                    $layout = get_row_layout();
                    // Strip '_section' suffix to match physical files (e.g. hero_section -> hero.php)
                    $layout_clean = str_replace('_section', '', $layout);
                    get_template_part('template-parts/layouts/' . $layout_clean);
                endwhile;
            else :
                // Fallback message if no layout rows are populated
                ?>
                <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 py-20 text-center" style="margin-block: 100px;">
                    <h2 class="font-display text-[1.75rem] font-bold text-slate-900 leading-tight mb-3">Bienvenue sur Agence Marketing Digital</h2>
                    <p class="text-slate-500 max-w-md mx-auto mt-2">Veuillez modifier cette page dans le tableau de bord d'administration pour ajouter des mises en page flexibles ou activer le thème pour déclencher l'initialisation automatique.</p>
                </div>
                <?php
            endif;
            ?>
        </main>
        <?php
    endwhile;
endif;

get_footer();
