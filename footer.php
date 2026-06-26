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
?>
    <footer class="bg-white border-t border-slate-200">
        <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-6 mb-8">
                <div class="col-span-2">
                    <div class="flex items-center gap-2 mb-3 cursor-pointer" onclick="window.location.href='<?php echo esc_url(home_url('/')); ?>'">
                        <div class="flex items-center gap-1 font-display">
                            <span class="font-extrabold text-[16px] text-slate-900 tracking-tight">Agence</span>
                            <span class="font-extrabold text-[16px] text-brand-600 tracking-tight">Marketing</span>
                            <span class="font-light text-[16px] text-slate-500 tracking-tight">Digital</span>
                        </div>
                    </div>
                    <p class="text-[13px] text-slate-500 leading-relaxed max-w-sm">
                        <?php
                        $tagline = get_bloginfo('description');
                        echo !empty($tagline) ? esc_html($tagline) : esc_html(v5_t('Analyses indépendantes des agences de marketing digital au Maroc. Évaluations objectives, guides de sélection pratiques et absence d\'influence publicitaire.'));
                        ?>
                    </p>
                </div>
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
            <div class="border-t border-slate-200 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[12px] text-slate-500 font-mono">&copy; <?php echo esc_html(date('Y')); ?> Agence Marketing Digital. <?php echo esc_html(v5_t('Recherche indépendante')); ?>.</p>
                <div class="flex items-center gap-4 text-slate-500">
                    <a href="#" aria-label="Twitter" class="hover:text-slate-700 transition-colors"><i data-lucide="twitter" class="w-4 h-4" aria-hidden="true"></i></a>
                    <a href="#" aria-label="LinkedIn" class="hover:text-slate-700 transition-colors"><i data-lucide="linkedin" class="w-4 h-4" aria-hidden="true"></i></a>
                </div>
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
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="SEO">
                        <span>SEO (Référencement naturel)</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="Paid Ads">
                        <span>Publicité Payante (Ads)</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="Social Media">
                        <span>Réseaux Sociaux / Influence</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
                    <button class="step-option-btn text-left p-3 border border-slate-200 rounded-xl text-[13px] text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold cursor-pointer transition-all flex items-center justify-between" data-value="Web Design">
                        <span>Création & Design Web</span>
                        <i data-lucide="check" class="w-4 h-4 text-brand-600 hidden select-check-icon"></i>
                    </button>
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
