<?php
/**
 * Layout: Search Filter Section
 * Standalone flexible content layout for the search filter block.
 */
?>
<section class="relative z-10 py-6 bg-white/80 backdrop-blur-sm search-filter-section">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <!-- Search Block -->
        <div class="max-w-3xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm p-4 md:p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Service</label>
                    <div class="relative">
                        <select id="home-filter-service" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-[13px] text-slate-700 search-focus appearance-none cursor-pointer">
                            <option value="all">Tous les services</option>
                            <option value="SEO">SEO (Référencement)</option>
                            <option value="Paid Ads">Publicité Payante</option>
                            <option value="Social Media">Réseaux Sociaux</option>
                            <option value="Web Design">Design Web</option>
                            <option value="Branding">Image de Marque</option>
                            <option value="Content Marketing">Marketing de Contenu</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Ville</label>
                    <div class="relative">
                        <select id="home-filter-city" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-[13px] text-slate-700 search-focus appearance-none cursor-pointer">
                            <option value="all">Toutes les villes</option>
                            <option value="Casablanca">Casablanca</option>
                            <option value="Rabat">Rabat</option>
                            <option value="Tangier">Tanger</option>
                            <option value="Marrakech">Marrakech</option>
                            <option value="Agadir">Agadir</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Note minimale</label>
                    <div class="relative">
                        <select id="home-filter-rating" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-[13px] text-slate-700 search-focus appearance-none cursor-pointer">
                            <option value="any">Toutes les notes</option>
                            <option value="4.5">4.5+ Étoiles</option>
                            <option value="4.0">4.0+ Étoiles</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-end">
                    <button onclick="triggerHomeSearch()" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 rounded-lg text-[13px] transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>Rechercher</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
