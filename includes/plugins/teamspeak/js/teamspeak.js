/* =========================================================
   TeamSpeak Live Refresh – FINAL / SINGLE SCHEDULER
========================================================= */
(function () {

    if (window.__TS_LIVE_RUNNING__) return;
    window.__TS_LIVE_RUNNING__ = true;

    let tsRefreshPaused = false;
    let refreshTimeout  = null;

    /* -----------------------------------------
       Visibility
    ----------------------------------------- */
    document.addEventListener('visibilitychange', () => {
        tsRefreshPaused = document.hidden;
    });

    /* -----------------------------------------
       Pause by interaction
    ----------------------------------------- */
    let interactionTimeout = null;

    function pauseByInteraction(treeEl) {
        tsRefreshPaused = true;

        const cacheSeconds = parseInt(treeEl?.dataset.cache || '5', 10);

        clearTimeout(interactionTimeout);
        interactionTimeout = setTimeout(() => {
            if (!document.hidden) tsRefreshPaused = false;
        }, cacheSeconds * 1000);
    }

    document.addEventListener('mousedown', e => {
        const tree = e.target.closest('.ts-tree');
        if (tree) pauseByInteraction(tree);
    }, true);

    document.addEventListener('touchstart', e => {
        const tree = e.target.closest('.ts-tree');
        if (tree) pauseByInteraction(tree);
    }, true);

    /* -----------------------------------------
       CORE REFRESH LOOP (GLOBAL)
    ----------------------------------------- */
    function refreshAll() {

        if (!tsRefreshPaused) {

            document.querySelectorAll('[id^="ts-live-"]').forEach(tree => {

                const serverID = tree.id.replace('ts-live-', '');
                const mode     = tree.dataset.mode || 'content';

                fetch(
                    '/includes/plugins/teamspeak/ajax/refresh.php'
                    + '?id=' + serverID
                    + '&mode=' + encodeURIComponent(mode),
                    { cache: 'no-store' }
                )
                .then(r => r.text())
                .then(html => {
                    if (html && html.trim().length > 10 && tree.innerHTML !== html) {
                        tree.innerHTML = html;
                    }
                });

            });

        }

        scheduleNext();
    }

    function scheduleNext() {
        clearTimeout(refreshTimeout);

        const tree = document.querySelector('.ts-tree');
        const cacheSeconds = parseInt(tree?.dataset.cache || '5', 10);

        // Minimum 8s, sonst Cache-Hölle
        const delay = Math.max((cacheSeconds + 3) * 1000, 8000);

        refreshTimeout = setTimeout(refreshAll, delay);
    }

    /* -----------------------------------------
       Start once
    ----------------------------------------- */
    document.addEventListener('DOMContentLoaded', () => {
        scheduleNext();
    });

})();
