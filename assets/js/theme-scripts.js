/* ----------------------------------------------------
 * Custom Theme Scripts and GSAP Animations for v5-digital
 * ---------------------------------------------------- */

document.addEventListener("DOMContentLoaded", () => {
    // 1. Initialise Motion / GSAP animations if possible
    initMotionSystem();

    // 2. Initialise Custom Select Dropdowns
    initCustomSelects();

    // 3. Initialise Search / Command Palette and Matchmaker bindings
    initModalBindings();
});

const motionState = {
    initialized: false,
    reduced: false,
    observer: null,
    mutationFrame: null,
    customSelectClickBound: false
};

function canUseMotion() {
    return typeof gsap !== "undefined" && !motionState.reduced;
}

function initMotionSystem() {
    motionState.reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!canUseMotion()) return;
    if (motionState.initialized) return;

    motionState.initialized = true;
    document.body.classList.add("motion-enhanced");
    gsap.defaults({ ease: "power3.out", duration: 0.45 });

    if (typeof ScrollTrigger !== "undefined") {
        gsap.registerPlugin(ScrollTrigger);
    }

    animatePageEntrance();
    initChallengeTitleAnimation();
    initApproachTitleAnimation();
    observeDynamicContent();
}

function animatePageEntrance() {
    const header = document.querySelector("header");
    const heroTitle = document.querySelector(".hero-title");
    const heroItems = document.querySelectorAll("main .section-label, main h1 + p, main .hero-title + p");
    const tl = gsap.timeline();

    if (header) {
        tl.from(header, { y: -12, autoAlpha: 0, duration: 0.28 });
    }

    if (heroTitle) {
        animateHeroTitle(heroTitle, tl);
    }

    if (heroItems.length) {
        tl.from(Array.from(heroItems), {
            y: 12,
            autoAlpha: 0,
            stagger: 0.025,
            duration: 0.32
        }, "-=0.1");
    }
}

function animateHeroTitle(heroTitle, timeline = null) {
    if (!canUseMotion() || !heroTitle) return;

    const focusWord = heroTitle.querySelector(".hero-focus-word");
    const locationWord = heroTitle.querySelector(".hero-location-word");
    const targets = [focusWord, locationWord].filter(Boolean);
    if (!targets.length) return;

    gsap.set(heroTitle, { autoAlpha: 1 });

    const run = timeline || gsap.timeline();
    run.fromTo(targets, {
        y: 12,
        autoAlpha: 0,
        scale: 0.97
    }, {
        y: 0,
        autoAlpha: 1,
        scale: 1,
        stagger: 0.07,
        duration: 0.3,
        ease: "back.out(1.4)"
    }, timeline ? "-=0.05" : 0);

    if (focusWord) {
        run.to(focusWord, {
            color: "#1d4ed8",
            textShadow: "0 8px 20px rgba(37, 99, 235, 0.15)",
            duration: 0.25,
            ease: "power2.out"
        }, timeline ? "-=0.1" : 0.15);
    }

    if (locationWord) {
        locationWord.style.setProperty("--hero-location-scale", 0);
        run.to(locationWord, {
            duration: 0.35,
            ease: "expo.out",
            onUpdate() {
                locationWord.style.setProperty("--hero-location-scale", this.progress());
            },
            onComplete() {
                locationWord.style.setProperty("--hero-location-scale", 1);
            }
        }, timeline ? "-=0.2" : 0.2);
    }
}

function initApproachTitleAnimation(root = document) {
    if (!canUseMotion() || typeof ScrollTrigger === "undefined") return;

    const title = root.querySelector(".approach-title-section");
    if (!title || title.dataset.motionReady === "true") return;

    const focusWord = title.querySelector(".approach-focus-word");
    const actionWord = title.querySelector(".approach-action-word");
    const targets = [focusWord, actionWord].filter(Boolean);
    if (!targets.length) return;

    title.dataset.motionReady = "true";
    if (actionWord) actionWord.style.setProperty("--approach-action-scale", 0);
    gsap.set(targets, { autoAlpha: 1, y: 0, scale: 1, transformOrigin: "center bottom" });

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: title,
            start: "top 90%",
            end: "+=280",
            scrub: 0.5,
            once: false
        }
    });

    tl.fromTo(targets, {
        y: 8,
        scale: 0.98
    }, {
        y: 0,
        scale: 1.03,
        stagger: 0.05,
        duration: 0.3,
        ease: "power2.out",
    }, 0).to(targets, {
        scale: 1,
        duration: 0.2,
        ease: "power2.out"
    }, 0.32);

    if (actionWord) {
        tl.to(actionWord, {
            "--approach-action-scale": 1,
            duration: 0.45,
            ease: "power2.out"
        }, 0.1);
    }

    if (focusWord) {
        tl.fromTo(focusWord, {
            color: "#0f172a"
        }, {
            color: "#2563eb",
            duration: 0.35,
            ease: "power2.out"
        }, 0.05);
    }
}

