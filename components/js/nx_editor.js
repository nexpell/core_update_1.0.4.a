/**
 * ==========================================================
 * NEXPELL ADMIN CORE JS
 * - NxEditor
 * - Multi Language Editor
 * - Tooltips
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', () => {

    initNxEditors();
    initLangEditors();
    initLangPaneEditors();
    initLangValueSync();
    initTooltips();

});


/* ==========================================================
   NX EDITOR
========================================================== */

window.NxEditor = {

    instances: new Map(),

    init(textarea) {

        if (this.instances.has(textarea)) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'nx-editor';

         wrapper.innerHTML = `
            <div class="nx-editor-toolbar">
                <select data-cmd="formatBlock" data-tooltip="Textstruktur (Absatz / Überschrift)">
                    <option value="p">Paragraph</option>
                    <option value="h1">Heading 1</option>
                    <option value="h2">Heading 2</option>
                </select>

                <select data-cmd="fontSize" data-tooltip="Schriftgröße">
                    <option value="3">Normal</option>
                    <option value="2">Small</option>
                    <option value="4">Large</option>
                </select>

                <input type="color" data-cmd="foreColor" value="#000000" data-tooltip="Textfarbe">
                <input type="color" data-cmd="hiliteColor" value="#ffffff" data-tooltip="Hintergrundfarbe">

                <button type="button" data-cmd="bold" data-tooltip="Fett"><b>B</b></button>
                <button type="button" data-cmd="italic" data-tooltip="Kursiv"><i>I</i></button>
                <button type="button" data-cmd="underline" data-tooltip="Unterstreichen"><u>U</u></button>
                <button type="button" data-cmd="strikeThrough" data-tooltip="Durchgestrichen"><s>S</s></button>

                <button type="button" data-cmd="createLink" data-tooltip="Link einfügen">🔗</button>
                <button type="button" data-cmd="insertUnorderedList" data-tooltip="Aufzählung">•</button>
                <button type="button" data-cmd="insertOrderedList" data-tooltip="Nummerierte Liste">1–2–3</button>

                <button type="button" data-cmd="insertImage" data-tooltip="Bild per URL einfügen">🖼</button>
                <button type="button" data-cmd="toggleSource" data-tooltip="HTML-Quelltext anzeigen">&lt;/&gt;</button>
            </div>


            <div class="nx-editor-content" contenteditable="true"></div>
        `;

        textarea.style.display = 'none';
        textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

        const editor = wrapper.querySelector('.nx-editor-content');
        editor.innerHTML = textarea.value;

        /* Toolbar */
        wrapper.querySelectorAll('[data-cmd]').forEach(btn => {

            btn.addEventListener('click', e => {

                e.preventDefault();
                editor.focus();

                const cmd = btn.dataset.cmd;

                document.execCommand('styleWithCSS', false, true);

                if (cmd === 'createLink') {
                    const url = prompt('Linkadresse:', 'https://');
                    if (url) document.execCommand('createLink', false, url);
                    return;
                }

                if (cmd === 'insertImage') {
                    const url = prompt('Bild-URL:');
                    if (url) document.execCommand('insertImage', false, url);
                    return;
                }

                document.execCommand(cmd, false, null);

            });

        });

        textarea.form?.addEventListener('submit', () => {
            textarea.value = editor.innerHTML;
        });

        this.instances.set(textarea, { wrapper, editor });

    },

    destroy(textarea) {

        const instance = this.instances.get(textarea);
        if (!instance) return;

        textarea.value = instance.editor.innerHTML;
        instance.wrapper.remove();
        textarea.style.display = '';
        this.instances.delete(textarea);

    }
};


function initNxEditors() {

    document.querySelectorAll('textarea[data-editor="nx_editor"]').forEach(textarea => {
        NxEditor.init(textarea);
    });

}


/* ==========================================================
   LANGUAGE EDITOR (GLOBAL & SCALABLE)
========================================================== */

