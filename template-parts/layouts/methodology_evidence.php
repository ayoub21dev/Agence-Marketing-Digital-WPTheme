<?php
/**
 * Layout: Methodology Evidence Section
 * Template part for rendering the evidence matrix diagram and description list.
 */

$section_label = v5_get_field_default('section_label', '02 / Carte des Preuves');
$title         = v5_get_field_default('title', 'Les affirmations nécessitent des preuves.');
$evidence_rows = v5_get_field_default('evidence_rows', null);

$has_label = !empty($section_label);
$has_title = !empty($title);
$has_rows  = !empty($evidence_rows) && is_array($evidence_rows);

if ($has_label || $has_title || $evidence_rows === null || $has_rows) :
?>

<style>
    .blg-evidence-section {
        --line: rgba(16, 20, 24, 0.12);
        --ink: #101418;
        --muted: #66717f;
        --blue: #2463eb;
        
        padding: clamp(32px, 4vw, 52px) 0;
        border-top: 1px solid var(--line);
        background: #ffffff;
    }
    .evidence-layout {
        display: grid;
        grid-template-columns: minmax(0, 0.42fr) minmax(0, 0.58fr);
        gap: 42px;
        align-items: center;
    }
    .evidence-visual {
        background: white;
        border-radius: 12px;
        padding: clamp(16px, 3vw, 24px);
        box-shadow: 0 1px 3px rgba(16, 20, 24, 0.04), 0 0 0 1px rgba(16, 20, 24, 0.06);
    }
    .matrix-svg { width: 100%; height: auto; display: block; }
    .matrix-svg text { font-family: 'Space Grotesk', system-ui, sans-serif; }
    .evidence-list { display: grid; gap: 0; }
    .evidence-row {
        display: grid;
        grid-template-columns: 96px 1fr;
        gap: 18px;
        align-items: start;
        padding: 16px 0;
        border-top: 1px solid var(--line);
    }
    .evidence-row:first-child { border-top: 0; }
    .evidence-row strong {
        font: 800 11px/1.4 'JetBrains Mono', monospace;
        color: var(--ink);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 2px;
    }
    .evidence-row p { color: var(--muted); font-size: 13.5px; line-height: 1.6; }

    @media (max-width: 980px) {
        .evidence-layout { grid-template-columns: 1fr; gap: 32px; }
    }
    @media (max-width: 640px) {
        .evidence-row { grid-template-columns: 1fr; gap: 6px; }
    }
</style>

<section class="blg-evidence-section">
    <div class="method-wrap">
        <div class="evidence-layout">
            <div class="evidence-visual">
                <!-- High-Fidelity Evidence Matrix SVG -->
                <svg class="matrix-svg" viewBox="0 0 420 420" role="img" aria-label="Evidence matrix diagram">
                    <!-- Background Panel -->
                    <rect x="10" y="10" width="400" height="400" rx="12" fill="#ffffff" stroke="rgba(16,20,24,0.08)" stroke-width="1.5" />
                    
                    <!-- Grid Background Lines -->
                    <path d="M100 10v400M190 10v400M280 10v400M370 10v400M10 100h400M10 190h400M10 280h400M10 370h400" stroke="rgba(16, 20, 24, 0.05)" stroke-width="1" />
                    
                    <!-- Connection Path (Bilingual Flow) -->
                    <path d="M80 80h120v110H80v110h240V80h-120" fill="none" stroke="rgba(37, 99, 235, 0.12)" stroke-width="2" stroke-dasharray="4,4" />
                    <path d="M80 80h120v110h120v90" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    
                    <!-- Category Labels -->
                    <g fill="#101418" font-family="'Space Grotesk', sans-serif" font-size="10" font-weight="800" letter-spacing="0.05em">
                        <text x="80" y="50" text-anchor="middle">LEGAL</text>
                        <text x="200" y="50" text-anchor="middle">WORK</text>
                        <text x="320" y="50" text-anchor="middle">CLIENT</text>
                    </g>
                    
                    <!-- Row Status labels -->
                    <g fill="#64748b" font-family="'JetBrains Mono', monospace" font-size="9" font-weight="500">
                        <text x="25" y="83" text-anchor="start">STEP 1</text>
                        <text x="25" y="193" text-anchor="start">STEP 2</text>
                        <text x="25" y="283" text-anchor="start">STEP 3</text>
                        <text x="25" y="363" text-anchor="start">FINAL</text>
                    </g>

                    <!-- Nodes & Checkmarks -->
                    <circle cx="80" cy="80" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M75 80l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="200" cy="80" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M195 80l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="320" cy="80" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M315 80l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="320" cy="190" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M315 190l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="200" cy="190" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M195 190l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="80" cy="190" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M75 190l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="80" cy="280" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M75 280l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="200" cy="280" r="14" fill="#eff6ff" stroke="#3b82f6" stroke-width="1.5" />
                    <path d="M195 280l3 3 6-6" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <circle cx="320" cy="280" r="14" fill="#ecfdf5" stroke="#10b981" stroke-width="1.5" />
                    <path d="M315 280l3 3 6-6" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                    <!-- Final verified badge -->
                    <g transform="translate(170, 355)">
                        <rect x="0" y="0" width="80" height="30" rx="15" fill="#f59e0b" />
                        <text x="40" y="19" text-anchor="middle" font-family="'JetBrains Mono', monospace" font-size="10" font-weight="800" fill="white">VERIFIED</text>
                    </g>
                </svg>
            </div>

            <div>
                <?php if ($has_label) : ?>
                    <div class="section-label"><?php echo esc_html($section_label); ?></div>
                <?php endif; ?>
                <?php if ($has_title) : ?>
                    <h2 class="section-title" style="margin-bottom:20px;"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                
                <?php if (have_rows('evidence_rows')) : ?>
                    <div class="evidence-list">
                        <?php while (have_rows('evidence_rows')) : the_row();
                            $row_title = get_sub_field('row_title');
                            $row_desc  = get_sub_field('row_description');
                            ?>
                            <div class="evidence-row">
                                <strong><?php echo esc_html($row_title); ?></strong>
                                <p><?php echo esc_html($row_desc); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
