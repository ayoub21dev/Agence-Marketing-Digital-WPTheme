<?php
/**
 * Outcomes Section Layout (agence-marketing-digital)
 */
$eyebrow     = v5_get_field_default('eyebrow', 'Résultats clients vérifiés');
$title       = v5_get_field_default('title', 'Ce que disent les équipes après avoir choisi via Agence Marketing Digital');
$description = v5_get_field_default('description', 'Histoires réelles de sélection par des équipes marocaines ayant analysé les profils, les audits techniques et les avis vérifiés avant de contacter une agence.');
$metrics     = v5_get_field_default('metrics', null);
$reviews     = v5_get_field_default('reviews', null);
$reviews_cards = v5_get_field_default('reviews_cards', null);
$has_cards   = !empty($reviews_cards) && is_array($reviews_cards);

$has_eyebrow = !empty($eyebrow);
$has_title   = !empty($title);
$has_desc    = !empty($description);
$has_metrics = !empty($metrics) && is_array($metrics);
$has_reviews = true;

if ($has_eyebrow || $has_title || $has_desc || $metrics === null || $has_metrics || $has_reviews) :
?>

<section class="py-16 md:py-20 bg-white/70 border-b border-slate-200 backdrop-blur-sm">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <?php if ($has_eyebrow || $has_title || $has_desc) : ?>
            <div class="mx-auto mb-9 max-w-3xl text-center">
                <?php if ($has_eyebrow) : ?>
                    <span class="section-label text-slate-800 mb-2 block"><?php echo esc_html($eyebrow); ?></span>
                <?php endif; ?>
                
                <?php if ($has_title) : ?>
                    <h2 class="mt-3 text-[1.9rem] md:text-[2.5rem] font-bold tracking-tight text-slate-900 font-display leading-tight">
                        <?php echo esc_html($title); ?>
                    </h2>
                <?php endif; ?>
                
                <?php if ($has_desc) : ?>
                    <p class="mt-4 text-[15px] md:text-[16px] leading-relaxed text-slate-500">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($has_metrics || $metrics === null) : ?>
            <div class="mx-auto mb-10 grid max-w-2xl grid-cols-1 gap-4 text-center sm:grid-cols-3">
                <?php if ($has_metrics) : ?>
                    <?php $count = 0; foreach ($metrics as $metric) : $count++; ?>
                        <div class="<?php echo $count < 3 ? 'border-b border-slate-200 pb-4 sm:border-b-0 sm:border-r sm:pb-0' : ''; ?>">
                            <div class="font-display text-[1.7rem] font-bold text-slate-900"><?php echo esc_html($metric['value']); ?></div>
                            <div class="text-[12px] font-semibold text-slate-500"><?php echo esc_html($metric['label']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- Static Fallback Metrics in French -->
                    <div class="border-b border-slate-200 pb-4 sm:border-b-0 sm:border-r sm:pb-0">
                        <div class="font-display text-[1.7rem] font-bold text-slate-900">4.8</div>
                        <div class="text-[12px] font-semibold text-slate-500">Note moyenne vérifiée</div>
                    </div>
                    <div class="border-b border-slate-200 pb-4 sm:border-b-0 sm:border-r sm:pb-0">
                        <div class="font-display text-[1.7rem] font-bold text-slate-900">92%</div>
                        <div class="text-[12px] font-semibold text-slate-500">Recommanderaient l'annuaire</div>
                    </div>
                    <div>
                        <div class="font-display text-[1.7rem] font-bold text-slate-900">2 400+</div>
                        <div class="text-[12px] font-semibold text-slate-500">Avis clients vérifiés</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($has_reviews) : ?>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <?php if ($has_cards) : ?>
                    <?php foreach ($reviews_cards as $card) :
                        $c_quote   = isset($card['quote']) ? $card['quote'] : '';
                        $c_rating  = !empty($card['rating']) ? intval($card['rating']) : 5;
                        $c_author  = isset($card['author']) ? $card['author'] : '';
                        $c_role    = isset($card['role']) ? $card['role'] : '';
                        $c_image   = isset($card['image']) ? $card['image'] : '';
                        if (is_array($c_image)) { $c_image = isset($c_image['url']) ? $c_image['url'] : ''; }
                        $c_agency  = isset($card['agency_name']) ? $card['agency_name'] : '';
                        $c_url     = isset($card['agency_url']) ? $card['agency_url'] : '';
                        $c_project = isset($card['project']) ? $card['project'] : '';
                        $c_result  = isset($card['result']) ? $card['result'] : '';
                        ?>
                        <article class="flex min-h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-900 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                            <div class="flex-1 p-6">
                                <div role="img" class="mb-4 flex gap-0.5 text-amber-500" aria-label="<?php echo esc_attr($c_rating); ?> stars">
                                    <?php for ($i = 0; $i < 5; $i++) {
                                        echo $i < $c_rating
                                            ? '<i data-lucide="star" class="h-4 w-4 fill-amber-500"></i>'
                                            : '<i data-lucide="star" class="h-4 w-4 text-slate-200"></i>';
                                    } ?>
                                </div>
                                <p class="text-[15px] leading-relaxed text-slate-700">"<?php echo esc_html($c_quote); ?>"</p>
                                <div class="mt-7 flex items-center gap-3">
                                    <?php if (!empty($c_image)) : ?>
                                        <img src="<?php echo esc_url($c_image); ?>" alt="<?php echo esc_attr($c_author); ?>" class="h-11 w-11 rounded-full object-cover">
                                    <?php else : ?>
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-[12px] font-bold text-slate-500"><?php echo esc_html(substr($c_author, 0, 1)); ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="text-[14px] font-bold text-slate-950"><?php echo esc_html($c_author); ?></div>
                                        <div class="text-[12px] text-slate-500"><?php echo esc_html($c_role); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($c_agency) || !empty($c_project) || !empty($c_result)) : ?>
                            <div class="border-t border-slate-100 bg-slate-50 p-6 text-[13px]">
                                <div class="grid grid-cols-[74px_1fr] gap-y-3">
                                    <?php if (!empty($c_agency)) : ?>
                                        <span class="text-slate-500">Recrutée</span>
                                        <?php if (!empty($c_url)) : ?>
                                            <a href="<?php echo esc_url($c_url); ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-600 hover:text-brand-700"><?php echo esc_html($c_agency); ?></a>
                                        <?php else : ?>
                                            <span class="font-semibold text-brand-600"><?php echo esc_html($c_agency); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($c_project)) : ?>
                                        <span class="text-slate-500">Projet</span><span><?php echo esc_html($c_project); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($c_result)) : ?>
                                        <span class="text-slate-500">Résultat</span><span class="text-emerald-700"><?php echo esc_html($c_result); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else : ?>
                <?php
                $query_args = array(
                    'post_type'      => 'testimonial',
                    'post_status'    => 'publish',
                );

                $review_ids = array();
                if (!empty($reviews) && is_array($reviews)) {
                    foreach ($reviews as $review_item) {
                        if (is_object($review_item) && !empty($review_item->ID)) {
                            $review_ids[] = (int) $review_item->ID;
                        } elseif (is_numeric($review_item)) {
                            $review_ids[] = (int) $review_item;
                        }
                    }
                }

                if (!empty($review_ids)) {
                    $query_args['post__in'] = $review_ids;
                    $query_args['orderby'] = 'post__in';
                    $query_args['posts_per_page'] = -1;
                } else {
                    $query_args['posts_per_page'] = 3;
                    $query_args['orderby'] = 'ID';
                    $query_args['order'] = 'ASC';
                }

                $testimonial_query = new WP_Query($query_args);

                if (!$testimonial_query->have_posts() && !empty($review_ids)) {
                    wp_reset_postdata();
                    $testimonial_query = new WP_Query(array(
                        'post_type'      => 'testimonial',
                        'post_status'    => 'publish',
                        'posts_per_page' => 3,
                        'orderby'        => 'ID',
                        'order'          => 'ASC',
                    ));
                }

                if ($testimonial_query->have_posts()) :
                    while ($testimonial_query->have_posts()) : $testimonial_query->the_post();
                        $t_id   = get_the_ID();
                        $quote  = get_the_content();
                        $author = get_the_title();
                        $rating = get_field('rating', $t_id);
                        $role   = get_field('author_role', $t_id);
                        $image_media = get_field('author_image_media', $t_id);
                        $image_url   = get_field('author_image', $t_id);
                        $image = !empty($image_media) ? (is_array($image_media) ? $image_media['url'] : $image_media) : $image_url;
                        $agency_name = get_field('hired_agency_name', $t_id);
                        $agency_slug = get_field('hired_agency_slug', $t_id);
                        $agency_url  = get_field('hired_agency_url', $t_id);
                        $project = get_field('project', $t_id);
                        $result  = get_field('result', $t_id);

                        // Fallbacks
                        $rating = $rating ? intval($rating) : 5;
                        ?>
                        <article class="flex min-h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-900 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                            <div class="flex-1 p-6">
                                <div role="img" class="mb-4 flex gap-0.5 text-amber-500" aria-label="<?php echo esc_attr($rating); ?> stars">
                                    <?php 
                                    for ($i = 0; $i < 5; $i++) {
                                        if ($i < $rating) {
                                            echo '<i data-lucide="star" class="h-4 w-4 fill-amber-500"></i>';
                                        } else {
                                            echo '<i data-lucide="star" class="h-4 w-4 text-slate-200"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <p class="text-[15px] leading-relaxed text-slate-700">"<?php echo esc_html($quote); ?>"</p>
                                <div class="mt-7 flex items-center gap-3">
                                    <?php if (!empty($image)) : ?>
                                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($author); ?>" class="h-11 w-11 rounded-full object-cover">
                                    <?php endif; ?>
                                    <div>
                                        <div class="text-[14px] font-bold text-slate-950"><?php echo esc_html($author); ?></div>
                                        <div class="text-[12px] text-slate-500"><?php echo esc_html($role); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($agency_name) || !empty($project) || !empty($result)) : ?>
                            <div class="border-t border-slate-100 bg-slate-50 p-6 text-[13px]">
                                <div class="grid grid-cols-[74px_1fr] gap-y-3">
                                    <?php if (!empty($agency_name)) : ?>
                                        <span class="text-slate-500">Recrutée</span>
                                        <?php if (!empty($agency_url)) : ?>
                                            <a href="<?php echo esc_url($agency_url); ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-600 hover:text-brand-700"><?php echo esc_html($agency_name); ?></a>
                                        <?php else : ?>
                                            <span class="font-semibold text-brand-600"><?php echo esc_html($agency_name); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($project)) : ?>
                                        <span class="text-slate-500">Projet</span><span><?php echo esc_html($project); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($result)) : ?>
                                        <span class="text-slate-500">Résultat</span><span class="text-emerald-700"><?php echo esc_html($result); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    $fallback_reviews = array(
                        array(
                            'quote' => 'Nous avons enfin pu comparer les agences avec des critères clairs, pas seulement des promesses commerciales.',
                            'author' => 'Youssef Benali',
                            'role' => 'CMO, Craft Morocco',
                            'agency' => 'Atlas Digital',
                            'project' => 'SEO & Publicité Payante',
                            'result' => '+38% de leads qualifiés',
                        ),
                        array(
                            'quote' => 'Les avis vérifiés et les audits nous ont aidés à réduire notre shortlist sans perdre des semaines en appels.',
                            'author' => 'Nadia El Amrani',
                            'role' => 'Fondatrice, Maison Naya',
                            'agency' => 'Casa Growth',
                            'project' => 'E-commerce & Social Ads',
                            'result' => 'ROAS x2,4 en 90 jours',
                        ),
                        array(
                            'quote' => 'Le classement éditorial nous a donné le contexte local qu’on ne trouvait pas sur les annuaires internationaux.',
                            'author' => 'Mehdi Alaoui',
                            'role' => 'Directeur Marketing, SaaS Maroc',
                            'agency' => 'Rabat Performance',
                            'project' => 'Refonte acquisition',
                            'result' => '-27% de coût par lead',
                        ),
                    );

                    foreach ($fallback_reviews as $fallback_review) :
                        ?>
                        <article class="flex min-h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-900 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                            <div class="flex-1 p-6">
                                <div role="img" class="mb-4 flex gap-0.5 text-amber-500" aria-label="5 stars">
                                    <?php for ($i = 0; $i < 5; $i++) : ?>
                                        <i data-lucide="star" class="h-4 w-4 fill-amber-500"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-[15px] leading-relaxed text-slate-700">"<?php echo esc_html($fallback_review['quote']); ?>"</p>
                                <div class="mt-7 flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-[12px] font-bold text-slate-500">
                                        <?php echo esc_html(substr($fallback_review['author'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-bold text-slate-950"><?php echo esc_html($fallback_review['author']); ?></div>
                                        <div class="text-[12px] text-slate-500"><?php echo esc_html($fallback_review['role']); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 bg-slate-50 p-6 text-[13px]">
                                <div class="grid grid-cols-[74px_1fr] gap-y-3">
                                    <span class="text-slate-500">Recrutée</span>
                                    <span class="font-semibold text-brand-600"><?php echo esc_html($fallback_review['agency']); ?></span>
                                    <span class="text-slate-500">Projet</span>
                                    <span><?php echo esc_html($fallback_review['project']); ?></span>
                                    <span class="text-slate-500">Résultat</span>
                                    <span class="text-emerald-700"><?php echo esc_html($fallback_review['result']); ?></span>
                                </div>
                            </div>
                        </article>
                        <?php
                    endforeach;
                endif;
                ?>
                <?php endif; // end $has_cards ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