function initLangEditors() {

    document.querySelectorAll('.nx-lang-editor').forEach(container => {

        const textarea   = container.querySelector('#nx-editor-main');
        const langSwitch = container.querySelector('#lang-switch');
        const activeLangInput = container.querySelector('#active_lang');
        const titleInput = container.querySelector('#nx-title-input');
        const form = container.closest('form');

        if (!textarea || !langSwitch || !activeLangInput) return;

        function getContent() {
            const instance = NxEditor.instances.get(textarea);
            return instance ? instance.editor.innerHTML : textarea.value;
        }

        function setContent(val) {
            const instance = NxEditor.instances.get(textarea);
            if (instance) {
                instance.editor.innerHTML = val;
            } else {
                textarea.value = val;
            }
        }

        /* ==========================================
           🔥 DATUM FUNKTION (NEU)
        ========================================== */
        function updateDateDisplay(lang) {

            const dateSpan = container.querySelector('#last-update-text');
            if (!dateSpan) return;

            if (typeof lastUpdateByLang !== 'undefined' && lastUpdateByLang[lang]) {

                const raw = lastUpdateByLang[lang];
                const d = new Date(raw.replace(' ', 'T'));
                const pad = n => String(n).padStart(2, '0');

                dateSpan.textContent =
                    pad(d.getDate()) + '.' +
                    pad(d.getMonth() + 1) + '.' +
                    d.getFullYear() + ' ' +
                    pad(d.getHours()) + ':' +
                    pad(d.getMinutes());

            } else {
                dateSpan.textContent = '–';
            }
        }

        /* ==========================================
           🔥 BEIM LADEN DER SEITE DATUM SETZEN
        ========================================== */
        updateDateDisplay(activeLangInput.value);

        /* ==========================================
           LANGUAGE SWITCH
        ========================================== */
        langSwitch.querySelectorAll('button').forEach(btn => {

btn.addEventListener('click', function () {

    const newLang = this.dataset.lang;
    const activeBtn = langSwitch.querySelector('.btn-primary');
    if (!activeBtn) return;

    const oldLang = activeBtn.dataset.lang;
    if (newLang === oldLang) return;

    /* =========================
       CONTENT speichern
    ========================= */
    const oldHidden = container.querySelector('#content_' + oldLang);
    if (oldHidden) oldHidden.value = getContent();

    /* =========================
       TITLE speichern
    ========================= */
    if (titleInput) {
        const oldTitleHidden = container.querySelector('#title_' + oldLang);
        if (oldTitleHidden) oldTitleHidden.value = titleInput.value;
    }

    /* =========================
       CONTENT laden
    ========================= */
    const newHidden = container.querySelector('#content_' + newLang);
    setContent(newHidden ? newHidden.value : '');

    /* =========================
       TITLE laden
    ========================= */
    if (titleInput) {
        const newTitleHidden = container.querySelector('#title_' + newLang);
        titleInput.value = newTitleHidden ? newTitleHidden.value : '';
    }

    /* =========================
       🔥 DATUM AKTUALISIEREN
    ========================= */
    if (typeof updateDateDisplay === 'function') {
        updateDateDisplay(newLang);
    }

    /* =========================
       STATE
    ========================= */
    activeLangInput.value = newLang;

    activeBtn.classList.remove('btn-primary');
    activeBtn.classList.add('btn-secondary');

    this.classList.remove('btn-secondary');
    this.classList.add('btn-primary');
});

function updateDateDisplay(lang) {

    const dateSpan = document.getElementById('last-update-text');
    if (!dateSpan) return;

    if (lastUpdateByLang && lastUpdateByLang[lang]) {

        const raw = lastUpdateByLang[lang];
        const d = new Date(raw.replace(' ', 'T'));

        const pad = n => String(n).padStart(2, '0');

        dateSpan.textContent =
            pad(d.getDate()) + '.' +
            pad(d.getMonth() + 1) + '.' +
            d.getFullYear() + ' ' +
            pad(d.getHours()) + ':' +
            pad(d.getMinutes());

    } else {
        dateSpan.textContent = '–';
    }
}

        });

        /* ==========================================
           SUBMIT SYNC
        ========================================== */
        if (form) {
            form.addEventListener('submit', function () {

                const activeBtn = langSwitch.querySelector('.btn-primary');
                if (!activeBtn) return;

                const currentLang = activeBtn.dataset.lang;

                const activeHidden = container.querySelector('#content_' + currentLang);
                if (activeHidden) activeHidden.value = getContent();

                if (titleInput) {
                    const activeTitle = container.querySelector('#title_' + currentLang);
                    if (activeTitle) activeTitle.value = titleInput.value;
                }
            });
        }

    });
}


