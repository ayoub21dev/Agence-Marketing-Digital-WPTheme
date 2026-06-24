<?php
/**
 * Layout: Methodology Process Validation
 * Template part for rendering the 4-stage validation process.
 */

$section_label = v5_get_field_default('section_label', '01 / Processus de Validation');
$title         = v5_get_field_default('title', 'Quatre étapes. Un seul standard.');
$description   = v5_get_field_default('description', 'La même séquence est appliquée à chaque agence répertoriée, qu\'elle ait postulé ou été découverte par notre équipe.');
$stages        = v5_get_field_default('stages', null);

$has_label = !empty($section_label);
$has_title = !empty($title);
$has_desc  = !empty($description);
$has_stages = !empty($stages) && is_array($stages);

if ($has_label || $has_title || $has_desc || $stages === null || $has_stages) :
?>

<style>
    .blg-methodology-section {
        --line: rgba(16, 20, 24, 0.12);
        --ink: #101418;
        --muted: #66717f;
        --blue: #2463eb;
        
        padding: clamp(32px, 4vw, 52px) 0;
        border-top: 1px solid var(--line);
        background: #ffffff;
    }
    .method-wrap {
        width: min(1180px, calc(100% - 40px));
        margin: 0 auto;
    }
    .section-head {
        display: grid;
        grid-template-columns: minmax(0, 0.68fr) minmax(280px, 0.32fr);
        align-items: end;
        gap: 32px;
        margin-bottom: 28px;
    }
    .section-label {
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    .section-title {
        font-size: clamp(24px, 3vw, 38px);
        line-height: 1.15;
        font-weight: 800;
        color: var(--ink);
    }
    .section-copy {
        color: var(--muted);
        font-size: 15px;
        line-height: 1.72;
    }
    .process-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1px;
        background: var(--line);
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 20, 24, 0.04), 0 0 0 1px rgba(16, 20, 24, 0.06);
    }
    .process-stage {
        background: white;
        min-height: 340px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stage-num {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        color: #a4adba;
        font-size: 12px;
    }
    .stage-icon { width: 100%; height: 100px; margin: 12px 0 18px; }
    .process-stage h3 {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 8px;
        color: var(--ink);
    }
    .process-stage p {
        color: var(--muted);
        font-size: 13.5px;
        line-height: 1.6;
    }
    .stage-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 16px;
    }
    .stage-tags span {
        background: #f1f4f8;
        color: #4f5c6b;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 10.5px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
    }

    @media (max-width: 980px) {
        .section-head { grid-template-columns: 1fr; gap: 16px; }
        .process-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 640px) {
        .method-wrap { width: min(100% - 28px, 1180px); }
        .process-grid { grid-template-columns: 1fr; }
        .process-stage { min-height: 280px; }
    }
</style>

