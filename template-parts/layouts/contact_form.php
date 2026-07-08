<?php
/**
 * Contact Form Section Layout
 */
$form_title      = v5_get_field_default('form_title', 'Envoyer un Message');
$form_desc       = v5_get_field_default('form_desc', 'Utilisez ce formulaire pour les demandes de référencement, les corrections, les questions des acheteurs ou les notes de partenariat.');
$office_icon     = v5_get_field_default('office_icon', '');
// Contact info comes from ONE place: Site Settings (options page). An empty
// option hides its row; all three office fields empty = whole address block
// hidden. The email always resolves (option first, else contact@<domain>) —
// a contact page must stay reachable.
$office_title    = (string) v5_digital_get_field('office_title', 'option');
$office_address  = (string) v5_digital_get_field('office_address', 'option');
$office_city     = (string) v5_digital_get_field('office_city', 'option');
$email_icon      = v5_get_field_default('email_icon', '');
$email           = v5_digital_get_dynamic_email();

// Normalize icon fields (ACF image may return an array depending on config).
if (is_array($office_icon)) { $office_icon = isset($office_icon['url']) ? $office_icon['url'] : ''; }
if (is_array($email_icon))  { $email_icon  = isset($email_icon['url'])  ? $email_icon['url']  : ''; }
$guarantee_title = v5_get_field_default('guarantee_title', 'Garantie d\'Indépendance');
$guarantee_desc  = v5_get_field_default('guarantee_desc', 'Nous n\'acceptons pas de placements payants ni de classements sponsorisés. Les agences qui souhaitent être référencées passent par notre processus d\'évaluation standard et indépendant.');

// Success message shown by the form JS after a successful submit (editable).
// If the editor clears BOTH fields, fall back to the defaults so the green
// confirmation box is never empty.
$success_title = v5_get_field_default('success_title', 'Message envoyé !');
$success_desc  = v5_get_field_default('success_desc', 'Merci ! Notre équipe éditoriale vous contactera sous 24 heures.');
if ($success_title === '' && $success_desc === '') {
    $success_title = 'Message envoyé !';
    $success_desc  = 'Merci ! Notre équipe éditoriale vous contactera sous 24 heures.';
}

// Subject choices come from the plugin so the theme can never drift from the
// value→label map the submission handler stores (unknown keys silently become
// "Demande Générale" server-side). Static copy only as an unreachable safety
// net — the form itself is hidden when the plugin is missing.
$subject_choices = (class_exists('AMD_CF_Handler') && method_exists('AMD_CF_Handler', 'subject_labels'))
    ? AMD_CF_Handler::subject_labels()
    : array(
        'general' => 'Demande Générale',
        'list'    => 'Référencer mon Agence',
        'report'  => 'Signaler un Problème',
        'partner' => 'Opportunité de Partenariat',
    );
?>

<style>
    .btn-spring {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, color 0.15s ease-in-out;
        transform: translateY(0);
    }
    .btn-spring:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.15);
    }
    .btn-spring:active {
        transform: translateY(1px);
    }
    /* Loading state: swap the send icon for a spinner while submitting. */
    .amd-btn-spinner {
        display: none;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 50%;
        animation: amd-spin 0.6s linear infinite;
    }
    #contact-submit-btn:disabled { opacity: 0.85; cursor: wait; }
    #contact-submit-btn:disabled:hover { transform: translateY(0); box-shadow: none; }
    #contact-submit-btn:disabled .amd-btn-spinner { display: inline-block; }
    #contact-submit-btn:disabled .amd-send-icon { display: none; }
    @keyframes amd-spin { to { transform: rotate(360deg); } }
</style>