/* ==========================================================
   BOOTSTRAP TOOLTIPS
========================================================== */

function initTooltips() {

    if (typeof bootstrap === 'undefined') return;

    document.querySelectorAll('[data-tooltip]').forEach(el => {

        el.setAttribute('data-bs-toggle', 'tooltip');
        el.setAttribute('data-bs-placement', 'top');
        el.setAttribute('data-bs-title', el.dataset.tooltip);

        new bootstrap.Tooltip(el);

    });

}

/* ==========================================================
   LANGUAGE PANE SWITCHER
========================================================== */

function initLangPaneEditors() {

    document.querySelectorAll('[data-nx-lang-pane]').forEach(container => {

        const langSwitch = container.querySelector('#lang-switch');
        const activeLangInput = container.querySelector('#active_lang');
        if (!langSwitch || !activeLangInput) return;

        const classPrefix = container.dataset.nxLangClassPrefix || 'lang-';
        const paneSelector = container.dataset.nxLangPaneSelector || '.lang-pane';

        function switchLang(lang) {
            activeLangInput.value = lang;

            container.querySelectorAll(paneSelector).forEach(el => {
                el.style.display = el.classList.contains(classPrefix + lang) ? '' : 'none';
            });

            langSwitch.querySelectorAll('button[data-lang]').forEach(btn => {
                const active = btn.dataset.lang === lang;
                btn.classList.toggle('btn-primary', active);
                btn.classList.toggle('btn-secondary', !active);
            });
        }

        langSwitch.querySelectorAll('button[data-lang]').forEach(btn => {
            btn.addEventListener('click', () => {
                const next = btn.dataset.lang;
                if (next) switchLang(next);
            });
        });

        switchLang(activeLangInput.value || 'de');
    });
}

/* ==========================================================
   LANGUAGE VALUE SYNC (MAIN <-> HIDDEN BY LANG)
========================================================== */

function initLangValueSync() {

    document.querySelectorAll('[data-nx-lang-hidden-prefix]').forEach(mainInput => {

        const scopeSelector = mainInput.dataset.nxLangScope || '';
        const scope = scopeSelector ? document.querySelector(scopeSelector) : (mainInput.closest('form') || document);
        if (!scope) return;

        const switchSelector = mainInput.dataset.nxLangSwitch || '#lang-switch';
        const activeSelector = mainInput.dataset.nxLangActive || '#active_lang';
        const hiddenPrefix = mainInput.dataset.nxLangHiddenPrefix || '';
        if (!hiddenPrefix) return;

        const langSwitch = scope.querySelector(switchSelector) || document.querySelector(switchSelector);
        const activeLangInput = scope.querySelector(activeSelector) || document.querySelector(activeSelector);
        if (!langSwitch || !activeLangInput) return;

        let current = activeLangInput.value || 'de';

        function getMainValue() {
            return typeof mainInput.value === 'string' ? mainInput.value : '';
        }

        function setMainValue(value) {
            if (typeof mainInput.value === 'string') {
                mainInput.value = value;
            }
        }

        function hiddenByLang(lang) {
            return scope.querySelector('#' + hiddenPrefix + lang) || document.getElementById(hiddenPrefix + lang);
        }

        function applyButtons() {
            langSwitch.querySelectorAll('button[data-lang]').forEach(btn => {
                const active = btn.dataset.lang === current;
                btn.classList.toggle('btn-primary', active);
                btn.classList.toggle('btn-secondary', !active);
            });
        }

        function switchLang(next) {
            if (!next) return;

            const currentHidden = hiddenByLang(current);
            if (currentHidden) currentHidden.value = getMainValue();

            current = next;
            activeLangInput.value = next;

            const nextHidden = hiddenByLang(next);
            setMainValue(nextHidden ? nextHidden.value : '');
            applyButtons();
        }

        langSwitch.querySelectorAll('button[data-lang]').forEach(btn => {
            btn.addEventListener('click', () => {
                const next = btn.dataset.lang;
                if (!next || next === current) return;
                switchLang(next);
            });
        });

        const form = mainInput.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                const currentHidden = hiddenByLang(current);
                if (currentHidden) currentHidden.value = getMainValue();
            });
        }

        switchLang(current);
    });
}
