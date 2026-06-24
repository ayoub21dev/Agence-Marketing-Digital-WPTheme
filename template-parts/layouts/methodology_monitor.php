<?php
/**
 * Layout: Methodology Monitor Section
 * Template part for rendering the step-by-step continuous monitoring strip.
 */

$section_label = v5_get_field_default('section_label', '03 / Suivi Continu');
$title         = v5_get_field_default('title', 'Nous contrôlons régulièrement les agences.');
$description   = v5_get_field_default('description', 'Une agence classée peut monter, descendre ou être retirée si de nouvelles analyses indiquent une modification de sa qualité.');
$steps         = v5_get_field_default('steps', null);

$has_label = !empty($section_label);
$has_title = !empty($title);
$has_desc  = !empty($description);
$has_steps = !empty($steps) && is_array($steps);

if ($has_label || $has_title || $has_desc || $steps === null || $has_steps) :

$total_steps = is_array($steps) ? count($steps) : 0;

// Dynamically construct grid template columns based on number of steps and separating arrows
$grid_cols_style = "";
if ($total_steps > 0) {
    $cols = array();
    for ($i = 0; $i < $total_steps; $i++) {
        if ($i > 0) $cols[] = "auto";
        $cols[] = "1fr";
    }
    $grid_cols_style = implode(" ", $cols);
}
?>

<style>
    .blg-monitor-section {
        --line: rgba(16, 20, 24, 0.12);
        --ink: #101418;
        --muted: #66717f;
        --blue: #2463eb;
        
        padding: clamp(32px, 4vw, 52px) 0;
        border-top: 1px solid var(--line);
        background: #ffffff;
    }
    .monitor-strip {
        display: grid;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
    }
    .monitor-step {
        background: white;
        border-radius: 12px;
        padding: 20px;
        min-height: 160px;
        box-shadow: 0 1px 3px rgba(16, 20, 24, 0.04), 0 0 0 1px rgba(16, 20, 24, 0.06);
    }
    .monitor-step i { color: var(--blue); width: 20px; height: 20px; margin-bottom: 18px; display: block; }
    .monitor-step h3 { font-size: 16px; font-weight: 800; margin-bottom: 6px; color: var(--ink); }
    .monitor-step p { color: var(--muted); font-size: 13px; line-height: 1.55; }
    .monitor-arrow { color: #aab3c0; width: 20px; height: 20px; justify-self: center; }

    @media (min-width: 981px) {
        .monitor-strip {
            grid-template-columns: <?php echo esc_attr($grid_cols_style); ?>;
        }
    }

    @media (max-width: 980px) {
        .monitor-strip { grid-template-columns: 1fr; }
        .monitor-arrow { transform: rotate(90deg); margin: 6px auto; }
    }
</style>

<section class="blg-monitor-section">
    <div class="method-wrap">
        <?php if ($has_label || $has_title || $has_desc) : ?>
        <div class="section-head">
            <div>
                <?php if ($has_label) : ?>
                    <div class="section-label"><?php echo esc_html($section_label); ?></div>
                <?php endif; ?>
                <?php if ($has_title) : ?>
                    <h2 class="section-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
            <?php if ($has_desc) : ?>
                <p class="section-copy"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (have_rows('steps')) : ?>
            <div class="monitor-strip">
                <?php while (have_rows('steps')) : the_row();
                    $step_icon  = get_sub_field('step_icon');
                    $step_title = get_sub_field('step_title');
                    $step_desc  = get_sub_field('step_description');
                    $step_index = get_row_index();
                    ?>
                    <?php if ($step_index > 1) : ?>
                        <i data-lucide="arrow-right" class="monitor-arrow"></i>
                    <?php endif; ?>
                    <article class="monitor-step">
                        <?php if ($step_icon) : ?>
                            <i data-lucide="<?php echo esc_attr($step_icon); ?>"></i>
                        <?php endif; ?>
                        <?php if ($step_title) : ?>
                            <h3><?php echo esc_html($step_title); ?></h3>
                        <?php endif; ?>
                        <?php if ($step_desc) : ?>
                            <p><?php echo esc_html($step_desc); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
