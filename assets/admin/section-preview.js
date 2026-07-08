/**
 * Page builder — live section preview.
 *
 * Adds an eye button to every entry of ACF's flexible-content "Add Row" popup.
 * Clicking it opens a modal that iframes the section, rendered standalone by
 * the `v5_section_preview` AJAX endpoint, so the editor sees the real thing
 * before inserting it.
 *
 * The section is rendered at desktop width (1280px) and scaled down to fit the
 * modal, otherwise every layout would render at its mobile breakpoint.
 *
 * Config comes from v5SectionPreview (localized in functions.php).
 */
(function () {
    'use strict';

    var cfg = window.v5SectionPreview || {};
    var layouts = cfg.layouts || {};
    var i18n = cfg.i18n || {};

    var RENDER_WIDTH = 1280; // the width layouts are designed around

    var modal = null;
    var frame = null;
    var stage = null;
    var currentAnchor = null;
    var lastFocused = null;
    var lastFieldKey = '';

    // ── Insert path ──────────────────────────────────────────────────────────
    // Remember which flexible-content field opened the popup, so we can still
    // insert if ACF tore the popup (and its anchors) down.
    document.addEventListener('click', function (e) {
        var addBtn = e.target.closest('[data-name="add-layout"], .acf-fc-add');
        if (!addBtn) return;
        var field = addBtn.closest('.acf-field-flexible-content');
        lastFieldKey = field ? field.getAttribute('data-key') || '' : '';
    }, true);

    /**
     * @param {string} layoutName
     * @param {HTMLElement|null} anchor  Passed explicitly: closeModal() clears
     *   `currentAnchor`, so the caller must capture it before closing.
     */
    function insertLayout(layoutName, anchor) {
        // Preferred: replay the click on ACF's own anchor — it handles insert
        // position and the min/max row rules.
        if (anchor && document.body.contains(anchor)) {
            anchor.click();
            return;
        }
        // Fallback: drive the field through ACF's JS API.
        if (window.acf && lastFieldKey && typeof window.acf.getField === 'function') {
            var field = window.acf.getField(lastFieldKey);
            if (field && typeof field.add === 'function') field.add(layoutName);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    function esc(s) {
        var d = document.createElement('div');
        d.textContent = s == null ? '' : s;
        return d.innerHTML;
    }

    function previewUrl(layoutName) {
        var params = new URLSearchParams({
            action: 'v5_section_preview',
            layout: layoutName,
            _wpnonce: cfg.nonce || ''
        });
        if (cfg.postId) params.set('post_id', cfg.postId);
        return (cfg.ajaxUrl || '') + '?' + params.toString();
    }

    // ── Modal ────────────────────────────────────────────────────────────────
    function buildModal() {
        if (modal) return modal;

        modal = document.createElement('div');
        modal.className = 'v5-sp-modal';
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML =
            '<div class="v5-sp-backdrop" data-close></div>' +
            '<div class="v5-sp-dialog" role="dialog" aria-modal="true" aria-labelledby="v5-sp-title">' +
            '  <header class="v5-sp-head">' +
            '    <h2 class="v5-sp-title" id="v5-sp-title"></h2>' +
            '    <button type="button" class="v5-sp-close" data-close aria-label="' + esc(i18n.close || 'Fermer') + '">&times;</button>' +
            '  </header>' +
            '  <div class="v5-sp-stage">' +
            '    <div class="v5-sp-status"></div>' +
            // .v5-sp-clip hides the overflow: transform:scale() shrinks the render
            // visually but the scaler still occupies 1280px of layout width.
            '    <div class="v5-sp-clip">' +
            '      <div class="v5-sp-scaler"><iframe title="' + esc(i18n.previewTitle || 'Aperçu') + '" scrolling="no"></iframe></div>' +
            '    </div>' +
            '  </div>' +
            '  <div class="v5-sp-body">' +
            '    <p class="v5-sp-desc"></p>' +
            '    <p class="v5-sp-note">' + esc(i18n.demoNotice || '') + '</p>' +
            '  </div>' +
            '  <footer class="v5-sp-foot">' +
            '    <button type="button" class="button" data-close>' + esc(i18n.close || 'Fermer') + '</button>' +
            '    <button type="button" class="button button-primary" data-insert>' + esc(i18n.insert || 'Insérer') + '</button>' +
            '  </footer>' +
            '</div>';

        // Clicks inside the modal must never reach ACF's document-level
        // "close the popup" handler — that destroys the anchor we insert with.
        modal.addEventListener('click', function (e) {
            e.stopPropagation();
            if (e.target.closest('[data-close]')) {
                closeModal();
            } else if (e.target.closest('[data-insert]')) {
                var name = modal.getAttribute('data-layout');
                var anchor = currentAnchor; // captured: closeModal() clears it
                closeModal({ keepFocus: true });
                insertLayout(name, anchor);
            }
        });

        document.body.appendChild(modal);
        frame = modal.querySelector('iframe');
        stage = modal.querySelector('.v5-sp-stage');

        frame.addEventListener('load', onFrameLoad);
        frame.addEventListener('error', function () { setStatus(i18n.error || 'Erreur', true); });

        window.addEventListener('resize', fitFrame);
        return modal;
    }

    function setStatus(text, isError) {
        var el = modal.querySelector('.v5-sp-status');
        el.textContent = text || '';
        el.classList.toggle('is-error', !!isError);
        el.style.display = text ? '' : 'none';
        modal.querySelector('.v5-sp-clip').style.visibility = text ? 'hidden' : 'visible';
    }

    /** Scale the 1280px-wide render down to the modal's width. */
    function fitFrame() {
        if (!frame || !stage || !modal.classList.contains('is-open')) return;

        var clip = modal.querySelector('.v5-sp-clip');
        var scaler = modal.querySelector('.v5-sp-scaler');

        // Measure against the clip (stage may show a vertical scrollbar).
        var available = clip.clientWidth || stage.clientWidth;
        var scale = Math.min(1, available / RENDER_WIDTH);

        scaler.style.transform = 'scale(' + scale + ')';
        scaler.style.width = RENDER_WIDTH + 'px';

        var h = parseInt(frame.dataset.contentHeight || '0', 10);
        if (h > 0) {
            frame.style.height = h + 'px';
            scaler.style.height = h + 'px';
            // The clip carries the *scaled* height so no phantom space remains.
            clip.style.height = Math.ceil(h * scale) + 'px';
        }
    }

    function onFrameLoad() {
        var doc;
        try {
            doc = frame.contentDocument; // same-origin
        } catch (err) {
            doc = null;
        }
        if (!doc || !doc.body) {
            setStatus(i18n.error || 'Erreur', true);
            return;
        }

        var measure = function () {
            var h = Math.max(
                doc.body.scrollHeight,
                doc.documentElement ? doc.documentElement.scrollHeight : 0
            );
            frame.dataset.contentHeight = String(h || 400);
            setStatus('');
            fitFrame();
        };

        measure();
        // Webfonts/images settle after load; re-measure once they do.
        if (frame.contentWindow) {
            frame.contentWindow.addEventListener('load', measure);
        }
        setTimeout(measure, 350);
        setTimeout(measure, 1200);
    }

    function openModal(anchor) {
        var name = anchor.getAttribute('data-layout');
        var info = layouts[name];
        if (!info) return;

        currentAnchor = anchor;
        lastFocused = document.activeElement;
        buildModal();

        modal.setAttribute('data-layout', name);
        modal.querySelector('.v5-sp-title').textContent = info.label || name;
        modal.querySelector('.v5-sp-desc').textContent = info.desc || '';

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        // Visually hide ACF's popup (it renders above everything). `visibility`
        // rather than `display`, so its anchors stay clickable for insertion.
        document.body.classList.add('v5-sp-modal-open');

        frame.dataset.contentHeight = '';
        frame.style.height = '400px';
        setStatus(i18n.loading || 'Chargement…');
        frame.src = previewUrl(name);

        modal.querySelector('.v5-sp-close').focus();
    }

    function closeModal(opts) {
        if (!modal || !modal.classList.contains('is-open')) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('v5-sp-modal-open');
        frame.src = 'about:blank'; // stop work, free memory

        if (!opts || !opts.keepFocus) {
            if (lastFocused && document.body.contains(lastFocused)) lastFocused.focus();
        }
        currentAnchor = null;
    }

    // ── Decorate the ACF popup entries ───────────────────────────────────────
    function decorate(popup) {
        popup.querySelectorAll('a[data-layout]').forEach(function (anchor) {
            if (anchor.dataset.v5Preview) return;
            anchor.dataset.v5Preview = '1';

            var name = anchor.getAttribute('data-layout');
            if (!layouts[name]) return;

            // Wrap the label so long names ellipsis instead of pushing the eye
            // button out of the popup.
            var label = document.createElement('span');
            label.className = 'v5-sp-label';
            while (anchor.firstChild) {
                label.appendChild(anchor.firstChild);
            }
            anchor.appendChild(label);
            if (!anchor.title) {
                anchor.title = label.textContent.trim(); // full text on hover
            }

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'v5-sp-eye';
            btn.title = i18n.previewTitle || 'Aperçu';
            btn.setAttribute('aria-label', (i18n.previewTitle || 'Aperçu') + ' : ' + (layouts[name].label || name));
            btn.innerHTML = '<span class="dashicons dashicons-visibility" aria-hidden="true"></span>';

            btn.addEventListener('click', function (e) {
                // Never let this reach ACF's anchor (inserts the row) nor the
                // document (closes the popup).
                e.preventDefault();
                e.stopPropagation();
                openModal(anchor);
            });

            anchor.appendChild(btn);
            anchor.classList.add('v5-sp-has-eye');
        });
    }

    // ACF injects the popup on demand, so watch for it.
    new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (node) {
                if (node.nodeType !== 1) return;
                if (node.classList && node.classList.contains('acf-fc-popup')) {
                    decorate(node);
                } else if (node.querySelector) {
                    var popup = node.querySelector('.acf-fc-popup');
                    if (popup) decorate(popup);
                }
            });
        });
    }).observe(document.body, { childList: true, subtree: true });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
            e.stopPropagation();
            closeModal();
        }
    });
})();