<section id="process" class="blg-methodology-section">
    <div class="method-wrap">
        <div class="section-head">
            <div>
        <?php if ($has_label || $has_title || $has_desc) : ?>
        <div class="section-head">
            <div>
                <?php if ($has_label) : ?>
                    <div class="section-label"><?php echo esc_html($section_label); ?></div>
                <?php endif; ?>
                <?php if ($has_title) : ?>
                    <h2 class="section-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
            <?php if ($has_desc) : ?>
                <p class="section-copy"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (have_rows('stages')) : ?>
            <div class="process-grid">
                <?php while (have_rows('stages')) : the_row();
                    $stage_num         = get_sub_field('stage_num');
                    $stage_title       = get_sub_field('stage_title');
                    $stage_description = get_sub_field('stage_description');
                    $stage_tags_raw    = get_sub_field('stage_tags');
                    $stage_tags        = $stage_tags_raw ? array_map('trim', explode(',', $stage_tags_raw)) : array();
                    $stage_index       = get_row_index();
                    $stage_image       = get_sub_field('stage_image');
                    $stage_icon        = get_sub_field('stage_icon');
                    ?>
                    <article class="process-stage">
                        <div>
                            <span class="stage-num"><?php echo esc_html($stage_num); ?></span>
                            
                            <?php if (!empty($stage_image)) : ?>
                                <div class="stage-icon" style="display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img src="<?php echo esc_url($stage_image); ?>" alt="<?php echo esc_attr($stage_title); ?>" style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;" />
                                </div>
                            <?php elseif (!empty($stage_icon)) : ?>
                                <div class="stage-icon" style="display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="<?php echo esc_attr($stage_icon); ?>" style="width: 48px; height: 48px; stroke-width: 1.5; color: #2563eb;"></i>
                                </div>
                            <?php else : ?>
                                <?php if ($stage_index == 1) : ?>
                                    <svg class="stage-icon" viewBox="0 0 220 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="56" y="22" width="108" height="76" rx="8" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.5" />
                                        <line x1="72" y1="42" x2="112" y2="42" stroke="#94a3b8" stroke-width="1.5" />
                                        <line x1="72" y1="56" x2="128" y2="56" stroke="#94a3b8" stroke-width="1.5" />
                                        <line x1="72" y1="70" x2="102" y2="70" stroke="#94a3b8" stroke-width="1.5" />
                                        <circle cx="152" cy="74" r="16" fill="white" stroke="#2563eb" stroke-width="1.5" />
                                        <path d="M147 74l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" />
                                    </svg>
                                <?php elseif ($stage_index == 2) : ?>
                                    <svg class="stage-icon" viewBox="0 0 220 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="40" y="20" width="140" height="80" rx="8" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.5" />
                                        <circle cx="54" cy="32" r="3" fill="#cbd5e1" />
                                        <circle cx="64" cy="32" r="3" fill="#cbd5e1" />
                                        <circle cx="74" cy="32" r="3" fill="#cbd5e1" />
                                        <path d="M54 82 L84 62 L114 72 L144 48 L166 52" fill="none" stroke="#2563eb" stroke-width="2" />
                                        <circle cx="144" cy="48" r="3" fill="#2563eb" stroke="white" stroke-width="1.5" />
                                        <line x1="50" y1="52" x2="170" y2="52" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2,2" />
                                        <line x1="50" y1="72" x2="170" y2="72" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="2,2" />
                                    </svg>
                                <?php elseif ($stage_index == 3) : ?>
                                    <svg class="stage-icon" viewBox="0 0 220 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="35" y="30" width="60" height="60" rx="8" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.5" />
                                        <circle cx="65" cy="52" r="10" fill="#e2e8f0" stroke="#cbd5e1" stroke-width="1.2" />
                                        <path d="M50 78 C50 68, 80 68, 80 78" fill="none" stroke="#cbd5e1" stroke-width="1.2" />
                                        <rect x="125" y="30" width="60" height="60" rx="8" fill="#eff6ff" stroke="#bfdbfe" stroke-width="1.5" />
                                        <circle cx="155" cy="52" r="10" fill="#bfdbfe" stroke="#3b82f6" stroke-width="1.2" />
                                        <path d="M140 78 C140 68, 170 68, 170 78" fill="none" stroke="#3b82f6" stroke-width="1.2" />
                                        <line x1="95" y1="60" x2="125" y2="60" stroke="#2563eb" stroke-width="1.5" stroke-dasharray="3,3" />
                                        <circle cx="110" cy="60" r="10" fill="#10b981" />
                                        <path d="M106 60l3 3 5-5" fill="none" stroke="white" stroke-width="1.5" />
                                    </svg>
                                <?php else: ?>
                                    <svg class="stage-icon" viewBox="0 0 220 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <defs>
                                            <linearGradient id="dialGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.6" />
                                                <stop offset="100%" stop-color="#2563eb" />
                                            </linearGradient>
                                        </defs>
                                        <path d="M60 90 A50 50 0 0 1 160 90" fill="none" stroke="#f1f5f9" stroke-width="6" stroke-linecap="round" />
                                        <path d="M60 90 A50 50 0 0 1 157 73" fill="none" stroke="url(#dialGradient)" stroke-width="6" stroke-linecap="round" />
                                        <line x1="60" y1="90" x2="65" y2="90" stroke="#cbd5e1" stroke-width="1" />
                                        <line x1="110" y1="40" x2="110" y2="45" stroke="#cbd5e1" stroke-width="1" />
                                        <line x1="160" y1="90" x2="155" y2="90" stroke="#cbd5e1" stroke-width="1" />
                                        <line x1="110" y1="90" x2="148" y2="76" stroke="#0f172a" stroke-width="2" stroke-linecap="round" />
                                        <circle cx="110" cy="90" r="4" fill="#0f172a" stroke="white" stroke-width="1" />
                                        <text x="110" y="82" text-anchor="middle" font-family="'JetBrains Mono', monospace" font-size="14" font-weight="800" fill="#0f172a" stroke="none">8.9</text>
                                        <text x="110" y="102" text-anchor="middle" font-family="'Inter', sans-serif" font-size="8" font-weight="600" fill="#94a3b8" stroke="none" letter-spacing="0.05em">INDEX SCORE</text>
                                    </svg>
                                <?php endif; ?>
                            <?php endif; ?>

                            <h3><?php echo esc_html($stage_title); ?></h3>
                            <p><?php echo esc_html($stage_description); ?></p>
                        </div>
                        <?php if (!empty($stage_tags)) : ?>
                            <div class="stage-tags">
                                <?php foreach ($stage_tags as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
