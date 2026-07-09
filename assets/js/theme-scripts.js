/* ----------------------------------------------------
 * Custom Theme Scripts and GSAP Animations for agence-marketing-digital
 * ---------------------------------------------------- */

document.addEventListener("DOMContentLoaded", () => {
    // 1. Initialise Motion / GSAP animations if possible
    initMotionSystem();

    // 2. Initialise Custom Select Dropdowns
    initCustomSelects();

    // 3. Enhance logo marquees
    initLogoMarquees();

    // 4. Initialise Search / Command Palette and Matchmaker bindings
    initModalBindings();

    // 5. Exit-intent newsletter popup
    initExitIntentModal();
    initExitIntent();
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

function initLogoMarquees() {
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (reduceMotion) return;

    document.querySelectorAll("[data-logo-marquee]").forEach((marquee) => {
        const track = marquee.querySelector(".v5-logos-track");
        if (!track) return;

        const state = {
            offset: 0,
            halfWidth: 0,
            speed: 0,
            lastTime: 0,
            hovered: false,
            dragging: false,
            dragStartX: 0,
            dragStartOffset: 0
        };

        const wrapOffset = (offset) => {
            if (!state.halfWidth) return 0;
            let wrapped = offset % state.halfWidth;
            if (wrapped > 0) wrapped -= state.halfWidth;
            return wrapped;
        };

        const applyOffset = () => {
            track.style.setProperty("--v5-logos-offset", `${state.offset}px`);
        };

        const measure = () => {
            const duration = Math.max(1, parseFloat(marquee.dataset.duration || "35"));
            state.halfWidth = track.scrollWidth / 2;
            state.speed = state.halfWidth / duration;
            state.offset = wrapOffset(state.offset);
            applyOffset();
        };

        const tick = (time) => {
            if (!state.lastTime) state.lastTime = time;
            const delta = (time - state.lastTime) / 1000;
            state.lastTime = time;

            if (!state.hovered && !state.dragging && state.halfWidth) {
                state.offset = wrapOffset(state.offset - state.speed * delta);
                applyOffset();
            }

            window.requestAnimationFrame(tick);
        };

        const endDrag = (event) => {
            if (!state.dragging) return;

            state.dragging = false;
            marquee.classList.remove("is-dragging");

            if (event.pointerId !== undefined && marquee.hasPointerCapture(event.pointerId)) {
                marquee.releasePointerCapture(event.pointerId);
            }

            if (event.pointerType !== "mouse") {
                state.hovered = false;
            }
        };

        marquee.classList.add("is-js-marquee");
        measure();
        window.requestAnimationFrame(tick);

        marquee.addEventListener("pointerenter", () => {
            state.hovered = true;
        });

        marquee.addEventListener("pointerleave", () => {
            state.hovered = false;
        });

        marquee.addEventListener("pointerdown", (event) => {
            if (event.button !== undefined && event.button !== 0) return;

            state.hovered = true;
            state.dragging = true;
            state.dragStartX = event.clientX;
            state.dragStartOffset = state.offset;
            marquee.classList.add("is-dragging");
            marquee.setPointerCapture(event.pointerId);
            event.preventDefault();
        });

        marquee.addEventListener("pointermove", (event) => {
            if (!state.dragging) return;

            const delta = event.clientX - state.dragStartX;
            state.offset = wrapOffset(state.dragStartOffset + delta);
            applyOffset();
        });

        marquee.addEventListener("pointerup", endDrag);
        marquee.addEventListener("pointercancel", endDrag);

        marquee.addEventListener("wheel", (event) => {
            if (!state.hovered || !state.halfWidth) return;

            event.preventDefault();
            const delta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
            state.offset = wrapOffset(state.offset - delta);
            applyOffset();
        }, { passive: false });

        window.addEventListener("resize", measure);
        track.querySelectorAll("img").forEach((img) => {
            if (!img.complete) {
                img.addEventListener("load", measure, { once: true });
            }
        });
    });
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

    // Hero entrance runs immediately (before first paint) to avoid any flash.
    animatePageEntrance();

    // The scroll-triggered setups create ScrollTriggers, which read element
    // geometry (offsetHeight / getBoundingClientRect). Running that during the
    // initial load forces a synchronous layout (Lighthouse "forced reflow").
    // Defer them until the browser is idle — they only fire on scroll anyway,
    // and the affected sections are fully visible meanwhile.
    const setupScrollAnimations = () => {
        initChallengeTitleAnimation();
        initApproachTitleAnimation();
        observeDynamicContent();
    };
    if ("requestIdleCallback" in window) {
        window.requestIdleCallback(setupScrollAnimations, { timeout: 600 });
    } else {
        window.setTimeout(setupScrollAnimations, 200);
    }
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

/* Exit-intent newsletter popup */
// Set when the popup interrupts an actual navigation (the "retour aux
// articles" link) rather than a passive signal (mouseout/scroll) — whatever
// closes the modal (X, backdrop, Escape, or auto-close after submit) then
// completes that navigation, so the popup never traps a visitor who was
// already on their way somewhere.
let exitIntentPendingRedirect = null;

function openExitIntent() {
    const modal = document.getElementById("exit-intent-modal");
    if (modal && !modal.open) {
        modal.showModal();
        // Animate the inner wrapper, NOT the <dialog> itself — GSAP's tween
        // sets an inline `transform` on its target, and applying that
        // directly to the dialog broke its native top-layer centering (it
        // would render far off-screen, offset by roughly the page's current
        // scroll position). Reproduced and confirmed in isolation; the
        // dialog's own position is left completely untouched.
        animateModalOpen(document.getElementById("exit-intent-inner"));
    }
}

function closeExitIntent() {
    const modal = document.getElementById("exit-intent-modal");
    const inner = document.getElementById("exit-intent-inner");
    if (modal && modal.open) {
        animateModalClose(inner, () => {
            modal.close();
            if (typeof gsap !== "undefined" && inner) {
                gsap.set(inner, { clearProps: "all" });
            }
            if (exitIntentPendingRedirect) {
                const url = exitIntentPendingRedirect;
                exitIntentPendingRedirect = null;
                window.location.href = url;
            }
        });
    }
}

/** Wires the modal itself: backdrop-click dismissal, Escape dismissal, and
 * the (cosmetic — matches the footer newsletter band, which is also
 * front-end only with no backend storage yet) submit handling. Runs once at
 * init regardless of whether exit-intent detection is enabled, so the modal
 * still works if something else ever opens it. */
function initExitIntentModal() {
    const modal = document.getElementById("exit-intent-modal");
    if (!modal) return;

    modal.addEventListener("click", (e) => {
        if (e.target === modal) closeExitIntent();
    });

    // Native <dialog> closes on Escape by itself (a "cancel" event, then an
    // implicit .close()) without going through closeExitIntent() — which
    // would skip the closing animation and, worse, leave
    // exitIntentPendingRedirect unresolved when the popup interrupted the
    // "retour aux articles" link, silently stranding the visitor. Intercept
    // it and route through the same close path instead.
    modal.addEventListener("cancel", (e) => {
        e.preventDefault();
        closeExitIntent();
    });

    const form = document.getElementById("exit-intent-form");
    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        try {
            localStorage.setItem("v5_newsletter_subscribed", "1");
        } catch (err) {
            // Storage unavailable (private mode, quota) — the popup will just
            // offer itself again next session, which is harmless.
        }

        const formState = document.getElementById("ei-form-state");
        const successState = document.getElementById("ei-success-state");
        if (formState) formState.classList.add("hidden");
        if (successState) {
            successState.classList.remove("hidden");
            successState.classList.add("flex");
        }
        if (typeof lucide !== "undefined" && typeof lucide.createIcons === "function") {
            lucide.createIcons();
        }

        window.setTimeout(closeExitIntent, 2200);
    });
}

/**
 * Fires the popup once per session on a strong "about to leave" signal:
 * desktop cursor exiting toward the browser chrome, or a rapid scroll-up
 * near the top of the page on touch devices (no mouse to read exit intent
 * from). Skipped entirely if the server-side gate
 * (window.wpThemeSettings.exitIntentEnabled — v5_digital_exit_intent_enabled())
 * is off, if it already fired this session, or if this visitor already
 * subscribed on a previous visit.
 */
function initExitIntent() {
    const modal = document.getElementById("exit-intent-modal");
    if (!modal) return;
    if (!window.wpThemeSettings || window.wpThemeSettings.exitIntentEnabled === false) return;

    try {
        if (sessionStorage.getItem("v5_exit_intent_shown") === "1") return;
    } catch (err) {
        // Storage unavailable — proceed; worst case it can fire again later.
    }
    try {
        if (localStorage.getItem("v5_newsletter_subscribed") === "1") return;
    } catch (err) {
        // Storage unavailable — proceed.
    }

    let fired = false;
    const trigger = (redirectUrl) => {
        if (fired) return;
        fired = true;
        try {
            sessionStorage.setItem("v5_exit_intent_shown", "1");
        } catch (err) {
            // Storage unavailable — the once-per-session cap just won't hold.
        }
        document.removeEventListener("mouseout", onMouseOut);
        window.removeEventListener("scroll", onScroll);
        if (backLink) backLink.removeEventListener("click", onBackLinkClick);

        if (redirectUrl) {
            exitIntentPendingRedirect = redirectUrl;
        }

        // A short delay, not an immediate open. The mobile trigger fires
        // mid-scroll-gesture, exactly when the browser's address bar may be
        // showing/hiding and transiently resizing the viewport; opening
        // before that settles centers the dialog against a viewport size
        // that's about to change, and it ends up looking shifted up once the
        // chrome finishes animating. Harmless on desktop too — an unnoticeable
        // delay before a popup the visitor didn't ask for.
        window.setTimeout(openExitIntent, 220);
    };

    // Desktop: cursor leaves the viewport upward with no related target —
    // i.e. toward the tab bar/address bar, not onto another element.
    const onMouseOut = (e) => {
        if (e.clientY <= 0 && !e.relatedTarget) trigger();
    };
    document.addEventListener("mouseout", onMouseOut);

    // Touch devices have no cursor to read exit intent from; a fast upward
    // scroll back near the top of the page is the closest equivalent signal.
    let lastScrollY = window.scrollY;
    let lastScrollTime = performance.now();
    const onScroll = () => {
        const now = performance.now();
        const deltaUp = lastScrollY - window.scrollY;
        const deltaTime = now - lastScrollTime;

        if (deltaUp > 80 && deltaTime < 300 && window.scrollY < window.innerHeight) {
            trigger();
        }

        lastScrollY = window.scrollY;
        lastScrollTime = now;
    };
    window.addEventListener("scroll", onScroll, { passive: true });

    // Explicit signal: the visitor clicked away from the article. Stronger
    // than mouseout/scroll (it's an actual navigation, not an inference), so
    // it interrupts the click and completes it once the popup is dismissed
    // (see exitIntentPendingRedirect / closeExitIntent).
    const backLink = document.getElementById("v5-back-to-articles");
    const onBackLinkClick = (e) => {
        e.preventDefault();
        trigger(backLink.href);
    };
    if (backLink) backLink.addEventListener("click", onBackLinkClick);
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
