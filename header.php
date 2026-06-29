<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Load Google Fonts without blocking render: fetch as a "print" sheet,
         then promote it to "all" once it has loaded. <noscript> keeps it working
         when JS is disabled. -->
    <link rel="stylesheet" media="print" onload="this.media='all'" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" />
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" />
    </noscript>
    
    <!-- Tailwind utilities are now compiled to /assets/css/tailwind.css and
         enqueued by the theme (no more render-blocking CDN runtime). -->

    <!-- Lucide Icons (deferred: icons are drawn on DOMContentLoaded) -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <script type="text/javascript">
        window.wpThemeSettings = {
            homeUrl: "<?php echo esc_url(home_url('/')); ?>"
        };
    </script>

    <style>
        .hidden { display: none; }
        /* Prevent GSAP flash of unstyled elements */
        .hero-title, .section-label, main h1 + p, main .hero-title + p {
            opacity: 0;
        }
        /* When motion system loads, it adds .motion-enhanced to body */
        .motion-enhanced .hero-title, 
        .motion-enhanced .section-label, 
        .motion-enhanced main h1 + p, 
        .motion-enhanced main .hero-title + p {
            opacity: 1;
        }
        /* Fallback for no JS or reduced motion */
        body:not(.motion-enhanced) .hero-title,
        body:not(.motion-enhanced) .section-label,
        body:not(.motion-enhanced) main h1 + p,
        body:not(.motion-enhanced) main .hero-title + p {
            opacity: 1;
        }
    </style>
    <?php wp_head(); ?>
</head>
<body <?php body_class('text-slate-800 antialiased min-h-screen flex flex-col justify-between'); ?>>
<?php
if (function_exists('wp_body_open')) {
    wp_body_open();
}
?>

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                
                <!-- Logo Wordmark -->
                <div class="flex items-center gap-2 cursor-pointer font-display" onclick="window.location.href='<?php echo esc_url(home_url('/')); ?>'">
                    <div class="flex items-center gap-1">
                        <span class="font-extrabold text-[16px] text-slate-900 tracking-tight">Agence</span>
                        <span class="font-extrabold text-[16px] text-brand-600 tracking-tight">Marketing</span>
                        <span class="font-light text-[16px] text-slate-500 tracking-tight">Digital</span>
                    </div>
                </div>
                               
                <!-- Desktop Nav Links (Dynamic) -->
                <nav class="hidden md:flex items-center gap-7">
                    <?php
                    // Resolved once here and reused by the mobile menu below.
                    $menu_items = v5_digital_get_primary_menu_items();

                    if (!empty($menu_items)) {
                        foreach ($menu_items as $item) {
                            $active_class = v5_digital_menu_item_is_active($item) ? 'active' : '';
                            echo '<a href="' . esc_url($item->url) . '" class="nav-link ' . $active_class . ' text-[13px] font-medium transition-colors">' . esc_html($item->title) . '</a>';
                        }
                    } else {
                        foreach (v5_digital_nav_fallback_links() as $link) {
                            $active_class = v5_digital_nav_fallback_is_active($link['check']) ? 'active' : '';
                            echo '<a href="' . esc_url($link['url']) . '" class="nav-link ' . $active_class . ' text-[13px] font-medium">' . esc_html(v5_t($link['label'])) . '</a>';
                        }
                    }
                    ?>
                </nav>
                
                <!-- Nav Actions: Matchmaker, optional language switcher, Mobile Menu Toggle -->
                <div class="flex items-center gap-3">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="hidden sm:flex bg-brand-600 hover:bg-brand-700 text-white font-semibold px-3.5 py-1.5 rounded-lg text-[12px] transition-all items-center gap-1 cursor-pointer">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 fill-white/20"></i>
                        <span><?php echo esc_html(v5_t('Trouver une agence')); ?></span>
                    </a>

                    <?php
                    if (v5_digital_primary_menu_has_language_switcher()) {
                        v5_digital_language_switcher();
                    }
                    ?>

                    <button onclick="toggleMobileMenu()" aria-label="<?php echo esc_attr(v5_t('Ouvrir le menu')); ?>" aria-expanded="false" class="md:hidden p-1.5 text-slate-600 hover:text-brand-600 cursor-pointer">
                        <i data-lucide="menu" class="w-5 h-5" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Dropdown Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white">
            <div class="px-5 py-2 space-y-0.5">
                <?php
                // Reuses $menu_items resolved by the desktop nav above.
                if (!empty($menu_items)) {
                    foreach ($menu_items as $item) {
                        $active_class = v5_digital_menu_item_is_active($item) ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50';
                        echo '<a href="' . esc_url($item->url) . '" class="block px-3 py-2 text-[13px] font-medium rounded-md ' . $active_class . '">' . esc_html($item->title) . '</a>';
                    }
                } else {
                    foreach (v5_digital_nav_fallback_links() as $link) {
                        $active_class = v5_digital_nav_fallback_is_active($link['check']) ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50';
                        echo '<a href="' . esc_url($link['url']) . '" class="block px-3 py-2 text-[13px] font-medium rounded-md ' . $active_class . '">' . esc_html(v5_t($link['label'])) . '</a>';
                    }
                }
                ?>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="w-full text-left block px-3 py-2 text-[13px] font-medium text-brand-600 hover:bg-slate-50 rounded-md flex items-center gap-1 font-semibold cursor-pointer">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 fill-brand-100"></i>
                    <span><?php echo esc_html(v5_t('Trouver une agence')); ?></span>
                </a>
            </div>
        </div>
    </header>
