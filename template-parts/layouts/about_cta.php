<?php
/**
 * Layout: About CTA Section
 * Template part for rendering the bottom CTA section of the about page.
 */

$eyebrow        = v5_get_field_default('eyebrow', 'Processus & Inscription');
$title          = v5_get_field_default('title', 'Vous voulez comprendre notre processus ?');
$description    = v5_get_field_default('description', 'Consultez la méthodologie pour voir les critères de recherche, ou contactez l\'équipe éditoriale pour une correction ou une demande d\'ajout.');
$primary_text   = v5_get_field_default('primary_text', 'Lire la méthodologie');
$primary_link   = v5_get_field_default('primary_link', '/methodologie/');
$secondary_text = v5_get_field_default('secondary_text', 'Nous contacter');
$secondary_link = v5_get_field_default('secondary_link', '/contact/');
?>

<style>
    .about-cta {
        background: #ffffff;
        border-top: 1px solid rgba(16, 20, 24, 0.12);
        padding: clamp(32px, 4vw, 52px) 0;
    }

    .about-cta-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        width: min(1180px, calc(100% - 40px));
        margin: 0 auto;
    }

    .section-label {
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
    }

    .method-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        transition: transform 0.15s ease, background 0.15s ease, color 0.15s ease;
        text-decoration: none;
    }

    .method-button.primary { background: #101418; color: white; }
    .method-button.secondary { background: white; color: #101418; box-shadow: inset 0 0 0 1px rgba(16, 20, 24, 0.12); }
    .method-button:hover { transform: translateY(-1px); }

    @media (max-width: 820px) {
        .about-cta-inner { align-items: flex-start; flex-direction: column; }
    }
</style>

<?php 
$has_eyebrow   = !empty($eyebrow);
$has_title     = !empty($title);
$has_desc      = !empty($description);
$has_primary   = !empty($primary_text);
$has_secondary = !empty($secondary_text);

if ($has_eyebrow || $has_title || $has_desc || $has_primary || $has_secondary) :
?>
<section class="about-cta">
    <div class="about-cta-inner">
        <div>
            <?php if ($has_eyebrow) : ?>
                <span class="section-label"><?php echo esc_html($eyebrow); ?></span>
            <?php endif; ?>
            <?php if ($has_title) : ?>
                <h2 class="text-[1.65rem] md:text-[2.1rem] font-extrabold text-slate-900 tracking-tight leading-tight mb-2"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($has_desc) : ?>
                <p class="text-[15px] text-slate-500 leading-relaxed max-w-2xl"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
            <?php if ($has_primary) : ?>
                <a href="<?php echo esc_url(home_url($primary_link)); ?>" class="method-button primary">
                    <span><?php echo esc_html($primary_text); ?></span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            <?php endif; ?>
            <?php if ($has_secondary) : ?>
                <a href="<?php echo esc_url(home_url($secondary_link)); ?>" class="method-button secondary">
                    <span><?php echo esc_html($secondary_text); ?></span>
                    <i data-lucide="mail" class="w-4 h-4"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
