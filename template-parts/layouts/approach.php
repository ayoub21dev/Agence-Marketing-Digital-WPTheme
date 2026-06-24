<?php
/**
 * Approach Section Layout (v5-digital)
 */
$eyebrow     = v5_get_field_default('eyebrow', 'Notre Approche');
$title       = v5_get_field_default('title', 'Une <span class="approach-focus-word">meilleure façon</span> de <span class="approach-action-word">choisir</span> une agence.');
$description = v5_get_field_default('description', 'Nous faisons les recherches pour que vous puissiez vous concentrer sur la décision.');
$points      = v5_get_field_default('points', null);

$has_eyebrow = !empty($eyebrow);
$has_title   = !empty($title);
$has_desc    = !empty($description);
$has_points  = !empty($points) && is_array($points);

// Only show layout if at least one field or points are populated (or if the defaults are active)
if ($has_eyebrow || $has_title || $has_desc || $points === null || $has_points) :
?>

<section class="py-16 md:py-20 bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <?php if ($has_eyebrow || $has_title || $has_desc) : ?>
            <div class="text-center mb-12 approach-title-section">
                <?php if ($has_eyebrow) : ?>
                    <span class="section-label text-slate-800 mb-2 block"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                
                <?php if ($has_title) : ?>
                    <h2 class="text-[1.75rem] md:text-[2.25rem] font-bold text-slate-900 tracking-tight font-display">
                        <?php echo wp_kses_post($title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ($has_desc) : ?>
                    <p class="text-[15px] text-slate-500 mt-2 max-w-md mx-auto">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <?php if ($has_points) : ?>
                <?php foreach ($points as $point) : ?>
                    <div class="relative bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <?php if (!empty($point['number'])) : ?>
                            <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-[11px] font-bold mb-4 font-mono">
                                <?php echo esc_html($point['number']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($point['title'])) : ?>
                            <h3 class="font-bold text-[16px] text-slate-900 mb-2 font-display"><?php echo esc_html($point['title']); ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($point['description'])) : ?>
                            <p class="text-[13.5px] text-slate-500 leading-relaxed"><?php echo esc_html($point['description']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($points === null) : ?>
                <!-- Static Fallback Steps in French -->
                <div class="relative bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-[11px] font-bold mb-4 font-mono">1</div>
                    <h3 class="font-bold text-[16px] text-slate-900 mb-2 font-display">Recherche approfondie</h3>
                    <p class="text-[13.5px] text-slate-500 leading-relaxed">Notre équipe éditoriale audite les portefeuilles, interroge de vrais clients et vérifie les registres commerciaux de chaque agence avant qu'elle n'apparaisse sur notre plateforme.</p>
                </div>
                <div class="relative bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-[11px] font-bold mb-4 font-mono">2</div>
                    <h3 class="font-bold text-[16px] text-slate-900 mb-2 font-display">Évaluation vérifiée</h3>
                    <p class="text-[13.5px] text-slate-500 leading-relaxed">Chaque agence reçoit une note indépendante basée sur quatre critères pondérés : les avis clients, la qualité des réalisations, la présence sur le marché et la largeur de service.</p>
                </div>
                <div class="relative bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-[11px] font-bold mb-4 font-mono">3</div>
                    <h3 class="font-bold text-[16px] text-slate-900 mb-2 font-display">Mise en relation directe</h3>
                    <p class="text-[13.5px] text-slate-500 leading-relaxed">Contactez directement les agences via des profils vérifiés. Pas de frais de mise en relation, pas d'intermédiaire, pas de commissions cachées. Juste des informations honnêtes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
