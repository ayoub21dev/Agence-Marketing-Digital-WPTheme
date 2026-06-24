<?php
/**
 * Contact Form Section Layout
 */
$form_title      = v5_get_field_default('form_title', 'Envoyer un Message');
$form_desc       = v5_get_field_default('form_desc', 'Utilisez ce formulaire pour les demandes de référencement, les corrections, les questions des acheteurs ou les notes de partenariat.');
$office_title    = v5_get_field_default('office_title', 'Siège Social');
$office_address  = v5_get_field_default('office_address', '8 rue de la Paix, 75002 Paris, France');
$office_city     = v5_get_field_default('office_city', 'Casablanca, Maroc');
$email           = v5_get_field_default('email', v5_digital_get_dynamic_email());
$guarantee_title = v5_get_field_default('guarantee_title', 'Garantie d\'Indépendance');
$guarantee_desc  = v5_get_field_default('guarantee_desc', 'Nous n\'acceptons pas de placements payants ni de classements sponsorisés. Les agences qui souhaitent être référencées passent par notre processus d\'évaluation standard et indépendant.');
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
                
                <form id="contact-form" onsubmit="handleContactSubmit(event)" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold">Prénom</label>
                            <input type="text" id="firstName" required placeholder="Votre prénom" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                        </div>
                        <div>
                            <label for="lastName" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold">Nom</label>
                            <input type="text" id="lastName" required placeholder="Votre nom de famille" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold">Email</label>
                        <input type="email" id="email" required placeholder="adresse@entreprise.com" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold">Sujet</label>
                        <select id="subject" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all font-sans">
                            <option value="general">Demande Générale</option>
                            <option value="list">Référencer mon Agence</option>
                            <option value="report">Signaler un Problème</option>
                            <option value="partner">Opportunité de Partenariat</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-[11px] font-semibold text-slate-500 mb-1.5 uppercase tracking-wider font-mono font-bold">Message</label>
                        <textarea id="message" required rows="5" placeholder="Comment pouvons-nous vous aider ?" class="w-full bg-slate-50 hover:bg-slate-100/50 border border-slate-200 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-500/10 rounded-lg px-3.5 py-2.5 text-[13px] outline-none transition-all resize-none font-sans"></textarea>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" id="contact-submit-btn" class="btn-spring bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-lg text-[13px] flex items-center gap-1.5 shadow-sm font-mono cursor-pointer">
                            <span>Envoyer le Message</span>
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </form>

                <div id="contact-success-msg" class="hidden bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-lg p-4 text-[13.5px] font-sans">
                    Merci ! Votre message a été envoyé avec succès. Notre équipe éditoriale vous contactera sous 24 heures.
                </div>
            </div>
            
            <!-- Right: Side block info (4 cols) -->
            <div class="lg:col-span-4 lg:pl-10 lg:border-l border-slate-200/80 space-y-8">
                <!-- Contact Info -->
                <?php 
                $has_address = !empty($office_title) || !empty($office_address) || !empty($office_city);
                $has_email   = !empty($email);
                if ($has_address || $has_email) : 
                ?>
                <div>
                    <span class="block font-mono text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-4">Informations de contact</span>
                    <div class="space-y-6 text-[13.5px] text-slate-500 leading-relaxed font-sans">
                        <?php if ($has_address) : ?>
                        <div class="flex items-start gap-3 <?php echo $has_email ? 'border-b border-slate-200/60 pb-5' : 'pb-2'; ?>">
                            <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0"></i>
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
                            <i data-lucide="mail" class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <h4 class="font-semibold text-slate-800 text-[13px] mb-0.5 font-display">Email</h4>
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

<script>
    // Handle submission of the contact form
    function handleContactSubmit(event) {
        event.preventDefault();
        
        // Hide the form fields
        document.getElementById('contact-form').classList.add('hidden');
        // Show success alert message
        document.getElementById('contact-success-msg').classList.remove('hidden');
    }
</script>
