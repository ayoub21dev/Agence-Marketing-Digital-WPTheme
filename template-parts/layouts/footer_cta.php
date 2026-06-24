<?php
/**
 * Footer CTA Section Layout (v5-digital)
 */
$eyebrow       = v5_get_field_default('eyebrow', 'Matchmaker');
$title         = v5_get_field_default('title', 'Prêt à trouver la bonne agence digitale ?');
$description   = v5_get_field_default('description', 'Évitez les présentations commerciales génériques. Décrivez votre projet en 2 minutes et laissez nos audits indépendants identifier le partenaire idéal pour vos objectifs de croissance.');
$primary_btn   = v5_get_field_default('primary_cta_text', 'Lancer le Matchmaker');
$secondary_btn = v5_get_field_default('secondary_cta_text', "Explorer l'annuaire");

if (is_front_page() || $secondary_btn === 'none' || $secondary_btn === '') {
    $secondary_btn = '';
}
?>

<section class="py-16 md:py-20 bg-slate-50/50 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <?php if ($eyebrow) : ?>
                <span class="section-label text-slate-800 mb-2 block"><?php echo esc_html($eyebrow); ?></span>
            <?php endif; ?>
            
            <?php if ($title) : ?>
                <h2 class="text-[1.75rem] md:text-[2.25rem] font-bold text-slate-900 tracking-tight leading-tight mb-5 font-display">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php endif; ?>
            
            <?php if ($description) : ?>
                <p class="text-[15px] text-slate-500 leading-relaxed mb-8">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>"
                    class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold px-7 py-3 rounded-lg text-[14px] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-lg shadow-brand-600/10">
                    <span><?php echo esc_html($primary_btn); ?></span>
                </a>
                <?php if (!empty($secondary_btn)) : ?>
                <a href="<?php echo esc_url(home_url('/annuaire/')); ?>"
                    class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold px-7 py-3 rounded-lg text-[14px] transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="compass" class="w-4 h-4 text-slate-400"></i>
                    <span><?php echo esc_html($secondary_btn); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
