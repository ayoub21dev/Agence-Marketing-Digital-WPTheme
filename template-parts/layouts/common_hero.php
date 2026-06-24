<?php
/**
 * Common Hero Section Layout (Centered with breadcrumbs and animated title)
 */
$home_text    = v5_get_field_default('home_text', 'Accueil');
$current_text = v5_get_field_default('current_text', get_the_title());
$title        = v5_get_field_default('title', get_the_title() . ' <span class="quiet">.</span>');
$description  = v5_get_field_default('description', '');
$cta_text     = v5_get_field_default('cta_text', '');
$cta_link     = v5_get_field_default('cta_link', '');

$has_home    = !empty($home_text);
$has_current = !empty($current_text);
$has_title   = !empty($title);
$has_desc    = !empty($description);
$has_cta     = !empty($cta_text);

if ($has_home || $has_current || $has_title || $has_desc || $has_cta) :
?>

<style>
    .common-hero-title {
        max-width: 860px;
        margin: 0 auto;
        font-size: clamp(32px, 4vw, 52px);
        line-height: 1.1;
        font-weight: 800;
        color: #101418;
    }

    .common-hero-title .quiet {
        color: #2463eb;
        display: inline-block;
        position: relative;
        transform-origin: center bottom;
    }

    .common-hero-title .quiet::after {
        content: '';
        position: absolute;
        left: 0.02em;
        right: 0.02em;
        bottom: 0.02em;
        height: 0.11em;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.16);
        transform: scaleX(var(--common-hero-line, 0));
        transform-origin: left center;
        z-index: -1;
    }
</style>

<!-- Hero Header Section (v5_1 style: Centered with animation) -->
<section class="relative z-10 bg-white/80 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8 pt-16 pb-12 md:pt-20 md:pb-16 text-center">
        <!-- Breadcrumbs & Back -->
        <?php if ($has_home || $has_current) : ?>
        <div class="flex items-center justify-center gap-1.5 text-[11px] font-semibold text-slate-400 mb-4 tracking-wider uppercase font-mono">
            <?php if ($has_home) : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cursor-pointer hover:text-slate-900 transition-colors"><?php echo esc_html($home_text); ?></a>
            <?php endif; ?>
            <?php if ($has_home && $has_current) : ?>
                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            <?php endif; ?>
            <?php if ($has_current) : ?>
                <span class="text-slate-900 font-semibold"><?php echo esc_html($current_text); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($has_title) : ?>
        <h1 class="common-hero-title font-display">
            <?php echo wp_kses_post($title); ?>
        </h1>
        <?php endif; ?>
        
        <?php if ($has_desc) : ?>
            <p class="max-w-xl mx-auto mt-5 text-[16px] md:text-[18px] text-slate-500 leading-relaxed font-sans">
                <?php echo esc_html($description); ?>
            </p>
        <?php endif; ?>
        
        <?php if ($has_cta) : ?>
            <div class="method-actions mt-6">
                <a class="method-button primary" href="<?php echo esc_url($cta_link); ?>">
                    <?php echo esc_html($cta_text); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<script>
    // Trigger GSAP line animation for the quiet word in the title
    function animateCommonHeroWord() {
        const word = document.querySelector(".common-hero-title .quiet");
        if (!word || typeof gsap === "undefined") return;

        word.style.setProperty("--common-hero-line", 0);
        gsap.fromTo(word,
            { y: 18, autoAlpha: 0, scale: 0.92, rotate: -2 },
            { y: 0, autoAlpha: 1, scale: 1, rotate: 0, duration: 0.7, ease: "back.out(1.8)" }
        );
        gsap.to(word, {
            "--common-hero-line": 1,
            duration: 0.65,
            ease: "expo.out",
            delay: 0.18
        });
        gsap.to(word, {
            scale: 1.035,
            duration: 0.42,
            ease: "power2.inOut",
            yoyo: true,
            repeat: 1,
            delay: 0.35
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", animateCommonHeroWord);
    } else {
        animateCommonHeroWord();
    }
</script>
