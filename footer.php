<?php
if (!function_exists('v5_digital_render_footer_column')) {
    function v5_digital_render_footer_column($theme_location, $fallback_slug, $default_title, $default_links) {
        // Fully WordPress-driven. The column appears ONLY when a menu is
        // assigned to this location (WordPress > Menus > Manage Locations) and
        // that menu has items. Delete or unassign the menu and the column
        // disappears from the footer — no hardcoded fallback links.
        $menu_id = 0;

        // Polylang-aware resolution of the assigned menu for this location.
        if (function_exists('pll_get_nav_menu_theme_loc')) {
            $menu_id = (int) pll_get_nav_menu_theme_loc($theme_location);
        }
        if (!$menu_id) {
            $locations = get_nav_menu_locations();
            if (!empty($locations)) {
                if (function_exists('pll_current_language')) {
                    $lang_loc = $theme_location . '___' . pll_current_language();
                    if (!empty($locations[$lang_loc])) {
                        $menu_id = (int) $locations[$lang_loc];
                    }
                }
                if (!$menu_id && !empty($locations[$theme_location])) {
                    $menu_id = (int) $locations[$theme_location];
                }
            }
        }

        if (!$menu_id) {
            return;
        }

        $menu_items = wp_get_nav_menu_items($menu_id);
        if (empty($menu_items) || is_wp_error($menu_items)) {
            return;
        }
        ?>
        <div>
            <h4 class="font-semibold text-slate-900 text-[13px] mb-3 font-display"><?php echo esc_html($default_title); ?></h4>
            <ul class="space-y-2 text-[13px]">
                <?php foreach ($menu_items as $item) : ?>
                    <li>
                        <a href="<?php echo esc_url($item->url); ?>" class="text-slate-500 hover:text-slate-900 transition-colors">
                            <?php echo esc_html($item->title); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

$matchmaker_services = array();
if (taxonomy_exists('agency_service')) {
    $matchmaker_service_terms = get_terms(array(
        'taxonomy'   => 'agency_service',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ));
    if (!is_wp_error($matchmaker_service_terms) && !empty($matchmaker_service_terms)) {
        foreach ($matchmaker_service_terms as $service_term) {
            $matchmaker_services[] = array(
                'value' => $service_term->name,
                'label' => $service_term->name,
            );
        }
    }
}
if (empty($matchmaker_services)) {
    $matchmaker_services = array(
        array('value' => 'SEO', 'label' => 'SEO (Référencement naturel)'),
        array('value' => 'Paid Ads', 'label' => 'Publicité Payante (Ads)'),
        array('value' => 'Social Media', 'label' => 'Réseaux Sociaux / Influence'),
        array('value' => 'Web Design', 'label' => 'Création & Design Web'),
    );
}
?>
    <footer class="site-footer bg-white border-t border-slate-200">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 py-10">
            <div class="site-footer-main max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-start gap-10 md:gap-16 mb-10">
                <div class="site-footer-brand md:w-72 shrink-0">
                    <div class="flex items-center gap-2 mb-3 cursor-pointer" onclick="window.location.href='<?php echo esc_url(home_url('/')); ?>'">
                        <div class="flex items-center gap-1 font-display">
                            <span class="font-extrabold text-[16px] text-slate-900 tracking-tight">Agence</span>
                            <span class="font-extrabold text-[16px] text-brand-600 tracking-tight">Marketing</span>
                            <span class="font-light text-[16px] text-slate-500 tracking-tight">Digital</span>
                        </div>
                    </div>
                    <p class="text-[13px] text-slate-500 leading-relaxed">
                        <?php
                        // Precedence: Site Settings option → site tagline → translated fallback.
                        $footer_desc = v5_digital_get_field('footer_description', 'option');
                        if (empty($footer_desc)) {
                            $footer_desc = get_bloginfo('description');
                        }
                        echo !empty($footer_desc) ? esc_html($footer_desc) : esc_html(v5_t('Analyses indépendantes des agences de marketing digital au Maroc. Évaluations objectives, guides de sélection pratiques et absence d\'influence publicitaire.'));
                        ?>
                    </p>
                </div>
                <div class="site-footer-nav">
                    <?php 
                    v5_digital_render_footer_column(
                        'footer_explore', 
                        'agence-footer-explore', 
                        'Découvrir', 
                        array(
                            'Accueil' => home_url('/'),
                            'Annuaire' => home_url('/annuaire/'),
                            'Blog' => home_url('/blog/')
                        )
                    );
                    
                    v5_digital_render_footer_column(
                        'footer_resources', 
                        'agence-footer-resources', 
                        'Ressources', 
                        array(
                            'Méthodologie' => home_url('/methodologie/'),
                            'Contact' => home_url('/contact/')
                        )
                    );
                    
                    v5_digital_render_footer_column(
                        'footer_villes', 
                        'agence-footer-company', 
                        'Villes', 
                        array(
                            'Casablanca' => home_url('/annuaire/?city=Casablanca'),
                            'Rabat' => home_url('/annuaire/?city=Rabat'),
                            'Tanger' => home_url('/annuaire/?city=Tangier'),
                            'Marrakech' => home_url('/annuaire/?city=Marrakech')
                        )
                    );
                    
                    v5_digital_render_footer_column(
                        'footer_legal', 
                        'agence-footer-legal', 
                        'Légal', 
                        array(
                            'Politique de Confidentialité' => '#',
                            'Conditions d\'Utilisation' => '#'
                        )
                    );
                    ?>
                </div>
            </div>
            <div class="site-footer-bottom max-w-4xl mx-auto border-t border-slate-200 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[12px] text-slate-500 font-mono">&copy; <?php echo esc_html(date('Y')); ?> Agence Marketing Digital. <?php echo esc_html(v5_t('Recherche indépendante')); ?>.</p>
                <?php
                // Social links from Site Settings — only filled ones render; the whole
                // block disappears when none are set (no dead "#" links).
                $footer_socials = array(
                    array('url' => v5_digital_get_field('twitter_url', 'option'),   'label' => 'Twitter',   'icon' => 'twitter'),
                    array('url' => v5_digital_get_field('linkedin_url', 'option'),  'label' => 'LinkedIn',  'icon' => 'linkedin'),
                    array('url' => v5_digital_get_field('instagram_url', 'option'), 'label' => 'Instagram', 'icon' => 'instagram'),
                    array('url' => v5_digital_get_field('facebook_url', 'option'),  'label' => 'Facebook',  'icon' => 'facebook'),
                );
                $footer_socials = array_filter($footer_socials, function ($s) { return !empty($s['url']); });
                if (!empty($footer_socials)) : ?>
                <div class="flex items-center gap-4 text-slate-500">
                    <?php foreach ($footer_socials as $social) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?>" class="hover:text-slate-700 transition-colors"><i data-lucide="<?php echo esc_attr($social['icon']); ?>" class="w-4 h-4" aria-hidden="true"></i></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <!-- ==================== MODALS ==================== -->

    <!-- Global Command Palette Search Overlay -->
    <dialog id="search-modal" class="backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm rounded-xl border border-slate-200 shadow-xl max-w-xl w-full p-0 bg-white overflow-hidden outline-none">
        <div class="p-4 border-b border-slate-150 flex items-center gap-3">
            <i data-lucide="search" class="w-5 h-5 text-slate-500 flex-shrink-0"></i>
            <input type="text" id="search-input" placeholder="<?php echo esc_attr(v5_t('Rechercher des agences, des pages, des villes...')); ?>" class="w-full text-[15px] outline-none text-slate-700 bg-transparent font-sans">
            <button onclick="closeSearchPalette()" aria-label="<?php echo esc_attr(v5_t('Fermer la recherche')); ?>" class="text-slate-500 hover:text-slate-600 cursor-pointer"><i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i></button>
        </div>
        <ul id="search-results" class="max-h-80 overflow-y-auto p-2 space-y-0.5 font-sans">
            <!-- Search results populated dynamically -->
        </ul>
        <div class="px-4 py-2 border-t border-slate-100 bg-slate-50 flex items-center justify-between text-[11px] text-slate-500 font-mono">
            <span>Appuyez sur ÉCHAP pour fermer</span>
            <span>recherche</span>
        </div>
    </dialog>

    <!-- Global Matchmaker Briefing Wizard Modal -->
    <dialog id="matchmaker-modal" class="backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-0 bg-white overflow-hidden outline-none">
        <div class="bg-gradient-to-br from-brand-900 to-brand-700 px-6 py-5 text-white flex items-start justify-between">
            <div>
                <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-blue-300 mb-1">Matchmaker</p>
                <h2 class="text-[1.2rem] font-extrabold tracking-tight font-display" id="mm-step-title">Trouvez votre agence idéale</h2>
                <p class="text-[12px] text-blue-200 mt-0.5" id="mm-step-subtitle">2 questions · 30 secondes</p>
            </div>
            <button onclick="closeMatchmaker()" class="text-blue-300 hover:text-white transition-colors mt-0.5 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        
        <div class="p-6" id="matching-wizard">
            <!-- Wizard Step 1 -->
            <div class="matching-wizard__step active" data-step="1">
                <p class="text-[13px] font-semibold text-slate-500 mb-3 uppercase tracking-wider">1. Quel service recherchez-vous ?</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 step-options">
                    <?php foreach ($matchmaker_services as $service) : ?>
                        <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="<?php echo esc_attr($service['value']); ?>">
                            <span><?php echo esc_html($service['label']); ?></span>
                            <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                        </button>
                    <?php endforeach; ?>
                </div>
                <button class="next-step-btn mt-6 w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 rounded-lg text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer" disabled>
                    <span>Étape suivante</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
            
            <!-- Wizard Step 2 -->
            <div class="matching-wizard__step" data-step="2">
                <p class="text-[13px] font-semibold text-slate-500 mb-3 uppercase tracking-wider">2. Quel est votre budget mensuel ?</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 step-options">
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="small">
                        <span>5 000 MAD – 15 000 MAD</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="medium">
                        <span>15 000 MAD – 50 000 MAD</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="large">
                        <span>50 000 MAD – 100 000 MAD</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="enterprise">
                        <span>Plus de 100 000 MAD</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                </div>
                <button class="submit-wizard-btn mt-6 w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 rounded-lg text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer" disabled>
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Trouver mon agence</span>
                </button>
            </div>
            
            <!-- Wizard Success -->
            <div class="matching-wizard__step text-center flex flex-col items-center gap-3 py-6" data-step="success">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center border border-emerald-100 flex-shrink-0">
                    <i data-lucide="check" class="w-6 h-6 stroke-[3px]"></i>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-[18px]">Agences recommandées trouvées !</h3>
                <p class="text-[13px] text-slate-500 max-w-sm">Nous avons analysé nos profils. Un conseiller va vous envoyer une sélection personnalisée par email sous 24 heures.</p>
            </div>
        </div>
    </dialog>

    <!-- Exit-intent newsletter capture. Opened by theme-scripts.js
         (initExitIntent) on desktop mouse-out-toward-chrome / mobile
         rapid-scroll-up; suppressed per-request via
         window.wpThemeSettings.exitIntentEnabled (v5_digital_exit_intent_enabled()). -->
    <dialog id="exit-intent-modal" aria-labelledby="ei-title" aria-describedby="ei-desc" class="backdrop:bg-slate-900/60 backdrop:backdrop-blur-sm rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-0 bg-white overflow-hidden outline-none">
        <!-- theme-scripts.js animates THIS wrapper, never the <dialog> itself:
             applying GSAP's transform tween directly to the dialog element
             was found to break its native top-layer centering (it would
             render far off-screen, offset by roughly however far the page
             had been scrolled — reproduced and confirmed in isolation). -->
        <div id="exit-intent-inner">
            <div class="bg-gradient-to-br from-brand-900 to-brand-700 px-6 py-5 text-white flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-blue-300 mb-1">Newsletter</p>
                    <h2 class="text-[1.2rem] font-extrabold tracking-tight font-display" id="ei-title">Avant de partir…</h2>
                    <p class="text-[12px] text-blue-200 mt-0.5" id="ei-desc">Recevez nos analyses d'agences par email</p>
                </div>
                <button type="button" onclick="closeExitIntent()" aria-label="<?php echo esc_attr(v5_t('Fermer')); ?>" class="text-blue-300 hover:text-white transition-colors mt-0.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="p-6" id="ei-body">
                <div id="ei-form-state">
                    <p class="text-[13px] text-slate-500 mb-4 leading-relaxed">Chaque quinzaine : un audit d'agence, un signal SEO à surveiller, une sélection de ressources pour les fondateurs et directeurs marketing au Maroc.</p>
                    <form id="exit-intent-form" class="flex flex-col gap-3">
                        <input type="email" id="ei-email" name="email" required placeholder="votre@email.com" autocomplete="email" class="w-full border border-slate-200 rounded-lg px-3.5 py-2.5 text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-500/10 transition-all">
                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 rounded-lg text-[13px] transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>S'abonner</span>
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-400 font-mono mt-3">Aucun spam · Désabonnement en 1 clic</p>
                </div>
                <div id="ei-success-state" class="hidden flex-col items-center text-center gap-1 py-2">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center border border-emerald-100 flex-shrink-0 mb-2">
                        <i data-lucide="check" class="w-6 h-6 stroke-[3px]"></i>
                    </div>
                    <h3 class="font-display font-bold text-slate-900 text-[16px]">Merci pour votre inscription !</h3>
                    <p class="text-[13px] text-slate-500">Vous recevrez notre prochaine analyse par email.</p>
                </div>
            </div>
        </div>
    </dialog>

    <style>
        .step-option-btn.active {
            border-color: #2563eb;
            background-color: #eff6ff;
        }
        .step-option-btn.active .select-check-icon {
            display: block !important;
        }
    </style>

    <!-- GSAP Libraries CDN (deferred: animations init on DOMContentLoaded) -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>

    <?php wp_footer(); ?>
</body>
</html>
