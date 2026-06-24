<?php
/**
 * Layout: About Grid Section
 * Template part for rendering the 3-column about page grid.
 */
?>

<style>
    .about-wrap {
        width: min(1180px, calc(100% - 40px));
        margin: 0 auto;
    }

    .about-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        padding: clamp(24px, 4vw, 40px) 0 clamp(36px, 5vw, 56px);
        background: #ffffff;
    }

    .about-card {
        border: 1px solid rgba(16, 20, 24, 0.12);
        border-radius: 14px;
        padding: 26px;
        background: #fff;
        box-shadow: 0 1px 4px rgba(16,20,24,0.06), 0 0 0 1px rgba(16,20,24,0.07);
        transition: box-shadow 0.2s ease, transform 0.18s ease;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .about-card:hover {
        box-shadow: 0 8px 28px rgba(16,20,24,0.12), 0 0 0 1px rgba(16,20,24,0.08);
        transform: translateY(-2px);
    }

    .about-card h2 {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 8px;
        font-family: 'Space Grotesk', sans-serif;
        color: #101418;
    }

    .about-card p {
        color: #66717f;
        font-size: 13.5px;
        line-height: 1.62;
    }

    .about-card-icon-container {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #2563eb;
    }

    @media (max-width: 820px) {
        .about-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
        .about-wrap { width: min(100% - 28px, 1180px); }
    }
</style>

<?php if (have_rows('cards')) : ?>
    <section class="about-wrap about-grid">
        <?php while (have_rows('cards')) : the_row();
            $card_icon        = get_sub_field('card_icon');
            $card_title       = get_sub_field('card_title');
            $card_description = get_sub_field('card_description');
            ?>
            <article class="about-card">
                <?php if (!empty($card_icon)) : ?>
                    <div class="about-card-icon-container">
                        <i data-lucide="<?php echo esc_attr($card_icon); ?>" style="width: 20px; height: 20px; stroke-width: 2;"></i>
                    </div>
                <?php endif; ?>
                <?php if (!empty($card_title)) : ?>
                    <h2><?php echo esc_html($card_title); ?></h2>
                <?php endif; ?>
                <?php if (!empty($card_description)) : ?>
                    <p><?php echo esc_html($card_description); ?></p>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </section>
<?php endif; ?>
