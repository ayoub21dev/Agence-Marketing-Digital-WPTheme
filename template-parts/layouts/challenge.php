<?php
/**
 * Challenge Section Layout (v5-digital)
 */
$eyebrow         = v5_get_field_default('eyebrow', 'Le Défi');
$title           = v5_get_field_default('title', 'La plupart des entreprises choisissent la <span class="challenge-focus-word">mauvaise agence</span> en <span class="challenge-time-word">48 heures</span>.');
$description     = v5_get_field_default('description', 'Le marché des agences de marketing digital au Maroc est encombré, bruyant et opaque. Les beaux sites cachent souvent un travail médiocre. Les témoignages élogieux sont rarement vérifiés.');
$cards           = v5_get_field_default('cards', null);
$quote_text      = v5_get_field_default('quote_text', 'Nous avons brûlé deux agences et 200 000 dirhams avant de trouver une équipe qui a réellement livré des résultats. Si cet annuaire avait existé, nous aurions économisé six mois.');
$quote_author    = v5_get_field_default('quote_author', 'Youssef Benali');
$quote_role      = v5_get_field_default('quote_role', 'CMO, Craft Morocco');
$quote_verified  = v5_get_field_default('quote_verified', 'Avis vérifié');
$quote_scope     = v5_get_field_default('quote_scope', 'Projet : SEO & Publicité Payante');
$quote_image_media = v5_get_field_default('quote_image_media', null);
$quote_image_url   = v5_get_field_default('quote_image', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face');
$quote_image       = !empty($quote_image_media) ? $quote_image_media : $quote_image_url;

$has_eyebrow = !empty($eyebrow);
$has_title   = !empty($title);
$has_desc    = !empty($description);
$has_cards   = !empty($cards) && is_array($cards);
$has_quote   = !empty($quote_text);

if ($has_eyebrow || $has_title || $has_desc || $cards === null || $has_cards || $has_quote) :
?>

<section class="py-16 md:py-20 bg-white/70 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <?php if ($has_eyebrow || $has_title || $has_desc || $cards === null || $has_cards) : ?>
            <div class="<?php echo $has_quote ? 'lg:col-span-5' : 'lg:col-span-12'; ?> challenge-title-section">
                <?php if ($has_eyebrow) : ?>
                    <span class="section-label text-slate-800 mb-3 block"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                
                <?php if ($has_title) : ?>
                    <h2 class="text-[1.75rem] md:text-[2.25rem] font-bold text-slate-900 tracking-tight leading-tight mb-5 font-display">
                        <?php echo wp_kses_post($title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ($has_desc) : ?>
                    <p class="text-[15px] text-slate-500 leading-relaxed mb-6">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
                
                <div class="space-y-4">
                    <?php if ($has_cards) : ?>
                        <?php foreach ($cards as $card) : ?>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <?php if (!empty($card['icon_svg'])) : ?>
                                        <?php echo $card['icon_svg']; ?>
                                    <?php else : ?>
                                        <i data-lucide="x" class="w-3.5 h-3.5 text-slate-800"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if (!empty($card['title'])) : ?>
                                        <p class="text-[14px] font-semibold text-slate-900"><?php echo esc_html($card['title']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($card['description'])) : ?>
                                        <p class="text-[13px] text-slate-500 mt-0.5"><?php echo esc_html($card['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($cards === null) : ?>
                        <!-- Static Fallback Bullets in French -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="x" class="w-3.5 h-3.5 text-slate-800"></i>
                            </div>
                            <div>
                                <p class="text-[14px] font-semibold text-slate-900">Aucune comparaison indépendante</p>
                                <p class="text-[13px] text-slate-500 mt-0.5">Chaque agence se proclame "#1 au Maroc" sans preuve concrète.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="x" class="w-3.5 h-3.5 text-slate-800"></i>
                            </div>
                            <div>
                                <p class="text-[14px] font-semibold text-slate-900">Des avis non fiables</p>
                                <p class="text-[13px] text-slate-500 mt-0.5">Les témoignages sont sélectionnés à la main par les agences elles-mêmes.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="x" class="w-3.5 h-3.5 text-slate-800"></i>
                            </div>
                            <div>
                                <p class="text-[14px] font-semibold text-slate-900">Absence de contexte local</p>
                                <p class="text-[13px] text-slate-500 mt-0.5">Les annuaires mondiaux ne comprennent pas les spécificités du marché marocain.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($has_quote) : ?>
            <div class="<?php echo ($has_eyebrow || $has_title || $has_desc || $cards === null || $has_cards) ? 'lg:col-span-7' : 'lg:col-span-12'; ?>">
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-slate-100/50 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="relative">
                        <div style="font-family: Georgia, serif; font-size: 4rem; line-height: 1; color: #cbd5e1; position: absolute; top: -1rem; left: -0.5rem;">"</div>
                        <blockquote class="text-[17px] md:text-[19px] text-slate-700 font-medium leading-relaxed pl-6 mb-4 font-display">
                            <?php echo esc_html($quote_text); ?>
                        </blockquote>
                        <?php if (!empty($quote_author) || !empty($quote_role)) : ?>
                        <div class="flex items-center gap-3 pl-6">
                            <?php if ($quote_image) : ?>
                                <img src="<?php echo esc_url($quote_image); ?>" alt="<?php echo esc_attr($quote_author); ?>" class="w-10 h-10 rounded-full object-cover">
                            <?php endif; ?>
                            <div>
                                <?php if (!empty($quote_author)) : ?>
                                    <p class="text-[13px] font-semibold text-slate-900"><?php echo esc_html($quote_author); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($quote_role)) : ?>
                                    <p class="text-[12px] text-slate-500"><?php echo esc_html($quote_role); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($quote_verified) || !empty($quote_scope)) : ?>
                    <div class="mt-6 pt-6 border-t border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <?php if (!empty($quote_verified)) : ?>
                                <span class="text-[11px] font-semibold text-slate-800 uppercase tracking-wider"><?php echo esc_html($quote_verified); ?></span>
                                <i data-lucide="badge-check" class="w-3.5 h-3.5 text-slate-500"></i>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($quote_scope)) : ?>
                            <span class="text-[12px] text-slate-400"><?php echo esc_html($quote_scope); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