<!-- Main Content Section (v3_2 style layout and elements) -->
<section class="py-12 md:py-16 bg-slate-50/30">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- Left: Form block (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 p-6 md:p-8 shadow-sm">
                <?php if ($form_title) : ?>
                    <h2 class="text-[1.25rem] font-bold text-slate-900 mb-2 font-display"><?php echo esc_html($form_title); ?></h2>
                <?php endif; ?>
                <?php if ($form_desc) : ?>
                    <p class="text-[13.5px] text-slate-500 mb-6 font-sans"><?php echo esc_html($form_desc); ?></p>
                <?php endif; ?>

                <?php if (class_exists('AMD_CF_Form')) : // Submission needs the AMD Contact Forms plugin — see fallback below. ?>
                <form id="contact-form" class="space-y-4">
                    <!-- Honeypot: hidden from humans, bots tend to fill it. Submissions with this set are silently dropped. -->
                    <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden">
                        <label><?php echo esc_html(v5_t('Ne pas remplir')); ?><input type="text" name="amd_hp" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <?php // Tag submissions with which form + page they came from (for the AMD Contact Forms plugin dashboard).
                    // Uses the plugin's default registered form ("Formulaire de contact") so it shows up as that form.
                    // amd_legacy=1 keeps this hand-built form on the fixed-field handler path, even if the
                    // "default" form is later customized in the plugin's field builder. ?>
                    <input type="hidden" name="form_id" value="default">
                    <input type="hidden" name="form_name" value="Formulaire de contact">
                    <input type="hidden" name="amd_legacy" value="1">
                    <input type="hidden" name="page_url" value="<?php echo esc_url(get_permalink()); ?>">
                    <input type="hidden" name="page_title" value="<?php echo esc_attr(get_the_title()); ?>">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold"><?php echo esc_html(v5_t('Prénom')); ?></label>
                            <input type="text" id="firstName" name="first_name" required placeholder="<?php echo esc_attr(v5_t('Votre prénom')); ?>" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                        </div>
                        <div>
                            <label for="lastName" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold"><?php echo esc_html(v5_t('Nom')); ?></label>
                            <input type="text" id="lastName" name="last_name" required placeholder="<?php echo esc_attr(v5_t('Votre nom de famille')); ?>" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold"><?php echo esc_html(v5_t('Email')); ?></label>
                        <input type="email" id="email" name="email" required placeholder="<?php echo esc_attr(v5_t('adresse@entreprise.com')); ?>" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                    </div>

                    <div>
                        <label for="subject" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold"><?php echo esc_html(v5_t('Sujet')); ?></label>
                        <select id="subject" name="subject" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                            <?php foreach ($subject_choices as $subject_value => $subject_label) : ?>
                                <option value="<?php echo esc_attr($subject_value); ?>"><?php echo esc_html(v5_t($subject_label)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold"><?php echo esc_html(v5_t('Message')); ?></label>
                        <textarea id="message" name="message" required rows="5" placeholder="<?php echo esc_attr(v5_t('Comment pouvons-nous vous aider ?')); ?>" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all resize-none font-sans"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="contact-submit-btn" class="btn-spring bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-lg text-[13px] flex items-center gap-1.5 shadow-sm font-mono cursor-pointer">
                            <span class="amd-btn-spinner" aria-hidden="true"></span>
                            <span class="amd-btn-label"><?php echo esc_html(v5_t('Envoyer le Message')); ?></span>
                            <i data-lucide="send" class="w-3.5 h-3.5 amd-send-icon"></i>
                        </button>
                    </div>
                </form>

                <div id="contact-success-msg" class="hidden bg-emerald-50 border border-emerald-100 rounded-xl p-5 font-sans flex items-start" style="gap:1.15rem">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <?php if ($success_title !== '') : ?>
                            <p class="font-bold text-emerald-900 text-[14px] mb-0.5 font-display"><?php echo esc_html($success_title); ?></p>
                        <?php endif; ?>
                        <?php if ($success_desc !== '') : ?>
                            <p class="text-[13.5px] text-emerald-800"><?php echo esc_html($success_desc); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="contact-error-msg" class="hidden mt-3 bg-red-50 border border-red-100 text-red-700 rounded-lg p-4 text-[13.5px] font-sans"></div>
                <?php else : ?>
                <!-- AMD Contact Forms plugin inactive: a dead form would silently
                     lose the visitor's message, so offer a direct email instead. -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 font-sans">
                    <p class="text-[13.5px] text-slate-500 mb-4"><?php echo esc_html(v5_t('Le formulaire est momentanément indisponible. Écrivez-nous directement par email :')); ?></p>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="btn-spring bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-lg text-[13px] inline-flex items-center gap-1.5 shadow-sm font-mono">
                        <i data-lucide="mail" class="w-3.5 h-3.5" aria-hidden="true"></i>
                        <span><?php echo esc_html(v5_t('Envoyer un email')); ?></span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Right: Side block info (4 cols) -->
            <div class="lg:col-span-4 lg:pl-10 lg:border-l border-slate-200/80 space-y-8">
                <!-- Contact Info -->
                <?php
                $has_address  = !empty($office_title) || !empty($office_address) || !empty($office_city);
                $has_email    = !empty($email);
                if ($has_address || $has_email) :
                ?>
                <div>
                    <span class="block font-mono text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-4"><?php echo esc_html(v5_t('Informations de contact')); ?></span>
                    <div class="space-y-6 text-[13.5px] text-slate-500 leading-relaxed font-sans">
                        <?php if ($has_address) : ?>
                        <div class="flex items-start gap-3 <?php echo $has_email ? 'border-b border-slate-200/60 pb-5' : 'pb-2'; ?>">
                            <?php if (!empty($office_icon)) : ?>
                                <img src="<?php echo esc_url($office_icon); ?>" alt="" class="w-4 h-4 mt-0.5 flex-shrink-0 object-contain">
                            <?php else : ?>
                                <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 flex-shrink-0 text-slate-400" aria-hidden="true"></i>
                            <?php endif; ?>
                            <div>
                                <?php if (!empty($office_title)) : ?>
                                    <h4 class="font-semibold text-slate-800 text-[13px] mb-0.5 font-display"><?php echo esc_html($office_title); ?></h4>
                                <?php endif; ?>
                                <?php if (!empty($office_address)) : ?>
                                    <p class="text-slate-500"><?php echo esc_html($office_address); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($office_city)) : ?>
                                    <p class="text-slate-500"><?php echo esc_html($office_city); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($has_email) : ?>
                        <div class="flex items-start gap-3 pb-2">
                            <?php if (!empty($email_icon)) : ?>
                                <img src="<?php echo esc_url($email_icon); ?>" alt="" class="w-4 h-4 mt-0.5 flex-shrink-0 object-contain">
                            <?php else : ?>
                                <i data-lucide="mail" class="w-4 h-4 mt-0.5 flex-shrink-0 text-slate-400" aria-hidden="true"></i>
                            <?php endif; ?>
                            <div>
                                <h4 class="font-semibold text-slate-800 text-[13px] mb-0.5 font-display"><?php echo esc_html(v5_t('Email')); ?></h4>
                                <a href="mailto:<?php echo esc_attr($email); ?>" class="text-brand-600 font-medium break-all hover:text-brand-700 transition-colors"><?php echo esc_html($email); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Independence Guarantee block -->
                <?php if (!empty($guarantee_title) || !empty($guarantee_desc)) : ?>
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center gap-2.5 mb-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white border border-indigo-200/60 text-indigo-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <?php if (!empty($guarantee_title)) : ?>
                            <h3 class="font-bold text-slate-900 text-[13px] font-display uppercase tracking-wider"><?php echo esc_html($guarantee_title); ?></h3>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($guarantee_desc)) : ?>
                    <p class="text-[12.5px] text-slate-500 leading-relaxed font-sans">
                        <?php echo esc_html($guarantee_desc); ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php // Submission is handled by the "AMD Contact Forms" plugin (assets/contact-form.js). ?>