function initChallengeTitleAnimation(root = document) {
    if (!canUseMotion() || typeof ScrollTrigger === "undefined") return;

    const title = root.querySelector(".challenge-title-section");
    if (!title || title.dataset.motionReady === "true") return;

    const focusWord = title.querySelector(".challenge-focus-word");
    const timeWord = title.querySelector(".challenge-time-word");
    const targets = [focusWord, timeWord].filter(Boolean);
    if (!targets.length) return;

    title.dataset.motionReady = "true";
    if (timeWord) timeWord.style.setProperty("--challenge-time-scale", 0);
    gsap.set(targets, { autoAlpha: 1, y: 0, scale: 1, transformOrigin: "center bottom" });

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: title,
            start: "top 90%",
            end: "+=280",
            scrub: 0.5,
            once: false
        }
    });

    tl.fromTo(targets, {
        y: 8,
        scale: 0.98
    }, {
        y: 0,
        scale: 1.03,
        stagger: 0.05,
        duration: 0.3,
        ease: "power2.out",
    }, 0).to(targets, {
        scale: 1,
        duration: 0.2,
        ease: "power2.out"
    }, 0.32);

    if (timeWord) {
        tl.to(timeWord, {
            "--challenge-time-scale": 1,
            duration: 0.45,
            ease: "power2.out"
        }, 0.1);
    }

    if (focusWord) {
        tl.fromTo(focusWord, {
            color: "#0f172a"
        }, {
            color: "#dc2626",
            duration: 0.35,
            ease: "power2.out"
        }, 0.05);
    }
}

function observeDynamicContent() {
    if (motionState.observer) return;

    motionState.observer = new MutationObserver(mutations => {
        const hasAddedNodes = mutations.some(mutation => mutation.addedNodes.length > 0);
        if (!hasAddedNodes || motionState.mutationFrame) return;

        motionState.mutationFrame = requestAnimationFrame(() => {
            motionState.mutationFrame = null;
            if (canUseMotion()) {
                initChallengeTitleAnimation(document);
                initApproachTitleAnimation(document);
            }
        });
    });

    motionState.observer.observe(document.body, { childList: true, subtree: true });
}

/* Modals animations */
function animateModalOpen(modal) {
    if (!canUseMotion() || !modal) return;
    gsap.fromTo(modal, 
        { y: 18, scale: 0.985, autoAlpha: 0 },
        { y: 0, scale: 1, autoAlpha: 1, duration: 0.32, ease: "power3.out" }
    );
}

function animateModalClose(modal, onComplete) {
    if (!canUseMotion() || !modal) {
        onComplete();
        return;
    }

    gsap.to(modal, {
        y: 12,
        scale: 0.985,
        autoAlpha: 0,
        duration: 0.22,
        ease: "power2.in",
        onComplete
    });
}

/* Custom selects implementation */
function openCustomSelectMenu(menu, trigger) {
    const wrapper = menu.closest(".custom-select-wrapper");
    if (wrapper) wrapper.style.zIndex = "80";
    menu.classList.remove("hidden");
    trigger.classList.add("open");
    if (canUseMotion()) {
        gsap.fromTo(menu,
            { y: -8, autoAlpha: 0, scale: 0.98 },
            { y: 0, autoAlpha: 1, scale: 1, duration: 0.2, ease: "power2.out" }
        );
    }
}

function closeCustomSelectMenu(menu, trigger) {
    const complete = () => {
        const wrapper = menu.closest(".custom-select-wrapper");
        if (wrapper) wrapper.style.zIndex = "";
        menu.classList.add("hidden");
        if (trigger) trigger.classList.remove("open");
    };

    if (canUseMotion() && !menu.classList.contains("hidden")) {
        gsap.to(menu, { y: -4, autoAlpha: 0, duration: 0.16, ease: "power2.in", onComplete: complete });
    } else {
        complete();
    }
}

