<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <script type="text/javascript">
        window.onerror = function(message, source, lineno, colno, error) {
            var errorDiv = document.createElement('div');
            errorDiv.id = 'js-debug-error';
            errorDiv.style.display = 'block';
            errorDiv.style.color = 'red';
            errorDiv.style.padding = '10px';
            errorDiv.style.background = '#ffebeb';
            errorDiv.style.border = '1px solid red';
            errorDiv.style.margin = '10px';
            errorDiv.innerText = 'JS Error: ' + message + ' at ' + source + ':' + lineno + ':' + colno;
            document.addEventListener("DOMContentLoaded", function() {
                document.body.insertBefore(errorDiv, document.body.firstChild);
            });
        };
        window.addEventListener('unhandledrejection', function(event) {
            var reason = event.reason ? event.reason.toString() : '';
            if (reason.indexOf('AbortError') !== -1 || reason.indexOf('Transition was skipped') !== -1) {
                return;
            }
            var errorDiv = document.createElement('div');
            errorDiv.id = 'js-debug-rejection';
            errorDiv.style.display = 'block';
            errorDiv.style.color = 'red';
            errorDiv.style.padding = '10px';
            errorDiv.style.background = '#ffebeb';
            errorDiv.style.border = '1px solid red';
            errorDiv.style.margin = '10px';
            errorDiv.innerText = 'JS Promise Rejection: ' + event.reason;
            document.addEventListener("DOMContentLoaded", function() {
                document.body.insertBefore(errorDiv, document.body.firstChild);
            });
        });
    </script>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Space Grotesk', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
                        }
                    }
                }
            }
        }
    </script>
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

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                
                <!-- Logo Wordmark -->
                <div class="flex items-center gap-2 cursor-pointer font-display" onclick="window.location.href='<?php echo esc_url(home_url('/')); ?>'">
                    <div class="flex items-center gap-1">
                        <span class="font-extrabold text-[16px] text-slate-900 tracking-tight">Agence</span>
                        <span class="font-extrabold text-[16px] text-brand-600 tracking-tight">Marketing</span>
                        <span class="font-light text-[16px] text-slate-400 tracking-tight">Digital</span>
                    </div>
                </div>
                               
                <!-- Desktop Nav Links (Dynamic) -->
                <nav class="hidden md:flex items-center gap-7">
                    <?php
                    global $wp;
                    $menu_id = 0;
                    if (function_exists('pll_get_nav_menu_theme_loc')) {
                        $menu_id = pll_get_nav_menu_theme_loc('primary');
                    }
                    if (!$menu_id) {
                        $locations = get_nav_menu_locations();
                        if (!empty($locations)) {
                            if (function_exists('pll_current_language')) {
                                $lang_loc = 'primary___' . pll_current_language();
                                if (isset($locations[$lang_loc]) && $locations[$lang_loc] > 0) {
                                    $menu_id = $locations[$lang_loc];
                                }
                            }
                            if (!$menu_id && isset($locations['primary']) && $locations['primary'] > 0) {
                                $menu_id = $locations['primary'];
                            }
                            if (!$menu_id) {
                                foreach ($locations as $loc => $id) {
                                    if (strpos($loc, 'primary') === 0 && $id > 0) {
                                        $menu_id = $id;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if (!$menu_id) {
                        $menus = wp_get_nav_menus();
                        if (!empty($menus)) {
                            foreach ($menus as $m) {
                                $items = wp_get_nav_menu_items($m->term_id);
                                if (!empty($items)) {
                                    $menu_id = $m->term_id;
                                    break;
                                }
                            }
                        }
                    }
                    $menu_items = $menu_id ? wp_get_nav_menu_items($menu_id) : array();
                    
                    if (!empty($menu_items)) {
                        foreach ($menu_items as $item) {
                            if ($item->url === '#pll_switcher' || strpos($item->url, 'pll_switcher') !== false) {
                                continue;
                            }
                            $is_current = false;
                            $current_url = home_url(add_query_arg(array(), $wp->request));
                            $current_url = rtrim($current_url, '/');
                            $item_url = rtrim($item->url, '/');
                            
                            if ($item_url == $current_url || ($item_url == home_url() && is_front_page())) {
                                $is_current = true;
                            } elseif (strpos($item_url, '/blog') !== false && (is_post_type_archive('blog') || is_singular('blog') || is_page('blog'))) {
                                $is_current = true;
                            }
                            
                            $active_class = $is_current ? 'active' : '';
                            echo '<a href="' . esc_url($item->url) . '" class="nav-link ' . $active_class . ' text-[13px] font-medium transition-colors">' . esc_html($item->title) . '</a>';
                        }
                    } else {
                        // Fallback static links in French
                        $current_page_slug = basename(get_permalink());
                        ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link <?php echo is_front_page() ? 'active' : ''; ?> text-[13px] font-medium">accueil</a>
                        <a href="<?php echo esc_url(home_url('/annuaire/')); ?>" class="nav-link <?php echo is_page('annuaire') ? 'active' : ''; ?> text-[13px] font-medium">annuaire</a>
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="nav-link <?php echo is_post_type_archive('blog') || is_singular('blog') || is_page('blog') || is_home() || is_singular('post') ? 'active' : ''; ?> text-[13px] font-medium">blog</a>
                        <a href="<?php echo esc_url(home_url('/about/')); ?>" class="nav-link <?php echo is_page('about') ? 'active' : ''; ?> text-[13px] font-medium">à propos</a>
                        <a href="<?php echo esc_url(home_url('/methodologie/')); ?>" class="nav-link <?php echo is_page('methodologie') ? 'active' : ''; ?> text-[13px] font-medium">méthodologie</a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="nav-link <?php echo is_page('contact') ? 'active' : ''; ?> text-[13px] font-medium">contact</a>
                        <?php
                    }
                    ?>
                </nav>
                
                <!-- Nav Actions: Matchmaker, Translation, Mobile Menu Toggle -->
                <div class="flex items-center gap-3">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="hidden sm:flex bg-brand-600 hover:bg-brand-700 text-white font-semibold px-3.5 py-1.5 rounded-lg text-[12px] transition-all items-center gap-1 cursor-pointer">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 fill-white/20"></i>
                        <span>Trouver une agence</span>
                    </a>
                    

 
                    <button onclick="toggleMobileMenu()" class="md:hidden p-1.5 text-slate-600 hover:text-brand-600 cursor-pointer">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Dropdown Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white">
            <div class="px-5 py-2 space-y-0.5">
                <?php
                global $wp;
                if (!empty($menu_items)) {
                    foreach ($menu_items as $item) {
                        if ($item->url === '#pll_switcher' || strpos($item->url, 'pll_switcher') !== false) {
                            continue;
                        }
                        $is_current = (rtrim($item->url, '/') == rtrim(home_url(add_query_arg(array(), $wp->request)), '/')) || ($item->url == home_url() && is_front_page());
                        $active_class = $is_current ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50';
                        echo '<a href="' . esc_url($item->url) . '" class="block px-3 py-2 text-[13px] font-medium rounded-md ' . $active_class . '">' . esc_html($item->title) . '</a>';
                    }
                } else {
                    ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="block px-3 py-2 text-[13px] font-medium <?php echo is_front_page() ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50'; ?> rounded-md">accueil</a>
                    <a href="<?php echo esc_url(home_url('/annuaire/')); ?>" class="block px-3 py-2 text-[13px] font-medium <?php echo is_page('annuaire') ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50'; ?> rounded-md">annuaire</a>
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="block px-3 py-2 text-[13px] font-medium <?php echo is_page('blog') || is_home() || is_singular('blog') || is_post_type_archive('blog') || is_singular('post') ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50'; ?> rounded-md">blog</a>
                    <a href="<?php echo esc_url(home_url('/about/')); ?>" class="block px-3 py-2 text-[13px] font-medium <?php echo is_page('about') ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50'; ?> rounded-md">à propos</a>
                    <a href="<?php echo esc_url(home_url('/methodologie/')); ?>" class="block px-3 py-2 text-[13px] font-medium <?php echo is_page('methodologie') ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50'; ?> rounded-md">méthodologie</a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="block px-3 py-2 text-[13px] font-medium <?php echo is_page('contact') ? 'bg-slate-50 text-slate-900 font-semibold' : 'text-slate-700 hover:bg-slate-50'; ?> rounded-md">contact</a>
                    <?php
                }
                ?>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="w-full text-left block px-3 py-2 text-[13px] font-medium text-brand-600 hover:bg-slate-50 rounded-md flex items-center gap-1 font-semibold cursor-pointer">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 fill-brand-100"></i>
                    <span>Trouver une agence</span>
                </a>
            </div>
        </div>
    </header>
