/**
 * Appearance → Nouveautés du thème — search box, category filter chips, and
 * a scrollspy on the version sidebar. Pure presentation: it only ever toggles
 * `hidden` on server-rendered nodes, never touches their content.
 */
(function () {
    'use strict';

    var wrap = document.querySelector('.v5-changelog');
    if (!wrap) return;

    var searchInput = wrap.querySelector('#v5-cl-search-input');
    var chips = wrap.querySelectorAll('.v5-cl-filter-chip');
    var releases = wrap.querySelectorAll('.v5-cl-release');
    var tocLinks = wrap.querySelectorAll('.v5-cl-toc a');
    var emptyMsg = wrap.querySelector('.v5-cl-empty');

    var activeSections = {};
    for (var c = 0; c < chips.length; c++) {
        activeSections[chips[c].getAttribute('data-section')] = true;
    }

    function tocItemFor(releaseId) {
        for (var i = 0; i < tocLinks.length; i++) {
            if (tocLinks[i].getAttribute('data-target') === releaseId) {
                return tocLinks[i].closest('li');
            }
        }
        return null;
    }

    function applyFilters() {
        var query = ((searchInput && searchInput.value) || '').trim().toLowerCase();
        var anyReleaseVisible = false;

        for (var r = 0; r < releases.length; r++) {
            var release = releases[r];
            var sections = release.querySelectorAll('.v5-cl-section');
            var releaseVisible = false;

            for (var s = 0; s < sections.length; s++) {
                var section = sections[s];
                var sectionActive = activeSections[section.getAttribute('data-section')];
                var items = section.querySelectorAll('.v5-cl-entries li');
                var sectionVisible = false;

                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    var matches = sectionActive && (!query || (item.getAttribute('data-search') || '').indexOf(query) !== -1);
                    item.hidden = !matches;
                    if (matches) sectionVisible = true;
                }

                section.hidden = !sectionVisible;
                if (sectionVisible) releaseVisible = true;
            }

            release.hidden = !releaseVisible;
            if (releaseVisible) anyReleaseVisible = true;

            var tocItem = tocItemFor(release.id);
            if (tocItem) tocItem.hidden = !releaseVisible;
        }

        if (emptyMsg) emptyMsg.hidden = anyReleaseVisible;
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    for (var b = 0; b < chips.length; b++) {
        chips[b].addEventListener('click', function () {
            var section = this.getAttribute('data-section');
            var wasPressed = this.getAttribute('aria-pressed') === 'true';
            activeSections[section] = !wasPressed;
            this.setAttribute('aria-pressed', String(!wasPressed));
            applyFilters();
        });
    }

    // ── Scrollspy: highlight the sidebar entry for the release in view ──────
    if ('IntersectionObserver' in window && tocLinks.length && releases.length) {
        var linkByTarget = {};
        for (var l = 0; l < tocLinks.length; l++) {
            linkByTarget[tocLinks[l].getAttribute('data-target')] = tocLinks[l];
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var link = linkByTarget[entry.target.id];
                if (!link) return;
                for (var t = 0; t < tocLinks.length; t++) tocLinks[t].classList.remove('is-active');
                link.classList.add('is-active');
            });
        }, { rootMargin: '-15% 0px -75% 0px' });

        for (var o = 0; o < releases.length; o++) observer.observe(releases[o]);
    }
}());