function initCustomSelects() {
    document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
        const select = wrapper.querySelector('select');
        if (select) {
            select.style.display = '';
            wrapper.replaceWith(select);
        }
    });

    const selectElements = document.querySelectorAll('select');
    selectElements.forEach(select => {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative custom-select-wrapper w-full';
        
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        select.style.display = 'none';

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = `custom-select-trigger flex items-center justify-between w-full bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg text-[13px] text-slate-700 shadow-sm focus:border-brand-600 focus:ring-2 focus:ring-brand-500/10 outline-none transition-all duration-200 cursor-pointer pl-3.5 pr-4 py-2`;
        
        const label = document.createElement('span');
        label.className = 'custom-select-label truncate';
        
        const currentOpt = select.options[select.selectedIndex] || select.options[0];
        label.textContent = currentOpt ? currentOpt.textContent : "";
        
        const arrow = document.createElement('span');
        arrow.className = 'chevron-icon flex items-center flex-shrink-0 ml-2';
        arrow.innerHTML = '<i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>';
        
        trigger.appendChild(label);
        trigger.appendChild(arrow);
        wrapper.appendChild(trigger);

        const menu = document.createElement('div');
        menu.className = 'custom-select-options hidden absolute left-0 right-0 z-50 mt-1.5 bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-xl shadow-[0_12px_32px_-8px_rgba(0,0,0,0.08)] max-h-60 overflow-y-auto p-1 font-sans';
        
        Array.from(select.options).forEach((opt, idx) => {
            const item = document.createElement('div');
            const isSelected = idx === select.selectedIndex;
            
            item.className = isSelected 
                ? 'custom-select-option px-3.5 py-2 text-[13px] text-brand-700 bg-brand-50/50 font-semibold cursor-pointer flex items-center justify-between transition-all rounded-md mx-1 my-0.5'
                : 'custom-select-option px-3.5 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 cursor-pointer flex items-center justify-between transition-all rounded-md mx-1 my-0.5';
            item.dataset.value = opt.value;
            item.dataset.index = idx;
            
            const itemText = document.createElement('span');
            itemText.textContent = opt.textContent;
            
            const checkIcon = document.createElement('span');
            checkIcon.className = `text-brand-600 flex items-center flex-shrink-0 ${isSelected ? 'block' : 'hidden'}`;
            checkIcon.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i>';
            
            item.appendChild(itemText);
            item.appendChild(checkIcon);
            menu.appendChild(item);

            item.onclick = (e) => {
                e.stopPropagation();
                select.selectedIndex = idx;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                closeCustomSelectMenu(menu, trigger);
            };
        });
        
        wrapper.appendChild(menu);

        trigger.onclick = (e) => {
            e.stopPropagation();
            
            document.querySelectorAll('.custom-select-options').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    closeCustomSelectMenu(otherMenu, otherMenu.previousElementSibling);
                }
            });
            document.querySelectorAll('.custom-select-trigger').forEach(otherTrigger => {
                if (otherTrigger !== trigger) {
                    otherTrigger.classList.remove('open');
                }
            });

            const isOpen = !menu.classList.contains('hidden');
            if (isOpen) {
                closeCustomSelectMenu(menu, trigger);
            } else {
                openCustomSelectMenu(menu, trigger);
            }
        };

        select.addEventListener('change', () => {
            const activeOpt = select.options[select.selectedIndex];
            if (activeOpt) {
                label.textContent = activeOpt.textContent;
                
                menu.querySelectorAll('.custom-select-option').forEach(item => {
                    const idxStr = item.dataset.index;
                    const isSel = idxStr === String(select.selectedIndex);
                    
                    item.className = isSel
                        ? 'custom-select-option px-3.5 py-2 text-[13px] text-brand-700 bg-brand-50/50 font-semibold cursor-pointer flex items-center justify-between transition-all rounded-md mx-1 my-0.5'
                        : 'custom-select-option px-3.5 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 cursor-pointer flex items-center justify-between transition-all rounded-md mx-1 my-0.5';
                    
                    const check = item.querySelector('.text-brand-600');
                    if (check) {
                        check.className = `text-brand-600 flex items-center flex-shrink-0 ${isSel ? 'block' : 'hidden'}`;
                    }
                });
            }
        });
    });

    if (!motionState.customSelectClickBound) {
        motionState.customSelectClickBound = true;
        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-select-options').forEach(menu => {
                closeCustomSelectMenu(menu, menu.previousElementSibling);
            });
        });
    }

    if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }
}

