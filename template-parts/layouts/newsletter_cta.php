<?php
/**
 * Layout: Section Newsletter
 * Template part for rendering the newsletter call-to-action block.
 */

$section_label     = v5_get_field_default('section_label', 'Newsletter · Gratuite');
$title             = v5_get_field_default('title', 'Recevez nos analyses dans votre boîte mail.');
$description       = v5_get_field_default('description', 'Chaque quinzaine : un audit d\'agence, un signal SEO à surveiller, une sélection de ressources pour les fondateurs et directeurs marketing au Maroc.');
$email_placeholder = v5_get_field_default('email_placeholder', 'votre@email.com');
$button_text       = v5_get_field_default('button_text', 'S\'abonner');
$footer_text       = v5_get_field_default('footer_text', 'Aucun spam · Désabonnement en 1 clic');
?>

<style>
    /* Scoped style for the Newsletter section, supporting standalone rendering */
    .blg-newsletter-band {
        --blg-ink:  #101418;
        --blg-muted:#66717f;
        --blg-line: rgba(16, 20, 24, 0.12);
        --blg-blue: #2463eb;
        
        border-top: 1px solid var(--blg-line);
        padding: clamp(28px, 4vw, 48px) 0;
        background: rgba(248, 250, 252, 0.5);
    }
    .blog-grid-wrap {
        width: min(1180px, calc(100% - 40px));
        margin: 0 auto;
    }
    .blg-section-label {
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    @media (max-width: 640px) {
        .blog-grid-wrap { width: min(100% - 28px, 1180px); }
    }
</style>

<section class="blg-newsletter-band">
    <div class="blog-grid-wrap">
        <div style="max-width:480px;">
            <div class="blg-section-label"><?php echo esc_html($section_label); ?></div>
            <h2 style="font-size:clamp(24px,3vw,38px);font-weight:800;line-height:1.15;color:var(--blg-ink);margin-top:8px;margin-bottom:8px;">
                <?php echo esc_html($title); ?>
            </h2>
            <p style="font-size:15px;color:var(--blg-muted);line-height:1.65;margin-top:8px;margin-bottom:20px;">
                <?php echo esc_html($description); ?>
            </p>
            <form style="display:flex;flex-direction:column;gap:12px;max-width:420px;"
                  onsubmit="blgSubmitNewsletter(event)">
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <input type="email" required placeholder="<?php echo esc_attr($email_placeholder); ?>" id="blg-nl-email"
                           style="flex:1;border:1.5px solid rgba(16,20,24,0.12);border-radius:10px;padding:10px 16px;font-size:14px;color:var(--blg-ink);background:white;outline:none;min-width:180px;transition:border-color 0.2s,box-shadow 0.2s;"
                           onfocus="this.style.borderColor='#2463eb';this.style.boxShadow='0 0 0 3px rgba(36,99,235,0.10)'"
                           onblur="this.style.borderColor='rgba(16,20,24,0.12)';this.style.boxShadow='none'">
                    <button type="submit" id="blg-nl-submit"
                            style="display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:44px;border-radius:10px;background:var(--blg-ink);color:white;font-weight:700;font-size:13px;padding:0 20px;border:none;cursor:pointer;transition:background 0.15s;white-space:nowrap;"
                            onmouseover="this.style.background='#000'" onmouseout="this.style.background='var(--blg-ink)'">
                        <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        <span><?php echo esc_html($button_text); ?></span>
                    </button>
                </div>
            </form>
            <?php if ($footer_text) : ?>
                <p style="font-size:11px;color:#94a3b8;font-family:monospace;margin-top:12px;">
                    <?php echo esc_html($footer_text); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function () {
    'use strict';
    
    if (!window.blgSubmitNewsletter) {
        window.blgSubmitNewsletter = function (e) {
            e.preventDefault();
            var btn = document.getElementById('blg-nl-submit');
            if (!btn) return;
            btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Abonné !';
            btn.style.background = '#16a34a';
            btn.disabled = true;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        };
    }
}());
</script>