/* Modals Triggering and Command Palette bindings */
function initModalBindings() {
    // ⌘K/Ctrl+K Search Palette Trigger
    window.addEventListener("keydown", (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === "k") {
            e.preventDefault();
            openSearchPalette();
        }
    });

    const searchInput = document.getElementById("search-input");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => runPaletteSearch(e.target.value));
    }
}

function openSearchPalette() {
    const modal = document.getElementById("search-modal");
    if (modal) {
        const input = document.getElementById("search-input");
        if (input) input.value = "";
        runPaletteSearch("");
        modal.showModal();
        animateModalOpen(modal);
    }
}

function closeSearchPalette() {
    const modal = document.getElementById("search-modal");
    if (modal && modal.open) {
        animateModalClose(modal, () => {
            modal.close();
            if (typeof gsap !== "undefined") {
                gsap.set(modal, { clearProps: "all" });
            }
        });
    }
}

// Global search index built dynamically based on WordPress structure
const SEARCH_INDEX = [
    { title: "RMD", type: "agence", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/?id=rmd" },
    { title: "Pixagram", type: "agence", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/?id=pixagram" },
    { title: "MediaBoost", type: "agence", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/?id=mediaboost" },
    { title: "DigitalWave", type: "agence", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/?id=digitalwave" },
    { title: "NexaMedia", type: "agence", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/?id=nexamedia" },
    { title: "Sahara Digital", type: "agence", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/?id=saharadigital" },
    { title: "accueil", type: "page", action: () => window.location.href = window.wpThemeSettings.homeUrl },
    { title: "annuaire des agences", type: "page", action: () => window.location.href = window.wpThemeSettings.homeUrl + "annuaire/" },
    { title: "méthodologie & audits", type: "page", action: () => window.location.href = window.wpThemeSettings.homeUrl + "methodologie/" },
    { title: "contactez-nous", type: "page", action: () => window.location.href = window.wpThemeSettings.homeUrl + "contact/" },
    { title: "blog & actualités", type: "page", action: () => window.location.href = window.wpThemeSettings.homeUrl + "blog/" }
];

function runPaletteSearch(val) {
    const list = document.getElementById("search-results");
    if (!list) return;
    list.innerHTML = "";
    const query = val.toLowerCase().trim();
    
    const filtered = SEARCH_INDEX.filter(item => item.title.toLowerCase().includes(query));
    
    if (filtered.length === 0) {
        list.innerHTML = `<li class="p-4 text-slate-400 text-center">aucun résultat trouvé</li>`;
        return;
    }
    
    filtered.forEach(item => {
        const li = document.createElement("li");
        li.className = "flex justify-between items-center p-3 hover:bg-slate-50 cursor-pointer rounded-lg transition-colors";
        li.innerHTML = `
            <span class="font-semibold text-slate-800">${item.title}</span>
            <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded font-mono">${item.type}</span>
        `;
        li.onclick = () => {
            closeSearchPalette();
            item.action();
        };
        list.appendChild(li);
    });
}

/* Matchmaker Lead Wizard steps */
let wizardStep = 1;
const wizardData = {
    service: "",
    budget: ""
};

function openMatchmaker() {
    const modal = document.getElementById("matchmaker-modal");
    if (modal) {
        resetWizard();
        modal.showModal();
        animateModalOpen(modal);
    }
}

function closeMatchmaker() {
    const modal = document.getElementById("matchmaker-modal");
    if (modal && modal.open) {
        animateModalClose(modal, () => {
            modal.close();
            if (typeof gsap !== "undefined") {
                gsap.set(modal, { clearProps: "all" });
            }
        });
    }
}

function resetWizard() {
    wizardStep = 1;
    wizardData.service = "";
    wizardData.budget = "";
    
    document.querySelectorAll(".matching-wizard__step").forEach(step => {
        step.classList.remove("active");
        if (step.dataset.step == "1") step.classList.add("active");
    });
    
    document.querySelectorAll(".step-option-btn").forEach(btn => btn.classList.remove("active"));
    
    const nextBtn = document.querySelector(".next-step-btn");
    if (nextBtn) nextBtn.disabled = true;
    
    const submitBtn = document.querySelector(".submit-wizard-btn");
    if (submitBtn) submitBtn.disabled = true;
    
    updateWizardHeader("Trouvez votre agence idéale", "4 questions · 60 secondes");
}

function updateWizardHeader(title, subtitle) {
    const tEl = document.getElementById("mm-step-title");
    const sEl = document.getElementById("mm-step-subtitle");
    if (tEl) tEl.textContent = title;
    if (sEl) sEl.textContent = subtitle;
}

// Bind options selection clicks inside wizard
document.addEventListener("click", (e) => {
    const optionBtn = e.target.closest(".step-option-btn");
    if (!optionBtn) return;
    
    const stepContainer = optionBtn.closest(".matching-wizard__step");
    if (!stepContainer) return;
    
    // De-activate siblings
    stepContainer.querySelectorAll(".step-option-btn").forEach(btn => btn.classList.remove("active"));
    optionBtn.classList.add("active");
    
    const val = optionBtn.dataset.value;
    const stepNum = stepContainer.dataset.step;
    
    if (stepNum == "1") {
        wizardData.service = val;
        const nextBtn = stepContainer.querySelector(".next-step-btn");
        if (nextBtn) nextBtn.disabled = false;
    } else if (stepNum == "2") {
        wizardData.budget = val;
        const submitBtn = stepContainer.querySelector(".submit-wizard-btn");
        if (submitBtn) submitBtn.disabled = false;
    }
});

// Bind next step
document.addEventListener("click", (e) => {
    const nextBtn = e.target.closest(".next-step-btn");
    if (!nextBtn) return;
    
    const currentStep = document.querySelector(".matching-wizard__step[data-step='1']");
    const nextStep = document.querySelector(".matching-wizard__step[data-step='2']");
    
    if (currentStep && nextStep) {
        currentStep.classList.remove("active");
        nextStep.classList.add("active");
        wizardStep = 2;
        updateWizardHeader("Quel est votre budget ?", "Étape 2 sur 2");
    }
});

// Bind submit wizard
document.addEventListener("click", (e) => {
    const submitBtn = e.target.closest(".submit-wizard-btn");
    if (!submitBtn) return;
    
    const currentStep = document.querySelector(".matching-wizard__step[data-step='2']");
    const successStep = document.querySelector(".matching-wizard__step[data-step='success']");
    
    if (currentStep && successStep) {
        currentStep.classList.remove("active");
        successStep.classList.add("active");
        wizardStep = "success";
        updateWizardHeader("Mise en relation réussie !", "Terminé");
        
        // Trigger star burst on the modal center
        const rect = successStep.getBoundingClientRect();
        triggerStarBurst(rect.left + rect.width / 2, rect.top + rect.height / 2);
    }
});

// Star burst canvas feedback
function triggerStarBurst(x, y) {
    const container = document.createElement("div");
    container.className = "star-burst-container";
    container.style.left = `${x - 12 + window.scrollX}px`;
    container.style.top = `${y - 12 + window.scrollY}px`;

    const star = document.createElement("div");
    star.className = "star-burst";

    container.appendChild(star);
    document.body.appendChild(container);

    setTimeout(() => {
        container.remove();
    }, 450);
}

// Redirect shortcut clicks to annuaire page with filters
function shortcutSearch(type, value) {
    window.location.href = window.wpThemeSettings.homeUrl + 'annuaire/?' + type + '=' + encodeURIComponent(value);
}

// Mobile dropdown toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Accordion faq toggle
function toggleFaq(button) {
    const content = button.nextElementSibling;
    const arrow = button.querySelector('[data-lucide="chevron-down"]');
    if (content) {
        content.classList.toggle('open');
        if (arrow) {
            if (content.classList.contains('open')) {
                arrow.style.transform = 'rotate(180deg)';
            } else {
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    }
}

// Compatibility redirect trigger
function triggerHomeSearch() {
    const service = document.getElementById('home-filter-service')?.value || 'all';
    const city = document.getElementById('home-filter-city')?.value || 'all';
    const rating = document.getElementById('home-filter-rating')?.value || 'any';
    
    const params = new URLSearchParams();
    if (service !== 'all') params.set('service', service);
    if (city !== 'all') params.set('city', city);
    if (rating !== 'any') params.set('rating', rating);
    
    window.location.href = window.wpThemeSettings.homeUrl + 'annuaire/' + (params.toString() ? '?' + params.toString() : '');
}
