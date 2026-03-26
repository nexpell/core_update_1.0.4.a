// Nexpell Live-Builder JS (ausgelagerte Version aus builder_live.php)
// Achtung: BASE_URL, CSRF, PAGE, ZONE_SELECTORS und widgetRestrictions
// werden serverseitig als globale Variablen bereitgestellt.

(function () {
  var nxDebug = typeof window.nxDebug === "function" ? window.nxDebug : function () {};
  if (typeof window.NXB_BUILDER_VARS === "undefined") return;
  nxDebug("builder_live.js START");

  const {
    CSRF,
    PAGE,
    BASE_URL,
    ZONE_SELECTORS,
    CLANNAME = "",
  } = window.NXB_BUILDER_VARS || {};

  const SAVE_ENDPOINT = BASE_URL + "/admin/plugin_widgets_save.php";
  const RENDER_ENDPOINT = BASE_URL + "/admin/plugin_widgets_render.php";

  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const uid = () => "w_" + Math.random().toString(36).slice(2, 9);
  const removedInstanceIds = [];
  let palList = null,
    palPanel = null,
    btnToggle = null;
  const PAL_VISIBLE_KEY = "nxPalVisible";

  // Bootstrap-Icon-Auswahl (für Core-Widgets wie Feature-Grid)
  const ICON_LIST = [
    "bi-star",
    "bi-star-fill",
    "bi-lightning-charge",
    "bi-lightning-charge-fill",
    "bi-shield-check",
    "bi-rocket-takeoff",
    "bi-emoji-smile",
    "bi-heart",
    "bi-check-circle",
    "bi-gear",
    "bi-graph-up",
    "bi-people",
    "bi-chat-dots",
    "bi-globe",
    "bi-cloud",
    "bi-award",
    "bi-bag-check",
    "bi-cpu",
    "bi-phone",
  ];
  let iconPickerEl = null;
  let iconPickerTarget = null;

  // Settings-UI (rechte Sidebar)
  let currentSettingsItem = null;
  let globalOptikActive = false;
  let settingsSidebar = null;
  let settingsContent = null;
  let settingsPlaceholder = null;
  let settingsForm = null;
  let settingsTextarea = null;
  let settingsError = null;
  let settingsFields = null;
  let iconPickerInitialized = false;

  // Inline-Editor (Ausrichtung, Text, Bild)
  let alignToolbarEl = null;
  let imagePopoverEl = null;
  let inlineEditingActive = false;
  const ALIGN_KEYS = ["core_heading", "core_text", "core_image", "core_button", "core_header", "core_quote", "core_hero", "core_hero_split", "core_alert", "core_badge", "core_section_full", "core_section_two_col", "core_section_three_col", "core_row", "core_col", "core_table", "core_faq", "core_testimonials", "core_timeline", "core_slider", "core_pricing", "core_collapse", "core_list_group", "core_link", "core_footer_simple", "core_footer_3col", "core_footer_centered"];

  // Visuelle Vorlagen / Presets direkt in der Palette (für wichtige Content-Widgets)
  const PRESETS = {
    core_header: [
      {
        id: "header-clean-left",
        title: "Clean Section-Header links",
        description: "Schlichte Abschnittsüberschrift mit schmalem Untertitel.",
        settings: {
          title: "Was wir für dich tun",
          subtitle: "Ein klarer Einstieg in den nächsten Abschnitt.",
          align: "start",
          level: 2,
          display: "",
        },
        previewType: "header-text",
      },
      {
        id: "header-eyebrow",
        title: "Header mit Eyebrow & Linie",
        description: "Kleines Label, starke Zeile, feine Trennlinie.",
        settings: {
          title: "Warum Kunden bei uns bleiben",
          subtitle: "ERFOLGSGESCHICHTEN",
          align: "start",
          level: 2,
          display: "",
        },
        previewType: "header-eyebrow",
      },
      {
        id: "header-overlay-center",
        title: "Overlay-Header über Bild",
        description: "Zentrierter Text auf dunklem Bild-Overlay.",
        settings: {
          title: "Starte dein nächstes Kapitel",
          subtitle: "Digitale Auftritte, die im Kopf bleiben.",
          align: "center",
          level: 1,
          display: "display-5",
          image: "",
          // Flag: auch ohne Bild schon Overlay-Layout verwenden
          forceOverlay: true,
          imageHeight: 40,
          imageHeightUnit: "vh",
          vignetteSize: 60,
          vignetteOpacity: 65,
        },
        previewType: "header-overlay",
      },
    ],
    core_hero: [
      {
        id: "hero-dark-gradient",
        title: "Agentur Hero – Hell, zentriert",
        description: "Hero mit Bildhintergrund, zentriertem Text und zwei Call-to-Actions.",
        settings: {
          title: "Weniger Technik, mehr Ergebnisse.",
          subtitle: "Branding, Websites & Kampagnen",
          text: "Wir entwickeln digitale Auftritte, die messbar performen – von der ersten Idee bis zum Launch.",
          bg: "bg-dark",
          padding: "py-6",
          mode: "light",
          align: "center",
          bgImage: "https://images.unsplash.com/photo-1603202662747-00e33e7d1468?q=80&w=2680&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
          primaryLabel: "Projekt anfragen",
          primaryUrl: "#",
          secondaryLabel: "Showreel ansehen",
          secondaryUrl: "#",
        },
        previewType: "hero-dark",
      },
      {
        id: "hero-light-centered",
        title: "Agentur Hero – Hell, linksbündig",
        description: "Hero mit Bildhintergrund, linksbündigem Content-Block und einem CTA.",
        settings: {
          title: "Enter Your Headline Here",
          subtitle: "SUBHEADLINE",
          text: "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptatem eos ea, cum quae facilis optio impedit tempora aliquam at eveniet?",
          bg: "bg-dark",
          padding: "py-6",
          mode: "light",
          align: "start",
          bgImage: "https://images.unsplash.com/photo-1603202662747-00e33e7d1468?q=80&w=2680&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
          primaryLabel: "Get Started",
          primaryUrl: "#",
          secondaryLabel: "",
          secondaryUrl: "#",
        },
        previewType: "hero-dark",
      },
    ],
    core_hero_split: [
      {
        id: "hero-split-agency",
        title: "Agentur Hero – Split",
        description: "Zweiteiliges Layout mit Text links und Bild rechts.",
        settings: {
          title: "Enter Your Headline Here",
          subtitle: "SUBHEADLINE",
          text: "Kurz erklärt, welchen Wert eure Agentur liefert – ohne Fachchinesisch.",
          bg: "",
          padding: "py-0",
          mode: "dark",
          align: "start",
          bgImage: "https://images.unsplash.com/photo-1603202662747-00e33e7d1468?q=80&w=2680&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
          primaryLabel: "Get Started",
          primaryUrl: "#",
        },
        previewType: "hero-split",
      },
    ],
    core_testimonials: [
      {
        id: "testimonials-3-cards",
        title: "3 Karten im Grid",
        description: "Klassisches Referenzen-Layout mit Karten & Namen.",
        settings: {
          title: "Was unsere Kunden sagen",
          subtitle: "Ausgewählte Stimmen aus Projekten der letzten Monate.",
          columns: 3,
          align: "start",
          item1_quote: "„In zwei Wochen von der Idee zur fertigen Landingpage.“",
          item1_name: "Lena Hoffmann",
          item1_role: "Marketing Lead",
          item1_company: "Studio Nord",
          item2_quote: "„Der Builder spart uns jede Woche mehrere Stunden Arbeit.“",
          item2_name: "Tobias Klein",
          item2_role: "Geschäftsführer",
          item2_company: "Klein Consulting",
          item3_quote: "„Endlich kann das Team Inhalte selbst pflegen – ohne Agentur.“",
          item3_name: "Sarah Müller",
          item3_role: "Produktmanagerin",
          item3_company: "",
        },
        previewType: "testimonials-grid",
      },
      {
        id: "testimonials-1-highlight",
        title: "Großes Highlight-Zitat",
        description: "Eine starke Referenz im Fokus.",
        settings: {
          title: "Erfolgsgeschichte",
          subtitle: "",
          columns: 1,
          align: "center",
          item1_quote: "„Wir konnten innerhalb kürzester Zeit eine professionelle Präsenz aufbauen – ohne eigenes Entwicklerteam.“",
          item1_name: "Dr. Jana Reuter",
          item1_role: "CEO",
          item1_company: "Reuter Health",
        },
        previewType: "testimonials-single",
      },
    ],
    core_faq: [
      {
        id: "faq-compact-3",
        title: "Kompakte FAQ (3 Einträge)",
        description: "Kurze Antworten, ideal am Seitenende.",
        settings: {
          title: "Häufige Fragen",
          subtitle: "",
          align: "start",
          item1_title: "Brauche ich Programmierkenntnisse?",
          item1_content: "Nein. Inhalte werden direkt im Layout bearbeitet – per Doppelklick auf Texte, Bilder, Buttons.",
          item2_title: "Kann ich später das Design ändern?",
          item2_content: "Ja. Du kannst Abschnitte austauschen, Reihenfolgen ändern und Farben in deinem Theme anpassen.",
          item3_title: "Wie veröffentliche ich Änderungen?",
          item3_content: "Über den Speichern-Button im Builder – ohne Deployments oder zusätzliche Tools.",
        },
        previewType: "faq-compact",
      },
      {
        id: "faq-section-with-intro",
        title: "FAQ mit Intro-Text",
        description: "Einführungstext plus mehrere Fragen – ideal für Support-Seiten.",
        settings: {
          title: "Noch Fragen offen?",
          subtitle: "Hier findest du Antworten auf häufige Themen rund um Setup, Inhalte und Wartung.",
          align: "start",
          item1_title: "Wie lange dauert der Start?",
          item1_content: "In der Regel kannst du innerhalb eines Tages die ersten Seiten live schalten.",
          item2_title: "Kann ich mehrere Sprachen nutzen?",
          item2_content: "Ja, der Builder ist für mehrsprachige Seiten vorbereitet.",
          item3_title: "Gibt es eine Backup-Funktion?",
          item3_content: "Ja. Änderungen werden im System gespeichert und können bei Bedarf wiederhergestellt werden.",
        },
        previewType: "faq-intro",
      },
    ],
  };

  // Template-Modus: Kategorien links, Vorlagen rechts in der Examples-Sidebar
  const TEMPLATE_MODE = true;
  const TEMPLATES = {
    starter_landing: {
      name: "Starter Landing",
      categories: [
        { id: "header", label: "Header" },
        { id: "hero", label: "Hero" },
        { id: "nav", label: "Navigation" },
        { id: "footer", label: "Footer" },
      ],
      blocks: {
        header: [
          {
            id: "header_overlay_nav_hero",
            title: "Header – Transparent Overlay (Scroll-Fill) + Hero",
            description:
              "Navigation liegt transparent über dem ersten Hero und füllt sich nach dem Scrollen.",
            widget: "core_nav_demo",
            // Bundle: wird im Drop-Handler zu mehreren Widgets expandiert
            bundle: [
              {
                widget: "core_nav_demo",
                title: "Navigation",
                settings: {
                  layout: "simple",
                  title: "Nexpell",
                  image: "",
                  // Overlay-Start (transparent), Text hell auf Hero
                  overlayMode: true,
                  overlayTextMode: "light",
                  // Scroll-Fill: ab X px in "gefüllt" wechseln
                  scrollFill: true,
                  scrollFillOffset: 80,
                  // gefülltes Schema
                  scheme: "light",
                  filledShadow: "shadow-sm",
                  container: "fixed",
                  paddingY: "26px",
                  paddingX: "16",
                  hoverEffect: "none",
                  menuSource: "plugin",
                  menu: [],
                  login_label: "Login",
                  login_url: "",
                  cta_label: "Jetzt starten",
                },
              },
              {
                widget: "core_hero",
                title: "Hero",
                settings: PRESETS.core_hero[0].settings,
              },
            ],
            previewType: "header-overlay",
          },
        ],
        hero: [
          {
            id: "hero_agency_dark",
            title: "Agentur Hero – Hell, zentriert",
            description: "Hero mit Bildhintergrund, zentriertem Text und zwei Call-to-Actions.",
            widget: "core_hero",
            settings: PRESETS.core_hero[0].settings,
            previewType: "hero-dark",
          },
          {
            id: "hero_agency_light_center",
            title: "Agentur Hero – Hell, linksbündig",
            description: "Hero mit Bildhintergrund, linksbündigem Content-Block und einem CTA.",
            widget: "core_hero",
            settings: PRESETS.core_hero[1].settings,
            previewType: "hero-light",
          },
          {
            id: "hero_agency_split",
            title: "Agentur Hero – Split",
            description: "Zweiteiliges Layout mit Text links und Bild rechts.",
            widget: "core_hero_split",
            settings: PRESETS.core_hero_split[0].settings,
            previewType: "hero-split",
          },
        ],
        nav: [
          {
            id: "nav_simple",
            title: "Klassische Top-Navigation",
            description: "Logo links, Links mittig, CTA rechts.",
            widget: "core_nav_demo",
            settings: {
              layout: "simple",
              title: "Nexpell",
              image: "",
              scheme: "light",
              shadow: "shadow-sm",
              container: "fixed",
              paddingY: "26px",
              paddingX: "16",
              // Standard: kein Hover-Effekt
              hoverEffect: "none",
              menuSource: "plugin",
              menu: [],
              login_label: "Login",
              login_url: "",
              cta_label: "Jetzt starten",
            },
            previewType: "nav-simple",
          },
        ],
        footer: [
          // Minimaler Link-Footer (ähnlich Geeks Footer #5)
          {
            id: "footer_minimal_links",
            title: "Footer – minimal, Links zentriert",
            description: "Schlanker Footer mit wenigen Links und Copyright.",
            widget: "core_footer_simple",
            settings: {
              brand: "Nexpell",
              year: new Date().getFullYear().toString(),
              container: "container",
              visibility: "",
              nav1: "Über uns",
              nav2: "Blog",
              nav3: "Feedback senden",
              nav4: "Nutzungsbedingungen",
              nav5: "Support",
              nav6: "",
              nav7: "",
            },
            previewType: "section-full",
          },
          // Drei-Spalten-Footer (About + About-Links + Help-Links, hell)
          {
            id: "footer_light_3col",
            title: "Footer – Über uns & Hilfe",
            description: "Heller 3-Spalten-Footer mit Beschreibung und zwei Link-Spalten.",
            widget: "core_footer_3col",
            settings: {
              container: "container",
              visibility: "",
              brand: "Nexpell",
              about:
                "Nexpell ist ein modularer Website-Builder mit modernen Bootstrap-5-Komponenten.",
              about_title: "Über uns",
              about1: "Nutzungsbedingungen",
              about2: "Datenschutz",
              about3: "Support",
              about4: "Presse",
              help_title: "Hilfe & Support",
              help1: "Allgemeine Fragen",
              help2: "FAQ",
              help3: "Abrechnung",
              help4: "Rechnungen & Zahlungen",
            },
            previewType: "section-three-col",
          },
          // Großer Plattform-Footer mit vier Link-Spalten im Nexpell-Stil
          {
            id: "footer_platform_columns_light",
            title: "Footer – Plattform / Ressourcen / Unternehmen / Support",
            description:
              "Großer, heller Footer mit Nexpell-Brand, Beschreibung, Social-Icons und vier Link-Spalten.",
            widget: "core_footer_2col",
            settings: {
              container: "container",
              visibility: "",
              brand: "Nexpell",
              about:
                "Nexpell ist ein modularer Website-Builder mit klaren Workflows für Teams, Agenturen und SaaS-Produkte.",
              year: new Date().getFullYear().toString(),
              platform_title: "Plattform",
              platform1: "Vorlagen durchsuchen",
              platform2: "Live-Builder",
              platform3: "Plugin-Store",
              platform4: "Changelog",
              resources_title: "Ressourcen",
              resources1: "Docs",
              resources2: "Anleitungen",
              resources3: "Case Studies",
              resources4: "Blog",
              resources5: "Community",
              company_title: "Unternehmen",
              company1: "Über Nexpell",
              company2: "Partnerprogramm",
              company3: "Datenschutzerklärung",
              company4: "Team",
              support_title: "Support",
              support1: "FAQ",
              support2: "Hilfe-Center",
              support3: "Systemstatus",
              support4: "Community beitreten",
              copyright_text: "Nexpell. Alle Rechte vorbehalten.",
              policy_privacy: "Datenschutzerklärung",
              policy_cookies: "Cookie-Hinweis",
              policy_terms: "Nutzungsbedingungen",
            },
            previewType: "section-two-col",
          },
          // Link-Footer mit Social-Icons im Nexpell-Stil
          {
            id: "footer_social_links",
            title: "Footer – Links & Social",
            description:
              "Zentrierter Footer mit Link-Navigation, Social-Icons und Copyright-Hinweis.",
            widget: "core_footer_simple",
            settings: {
              brand: "Nexpell",
              year: new Date().getFullYear().toString(),
              container: "container",
              visibility: "",
              nav1: "Über uns",
              nav2: "Blog",
              nav3: "Feedback senden",
              nav4: "Nutzungsbedingungen",
              nav5: "Support erhalten",
              nav6: "",
              nav7: "",
            },
            previewType: "section-full",
          },
          // Zentrierter Footer im Nexpell-Stil
          {
            id: "footer_centered_hero",
            title: "Footer – zentriert, Beschreibung",
            description:
              "Zentrierter Footer mit Nexpell-Brand, Beschreibung und Link-Navigation.",
            widget: "core_footer_centered",
            settings: {
              container: "container",
              visibility: "",
              brand: "Nexpell",
              description:
                "Nexpell ist ein modularer Website-Builder mit modernen Bootstrap-5-Komponenten und einem intuitiven Live-Builder.",
              year: new Date().getFullYear().toString(),
              nav1: "Über Nexpell",
              nav2: "Karriere",
              nav3: "Kontakt",
              nav4: "Preise",
              nav5: "Blog",
              nav6: "Partnerprogramm",
              nav7: "Hilfe",
              nav8: "Investoren",
              policy_privacy: "Datenschutzerklärung",
              policy_cookies: "Cookie-Hinweis",
              policy_terms: "Nutzungsbedingungen",
              copyright_text: "© " +
                new Date().getFullYear().toString() +
                " Nexpell. Alle Rechte vorbehalten.",
            },
            previewType: "section-full",
          },
        ],
      },
    },
  };
  const CURRENT_TEMPLATE_ID = "starter_landing";

  let examplesPanel = null;
  let examplesBody = null;
  let examplesTitleEl = null;
  let examplesEmptyEl = null;
  let examplesSortableBound = false;
  let examplesHideTimer = null;

  function getZones() {
    nxDebug("getZones START");
    const root = document.querySelector("main") || document.body;
    const candidates = root.querySelectorAll("[data-nx-zone]");
    nxDebug("getZones querySelectorAll found " + candidates.length + " [data-nx-zone]");
    const out = [];
    for (let i = 0; i < candidates.length; i++) {
      const el = candidates[i];
      out.push(el);
    }
    nxDebug("getZones END returning " + out.length + " zones");
    return out;
  }

  const DROP_HINT_TEXT = "Hier Blöcke ablegen – Reihenfolge frei wählbar";
  // Merkt sich die zuletzt wirklich unter dem Mauszeiger liegende Zone (für präzisere Drops, z. B. Col in Row)
  let lastHoverZone = null;

  function ensureDropHints() {
    nxDebug("ensureDropHints START");
    var contentZone = document.querySelector("[data-nx-zone=\"content\"]");
    if (contentZone) {
      var hasItems = contentZone.querySelector(":scope > .nx-live-item");
      var placeholder = contentZone.querySelector(".builder-placeholder");
      var hint = contentZone.querySelector(".nx-drop-hint");
      if (placeholder) {
        placeholder.style.display = hasItems ? "none" : "flex";
        if (hint) hint.remove();
      } else {
        var h = hint;
        if (!h) {
          h = document.createElement("div");
          h.className = "nx-drop-hint";
          h.textContent = DROP_HINT_TEXT;
          contentZone.appendChild(h);
        } else {
          h.textContent = DROP_HINT_TEXT;
        }
        h.style.display = hasItems ? "none" : "flex";
      }
    }
    getZones().forEach(function (z) {
      if (z.getAttribute("data-nx-zone") === "content") return;
      var hint = z.querySelector(":scope > .nx-drop-hint");
      if (hint) {
        var hasItems = z.querySelector(":scope > .nx-live-item");
        hint.style.display = hasItems ? "none" : "flex";
      }
    });
    nxDebug("ensureDropHints END");
  }

  function collectState() {
    const data = {};
    getZones().forEach((zone) => {
      const pos = zone.getAttribute("data-nx-zone");
      data[pos] = [];
      zone.querySelectorAll(":scope>.nx-live-item").forEach((el) => {
        let iid = el.getAttribute("data-nx-iid");
        if (!iid) {
          iid = uid();
          el.setAttribute("data-nx-iid", iid);
        }
        let settings = {};
        try {
          settings = JSON.parse(el.getAttribute("data-nx-settings") || "{}");
        } catch (e) {}
        // stabile ID im Settings-Objekt hinterlegen (hilft Layout-Widgets)
        if (!settings.id) {
          settings.id = iid;
        }
        data[pos].push({
          widget_key: el.getAttribute("data-nx-key") || "",
          instance_id: iid,
          settings,
        });
      });
    });
    return data;
  }

  const UNDO_STACK_KEY = "NXB_BUILDER_UNDO_STACK";
  const UNDO_STACK_LIMIT = 30;
  let undoStack = [];
  let undoIndex = -1;
  let lastSaveTimestamp = 0;

  function loadUndoStack() {
    try {
      const raw = localStorage.getItem(UNDO_STACK_KEY);
      if (!raw) return;
      const parsed = JSON.parse(raw);
      if (Array.isArray(parsed.stack)) {
        undoStack = parsed.stack;
        undoIndex = typeof parsed.index === "number" ? parsed.index : parsed.stack.length - 1;
      }
    } catch (e) {}
  }

  function persistUndoStack() {
    try {
      localStorage.setItem(UNDO_STACK_KEY, JSON.stringify({ stack: undoStack, index: undoIndex }));
    } catch (e) {}
  }

  function pushUndoSnapshot(body) {
    // Wenn wir „in der Mitte“ der History sind (nach Undo), alle neueren States abschneiden
    if (undoIndex < undoStack.length - 1) {
      undoStack = undoStack.slice(0, undoIndex + 1);
    }
    undoStack.push(body);
    if (undoStack.length > UNDO_STACK_LIMIT) {
      undoStack.shift();
    }
    undoIndex = undoStack.length - 1;
    persistUndoStack();
  }

  /** Theme-Optionen als CSS rendern (Spiegel von theme_options.php) und #nx-theme-options aktualisieren (Echtzeit im Builder). */
  function nxApplyThemeOptionsCss(opts) {
    if (!opts || typeof opts !== "object") return;
    var get = function(k, def) { def = def || ""; var v = opts[k]; return (v != null && typeof v === "string") ? v.trim() : def; };
    var sanitizeColor = function(v) {
      v = String(v).trim();
      if (!v) return "";
      if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(v)) return v;
      if (/^rgb\(|^rgba\(|^hsl\(|^hsla\(/.test(v)) return v.replace(/[^a-z0-9(),.%\s-]/g, "");
      return v;
    };
    var sanitizeSize = function(v) {
      v = String(v).trim();
      return /^\d+(\.\d+)?(rem|em|px|%)$/.test(v) ? v : "";
    };
    var sanitizeDecoration = function(v) { return String(v).replace(/[^a-z-]/g, ""); };
    var hexToRgb = function(hex) {
      var m = /^#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/.exec(hex);
      return m ? parseInt(m[1], 16) + ", " + parseInt(m[2], 16) + ", " + parseInt(m[3], 16) : "13, 110, 253";
    };
    var vars = [];
    var bgVal = get("theme_bg_color");
    var bg = bgVal ? sanitizeColor(bgVal) : "";
    if (bg) vars.push("  --bs-body-bg: " + bg + ";");
    var txtVal = get("theme_text_color");
    var txt = txtVal ? sanitizeColor(txtVal) : "";
    if (txt) vars.push("  --bs-body-color: " + txt + ";");
    var primary = get("theme_primary");
    if (primary) {
      var p = sanitizeColor(primary);
      if (p) {
        vars.push("  --bs-primary: " + p + ";");
        vars.push("  --bs-primary-rgb: " + hexToRgb(p || primary) + ";");
      }
    }
    var sec = get("theme_secondary");
    if (sec) {
      var s = sanitizeColor(sec);
      if (s) {
        vars.push("  --bs-secondary: " + s + ";");
        var m = /^#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/.exec(s);
        if (m) vars.push("  --bs-secondary-rgb: " + parseInt(m[1], 16) + "," + parseInt(m[2], 16) + "," + parseInt(m[3], 16) + ";");
      }
    }
    var linkColor = get("theme_link_color");
    if (linkColor) { var lc = sanitizeColor(linkColor); if (lc) { vars.push("  --bs-link-color: " + lc + ";"); vars.push("  --bs-nav-link-color: " + lc + ";"); } }
    var linkDec = get("theme_link_decoration");
    if (linkDec) vars.push("  --bs-link-decoration: " + sanitizeDecoration(linkDec) + ";");
    var linkHoverColor = get("theme_link_hover_color");
    if (linkHoverColor) {
      var lhc = sanitizeColor(linkHoverColor);
      if (lhc) { vars.push("  --bs-link-hover-color: " + lhc + ";"); vars.push("  --bs-nav-link-hover-color: " + lhc + ";"); }
    } else {
      vars.push("  --bs-link-hover-color: var(--bs-primary);");
      vars.push("  --bs-nav-link-hover-color: var(--bs-primary);");
    }
    var linkHoverDec = get("theme_link_hover_decoration");
    if (linkHoverDec) vars.push("  --bs-link-hover-decoration: " + sanitizeDecoration(linkHoverDec) + ";");
    var fs = get("theme_font_size");
    if (fs) { var fss = sanitizeSize(fs); if (fss) vars.push("  --bs-body-font-size: " + fss + ";"); }
    var css = "/* Basis-Design (Live-Builder) */\n:root {\n" + vars.join("\n") + "\n}\n";
    if (bg || txt) {
      css += "html, html body, body, .sticky-footer-wrapper { ";
      if (bg) css += "background-color: " + bg + " !important; ";
      if (txt) css += "color: " + txt + " !important; ";
      css += "}\n";
    }
    if (bg) {
      css += "body.builder-active html, body.builder-active, body.builder-active body, body.builder-active .sticky-footer-wrapper { background-color: #fff !important; }\n";
      css += "body.builder-active main.flex-fill { background-color: " + bg + " !important; border: 1px solid #c5c5c5; }\n";
    }
    css += "html body a { color: var(--bs-link-color, inherit); text-decoration: var(--bs-link-decoration, none) !important; }\n";
    css += "html body a:hover, html body a:focus { color: var(--bs-link-hover-color, var(--bs-primary)); text-decoration: var(--bs-link-hover-decoration, var(--bs-link-decoration, none)) !important; }\n";
    var styleEl = document.getElementById("nx-theme-options");
    if (styleEl) {
      styleEl.textContent = css;
    } else {
      styleEl = document.createElement("style");
      styleEl.id = "nx-theme-options";
      styleEl.textContent = css;
      document.head.appendChild(styleEl);
    }
  }

  async function saveState() {
    const body = {
      page: PAGE,
      data: collectState(),
      removedInstanceIds: removedInstanceIds.splice(0),
      ts: Date.now(),
    };
    if (window._nxCurrentThemeOptions && typeof window._nxCurrentThemeOptions === "object" && Object.keys(window._nxCurrentThemeOptions).length > 0) {
      body.theme_options = window._nxCurrentThemeOptions;
    }
    try {
      const r = await fetch(SAVE_ENDPOINT, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": CSRF,
        },
        body: JSON.stringify(body),
        credentials: "same-origin",
      });
      const j = await r.json().catch(() => null);
      if (!j || !j.ok) {
        console.warn("❌ Save failed", j);
      } else {
        console.log("✅ Saved");
        lastSaveTimestamp = Date.now();
        pushUndoSnapshot(body);
        updateSaveStatus();
        if (window._nxCurrentThemeOptions && typeof window._nxCurrentThemeOptions === "object") {
          nxApplyThemeOptionsCss(window._nxCurrentThemeOptions);
        }
      }
    } catch (e) {
      console.error("Save error", e);
    } finally {
      ensureDropHints();
    }
  }

  function updateSaveStatus() {
    const el = document.getElementById("nx-save-status");
    if (!el) return;
    if (!lastSaveTimestamp) {
      el.textContent = "";
      return;
    }
    const d = new Date(lastSaveTimestamp);
    const hh = String(d.getHours()).padStart(2, "0");
    const mm = String(d.getMinutes()).padStart(2, "0");
    el.textContent = "Gespeichert um " + hh + ":" + mm;
  }

  function nxIsAllowedUniversal(widgetElOrKey, zoneName) {
    // Row-Zonen (row_*) akzeptieren nur core_col
    const zn = String(zoneName || "");
    if (zn.indexOf("row_") === 0) {
      let k = "";
      if (widgetElOrKey instanceof HTMLElement)
        k = (widgetElOrKey.dataset && (widgetElOrKey.dataset.nxKey || widgetElOrKey.dataset.palKey)) || "";
      else if (typeof widgetElOrKey === "string")
        k = widgetElOrKey;
      return k === "core_col";
    }
    let allowed = [];
    if (widgetElOrKey instanceof HTMLElement) {
      const attr = widgetElOrKey.dataset.allowed;
      if (attr) {
        allowed = attr.split(",").map((z) => z.trim()).filter(Boolean);
      } else {
        const key =
          widgetElOrKey.dataset.nxKey || widgetElOrKey.dataset.palKey;
        if (window.widgetRestrictions && window.widgetRestrictions[key])
          allowed = window.widgetRestrictions[key];
        if (widgetElOrKey.dataset.palKey && (!allowed || allowed.length === 0))
          return true;
      }
    } else if (typeof widgetElOrKey === "string") {
      if (window.widgetRestrictions && window.widgetRestrictions[widgetElOrKey])
        allowed = window.widgetRestrictions[widgetElOrKey];
    }
    if (!allowed || allowed.length === 0) return true;
    // Option B: content-Zone akzeptiert Widgets, die für content oder eine Legacy-Mittel-Zone erlaubt sind
    if (zoneName === "content") {
      const legacyContent = ["undertop", "left", "maintop", "mainbottom", "right", "content"];
      return legacyContent.some((z) => allowed.includes(z));
    }
    return allowed.includes(zoneName);
  }

  let __nxLastMarked = null;
  function nxClearMark(el) {
    if (el) el.classList.remove("nx-zone-allowed", "nx-zone-forbidden");
  }
  function nxMark(el, ok) {
    if (!el) return;
    if (__nxLastMarked && __nxLastMarked !== el) nxClearMark(__nxLastMarked);
    el.classList.toggle("nx-zone-allowed", !!ok);
    el.classList.toggle("nx-zone-forbidden", !ok);
    __nxLastMarked = el;
  }
  function nxClearAllMarks() {
    document
      .querySelectorAll(".nx-zone-allowed,.nx-zone-forbidden")
      .forEach(nxClearMark);
    __nxLastMarked = null;
  }

  function ensureExamplesPanel() {
    if (examplesPanel) return;
    examplesPanel = document.getElementById("nx-examples-panel");
    if (!examplesPanel) return;
    examplesBody = document.getElementById("nx-examples-body");
    examplesTitleEl = document.getElementById("nx-examples-title");
    examplesEmptyEl = document.getElementById("nx-examples-empty");
  }

  function hideExamplesPanelSoon() {
    if (examplesHideTimer) {
      clearTimeout(examplesHideTimer);
    }
    examplesHideTimer = setTimeout(() => {
      if (!examplesPanel) return;
      examplesPanel.classList.remove("nx-examples-visible");
      // Aktiven Kategorien-State zurücksetzen, wenn keine Vorlagen-Sidebar mehr sichtbar ist
      const palPanel = document.getElementById("nx-palette");
      if (palPanel) {
        palPanel
          .querySelectorAll(".nx-pal-category-head")
          .forEach((head) => head.classList.remove("active"));
      }
    }, 200);
  }

  function cancelHideExamples() {
    if (examplesHideTimer) {
      clearTimeout(examplesHideTimer);
      examplesHideTimer = null;
    }
  }

  function bindExamplesSortable() {
    if (examplesSortableBound) return;
    ensureExamplesPanel();
    if (!examplesBody || typeof Sortable === "undefined") return;
    const opts = {
      group: { name: "nx-builder", pull: "clone", put: false },
      sort: false,
      animation: 150,
      ghostClass: "ghost",
      // Native HTML5-Drag für natürliches Verhalten des Ghosts
      fallbackOnBody: false,
      forceFallback: false,
      onStart() {
        document.body.classList.add("nx-dragging");
        nxClearAllMarks();
      },
      onMove(evt) {
        if (!evt.to) return true;
        // Vorlagen dürfen grundsätzlich überall hin gezogen werden – keine Blockierung
        nxMark(evt.to, true);
        return true;
      },
      onEnd() {
        document.body.classList.remove("nx-dragging");
        nxClearAllMarks();
      },
    };
    new Sortable(
      examplesBody,
      Object.assign({}, opts, {
        draggable: ".nx-example-item",
        handle: ".nx-example-handle, .nx-example-item",
      })
    );
    examplesSortableBound = true;
  }

  function setActivePaletteCategoryForWidget(widgetKey) {
    try {
      const palPanel = document.getElementById("nx-palette");
      if (!palPanel) return;
      palPanel
        .querySelectorAll(".nx-pal-category-head")
        .forEach((head) => head.classList.remove("active"));
      if (!widgetKey) return;
      const item =
        palPanel.querySelector('.nx-pal-item[data-pal-key="' + widgetKey + '"]') ||
        palPanel.querySelector('.nx-pal-item[data-nx-key="' + widgetKey + '"]');
      if (!item) return;
      const head =
        item.closest(".nx-pal-category") &&
        item.closest(".nx-pal-category").querySelector(".nx-pal-category-head");
      if (head) head.classList.add("active");
    } catch (e) {
      console.warn("setActivePaletteCategoryForWidget failed", e);
    }
  }

  function renderExamplesForKey(widgetKey, title) {
    ensureExamplesPanel();
    if (!examplesPanel || !examplesBody) return;
    const variants = PRESETS[widgetKey];
    examplesBody.innerHTML = "";

    if (!variants || !variants.length) {
      examplesPanel.classList.remove("nx-examples-visible");
      if (examplesEmptyEl) examplesEmptyEl.classList.remove("d-none");
      return;
    }

    if (examplesTitleEl) {
      examplesTitleEl.textContent = title || widgetKey;
    }
    if (examplesEmptyEl) examplesEmptyEl.classList.add("d-none");

    variants.forEach((variant) => {
      const div = document.createElement("div");
      div.className = "nx-example-item";
      div.setAttribute("data-pal-key", widgetKey);
      div.setAttribute(
        "data-pal-title",
        variant.title || variant.id || widgetKey
      );
      if (variant.settings && typeof variant.settings === "object") {
        try {
          div.setAttribute(
            "data-pal-settings",
            JSON.stringify(variant.settings)
          );
        } catch (e) {}
      }

      const handle = document.createElement("div");
      handle.className = "nx-example-handle";
      handle.textContent = "⋮⋮";

      const inner = document.createElement("div");
      inner.className = "nx-example-inner";

      const titleEl = document.createElement("div");
      titleEl.className = "nx-example-title small fw-semibold mb-1";
      titleEl.textContent = variant.title || "";
      inner.appendChild(titleEl);

      if (variant.description) {
        const descEl = document.createElement("div");
        descEl.className = "nx-example-desc text-muted small mb-1";
        descEl.textContent = variant.description;
        inner.appendChild(descEl);
      }

      const previewType = variant.previewType || widgetKey;
      const preview = document.createElement("div");
      preview.className =
        "nx-pal-preview nx-pal-preview-" + previewType;
      let spanCount = 3;
      if (previewType === "faq-compact") spanCount = 6;
      else if (previewType === "faq-intro") spanCount = 8;
      else if (previewType === "testimonials-grid")
        spanCount = 3;
      else if (previewType === "testimonials-single")
        spanCount = 3;
      else if (
        previewType === "hero-dark" ||
        previewType === "hero-light"
      )
        spanCount = 3;
      else if (previewType.indexOf("header") === 0) spanCount = 3;
      for (let i = 0; i < spanCount; i++) {
        preview.appendChild(document.createElement("span"));
      }

      inner.appendChild(preview);
      div.appendChild(handle);
      div.appendChild(inner);

      examplesBody.appendChild(div);
    });

    examplesPanel.classList.add("nx-examples-visible");
    setActivePaletteCategoryForWidget(widgetKey);
    bindExamplesSortable();
  }

  function renderExamplesForTemplateCategory(templateId, categoryId, label) {
    ensureExamplesPanel();
    const tpl = TEMPLATES[templateId];
    if (!tpl || !tpl.blocks || !tpl.blocks[categoryId]) return;
    const variants = tpl.blocks[categoryId];
    if (!examplesPanel || !examplesBody) return;
    examplesBody.innerHTML = "";

    if (!variants.length) {
      examplesPanel.classList.remove("nx-examples-visible");
      if (examplesEmptyEl) examplesEmptyEl.classList.remove("d-none");
      return;
    }

    if (examplesTitleEl) {
      examplesTitleEl.textContent = label || tpl.name || "Vorlagen";
    }
    if (examplesEmptyEl) examplesEmptyEl.classList.add("d-none");

    variants.forEach((variant) => {
      const div = document.createElement("div");
      div.className = "nx-example-item";
      div.setAttribute("data-pal-key", variant.widget);
      div.setAttribute(
        "data-pal-title",
        variant.title || variant.id || variant.widget
      );
      if (variant.settings && typeof variant.settings === "object") {
        try {
          div.setAttribute(
            "data-pal-settings",
            JSON.stringify(variant.settings)
          );
        } catch (e) {}
      }
      if (Array.isArray(variant.bundle) && variant.bundle.length) {
        try {
          div.setAttribute("data-pal-bundle", JSON.stringify(variant.bundle));
        } catch (e) {}
      }

      const handle = document.createElement("div");
      handle.className = "nx-example-handle";
      handle.textContent = "⋮⋮";

      const inner = document.createElement("div");
      inner.className = "nx-example-inner";

      const titleEl = document.createElement("div");
      titleEl.className = "nx-example-title small fw-semibold mb-1";
      titleEl.textContent = variant.title || "";
      inner.appendChild(titleEl);

      if (variant.description) {
        const descEl = document.createElement("div");
        descEl.className = "nx-example-desc text-muted small mb-1";
        descEl.textContent = variant.description;
        inner.appendChild(descEl);
      }

      const previewType = variant.previewType || variant.widget;
      const preview = document.createElement("div");
      preview.className =
        "nx-pal-preview nx-pal-preview-" + previewType;
      let spanCount = 3;
      if (previewType === "faq-compact") spanCount = 6;
      else if (previewType === "faq-intro") spanCount = 8;
      else if (previewType === "testimonials-grid")
        spanCount = 3;
      else if (previewType === "testimonials-single")
        spanCount = 3;
      else if (
        previewType === "hero-dark" ||
        previewType === "hero-light"
      )
        spanCount = 3;
      else if (previewType.indexOf("header") === 0) spanCount = 3;
      for (let i = 0; i < spanCount; i++) {
        preview.appendChild(document.createElement("span"));
      }

      inner.appendChild(preview);
      div.appendChild(handle);
      div.appendChild(inner);

      examplesBody.appendChild(div);
    });

    examplesPanel.classList.add("nx-examples-visible");
    bindExamplesSortable();
  }

  function createShellFromBundleEntry(entry) {
    const tmp = document.createElement("div");
    tmp.className = "nx-pal-item";
    tmp.setAttribute("data-pal-key", entry.widget || "widget");
    tmp.setAttribute("data-pal-title", entry.title || entry.widget || "Widget");
    if (entry.settings && typeof entry.settings === "object") {
      try {
        // Defaults: Header+Navbar soll nachvollziehbar Overlay+ScrollFill aktiv haben
        if (entry.widget === "core_nav_demo") {
          if (typeof entry.settings.overlayMode === "undefined") entry.settings.overlayMode = true;
          if (typeof entry.settings.scrollFill === "undefined") entry.settings.scrollFill = true;
        }
        tmp.setAttribute("data-pal-settings", JSON.stringify(entry.settings));
      } catch (e) {}
    }
    return createShellFromPalette(tmp);
  }

  async function renderInto(el) {
    const key = el.getAttribute("data-nx-key") || "";
    const iid = el.getAttribute("data-nx-iid") || uid();
    el.setAttribute("data-nx-iid", iid);
    let settings = {};
    try {
      settings = JSON.parse(el.getAttribute("data-nx-settings") || "{}");
    } catch (e) {}
    if (!settings.id && (key === "core_container" || key === "core_columns" || key.startsWith("core_section_") || key === "core_row" || key === "core_col")) {
      settings.id = iid;
      el.setAttribute("data-nx-settings", JSON.stringify(settings));
    }
    const parentZone = el.closest("[data-nx-zone]");
    const position = parentZone ? parentZone.getAttribute("data-nx-zone") : "";
    const body = {
      widget_key: key,
      instance_id: iid,
      title: el.getAttribute("data-nx-title") || key,
      settings,
      page: PAGE,
      builder: true,
      lang: window.NXB_LANG || "de",
      csrf: CSRF,
      position,
    };
    try {
      const renderUrl = RENDER_ENDPOINT + "?format=html&_=" + Date.now();
      const res = await fetch(renderUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": CSRF,
        },
        body: JSON.stringify(body),
        credentials: "same-origin",
        cache: "no-store",
      });
      const html = await res.text();
      const c =
        el.querySelector(".nx-live-content") ||
        el.appendChild(document.createElement("div"));
      c.className = "nx-live-content";
      c.innerHTML =
        html || '<div class="alert alert-warning small">leer</div>';
      if (!res.ok) console.error("render error", res.status, html);
      try {
        // Overlay/ScrollFill (Navbar) nach jedem Render aktivieren (Builder)
        nxInitNavOverlayScrollFill(document);
      } catch (e) {}
    } catch (e) {
      console.error("render exception", e);
      const c = el.querySelector(".nx-live-content");
      if (c)
        c.innerHTML =
          '<div class="alert alert-danger small">Render-Fehler</div>';
    }
  }

  let _nxNavOverlayScrollFillBound = false;
  function nxInitNavOverlayScrollFill(root) {
    if (_nxNavOverlayScrollFillBound) return;
    _nxNavOverlayScrollFillBound = true;

    const update = () => {
      const navs = (root || document).querySelectorAll
        ? (root || document).querySelectorAll('nav.nx-nav-core-demo[data-nx-scrollfill="1"], nav.nx-nav-core-demo[data-nx-overlay="1"]')
        : [];
      // vorherige Push-Klasse entfernen
      try {
        const prev = document.querySelectorAll(".nx-overlay-push");
        prev.forEach((el) => el.classList.remove("nx-overlay-push"));
      } catch (e) {}
      navs.forEach((nav) => {
        const overlay = nav.getAttribute("data-nx-overlay") === "1";
        const scrollFill = nav.getAttribute("data-nx-scrollfill") === "1";
        const offsetRaw = nav.getAttribute("data-nx-scrollfill-offset") || "80";
        const offset = /^[0-9]+$/.test(offsetRaw) ? parseInt(offsetRaw, 10) : 80;
        const filledShadow = nav.getAttribute("data-nx-filled-shadow") || "";
        const liveItem = nav.closest ? nav.closest(".nx-live-item") : null;

        if (overlay) nav.classList.add("nx-nav-overlay");
        else nav.classList.remove("nx-nav-overlay", "nx-nav-filled");

        // Abstand zum ersten Text (Builder): Navbar-Höhe + Zusatzabstand
        if (overlay) {
          try {
            const navH = nav.getBoundingClientRect().height || 0;
            const h = navH + 28;
            document.documentElement.style.setProperty("--nx-overlay-safe-top", h + "px");
            // Overlay soll "über" dem Header liegen (Header nicht als Block nach unten schieben).
            // Safe-Top lösen wir über CSS am Header-Text, nicht über padding-top am Header-Block.
            const hero = document.querySelector(".nx-hero, .nx-hero-split");
            if (hero) hero.classList.add("nx-overlay-push");

            // Marker für CSS: mindestens eine Overlay-Nav ist aktiv
            try {
              document.documentElement.classList.add("nx-has-overlay-nav");
            } catch (e) {}

            // Builder-Overlay: Nav soll über dem NÄCHSTEN Block (z.B. Header) liegen,
            // ohne position:fixed (das lässt die Nav im Builder "verschwinden").
            if (liveItem) {
              liveItem.classList.add("nx-builder-overlay-nav");
              liveItem.style.position = "relative";
              liveItem.style.zIndex = "50";
              // Nächsten Block hinter die Nav ziehen
              if (navH > 0) {
                liveItem.style.marginBottom = "-" + Math.round(navH) + "px";
              }
              // Content-Zone darf überlappen
              const zone = liveItem.closest ? liveItem.closest('[data-nx-zone="content"]') : null;
              if (zone) zone.style.overflow = "visible";
            }

            // WICHTIG (Builder): Header-Textlayer IMMER unter die Nav schieben (Padding),
            // unabhängig davon, wo der Header im DOM steht.
            try {
              document
                .querySelectorAll('header.nx-header .nx-header-image > .position-absolute.d-flex')
                .forEach((layer) => {
                  layer.style.justifyContent = "flex-start";
                  layer.style.paddingTop = "calc(var(--nx-overlay-safe-top, 96px) + 3rem)";
                });
            } catch (e) {}
          } catch (e) {}
        } else {
          // Overlay aus: Overlap-Styling zurücksetzen
          if (liveItem && liveItem.classList.contains("nx-builder-overlay-nav")) {
            liveItem.classList.remove("nx-builder-overlay-nav");
            liveItem.style.marginBottom = "";
            liveItem.style.zIndex = "";
            liveItem.style.position = "";
          }
          try {
            document.documentElement.classList.remove("nx-has-overlay-nav");
          } catch (e) {}

          // Builder: Header-Textlayer Padding zurücksetzen
          try {
            document
              .querySelectorAll('header.nx-header .nx-header-image > .position-absolute.d-flex')
              .forEach((layer) => {
                layer.style.justifyContent = "";
                layer.style.paddingTop = "";
              });
          } catch (e) {}
        }

        if (!scrollFill) {
          // Wichtig: Overlay ohne ScrollFill soll NICHT weiß gefüllt sein.
          // (Standalone-Navbar ohne ScrollFill bleibt "sofort gefüllt" wie bisher.)
          if (overlay) {
            nav.style.setProperty("--nx-overlay-progress", "0");
            nav.classList.remove("nx-nav-filled");
            nav.classList.remove("shadow-sm", "shadow", "shadow-lg");
            if (filledShadow) nav.classList.remove(filledShadow);
          } else {
            // Ohne "Füllen nach scroll" soll sofort gefüllt sein.
            nav.style.setProperty("--nx-overlay-progress", "1");
            nav.classList.add("nx-nav-filled");
            nav.classList.remove("shadow-sm", "shadow", "shadow-lg");
            if (filledShadow) nav.classList.remove(filledShadow);
            if (filledShadow) nav.classList.add(filledShadow);
          }
          return;
        }

        const y = window.scrollY || document.documentElement.scrollTop || 0;
        const shouldFill = y >= offset;
        nav.style.setProperty("--nx-overlay-progress", shouldFill ? "1" : "0");

        // Shadow-Classes immer zuerst bereinigen (sonst bleibt ein Shadow "stehen")
        nav.classList.remove("shadow-sm", "shadow", "shadow-lg");
        if (filledShadow) nav.classList.remove(filledShadow);

        if (shouldFill) {
          nav.classList.add("nx-nav-filled");
          if (filledShadow) nav.classList.add(filledShadow);
        } else {
          nav.classList.remove("nx-nav-filled");
        }
      });
    };

    window.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    setTimeout(update, 0);
  }

  function bindPalette() {
    palList = document.getElementById("nx-pal-categories");
    palPanel = document.getElementById("nx-palette");
    btnToggle = document.getElementById("nx-toggle-palette");
    if (!palList || !palPanel) return;
    const vis = (localStorage.getItem(PAL_VISIBLE_KEY) ?? "1") === "1";
    setPaletteVisible(vis, false);

    // Template-Modus: Palette-Kategorien als einfache Buttons aufbauen
    if (TEMPLATE_MODE) {
      const tpl = TEMPLATES[CURRENT_TEMPLATE_ID];
      if (tpl && tpl.categories && Array.isArray(tpl.categories)) {
        palList.innerHTML = "";
        tpl.categories.forEach((cat, index) => {
          const wrapper = document.createElement("div");
          wrapper.className = "nx-pal-category border-bottom";
          const btn = document.createElement("button");
          btn.type = "button";
          btn.className =
            "nx-pal-category-head w-100 text-start d-flex align-items-center gap-2 py-3 px-4 border-0 bg-transparent";
          btn.setAttribute("data-tpl-cat", cat.id);
          btn.innerHTML =
            '<span class="fw-semibold small">' +
            (cat.label || cat.id) +
            "</span>" +
            '<span class="badge rounded-pill text-bg-light text-dark ms-auto">' +
            String(
              (tpl.blocks &&
                tpl.blocks[cat.id] &&
                tpl.blocks[cat.id].length) ||
                0
            ) +
            "</span>";
          // Kein automatischer Active-State – wird nur gesetzt, wenn Vorlagen-Sidebar wirklich offen ist
          wrapper.appendChild(btn);
          palList.appendChild(wrapper);
        });
      }

      // Hover auf Kategorie: passende Vorlagen rechts anzeigen
      ensureExamplesPanel();
      palPanel
        .querySelectorAll(".nx-pal-category-head[data-tpl-cat]")
        .forEach((btn) => {
          btn.addEventListener("mouseenter", () => {
            const catId = btn.getAttribute("data-tpl-cat") || "";
            if (!catId) return;
            palPanel
              .querySelectorAll(".nx-pal-category-head[data-tpl-cat]")
              .forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            const label =
              btn.textContent.trim() ||
              btn.getAttribute("data-pal-title") ||
              catId;
            cancelHideExamples();
            renderExamplesForTemplateCategory(
              CURRENT_TEMPLATE_ID,
              catId,
              label
            );
          });
        });

      if (examplesPanel && palPanel) {
        examplesPanel.addEventListener("mouseenter", cancelHideExamples);
        examplesPanel.addEventListener("mouseleave", hideExamplesPanelSoon);
        palPanel.addEventListener("mouseleave", hideExamplesPanelSoon);
      }

      // Suche in den Kategorienamen
      const searchInput = document.getElementById("nx-pal-search");
      if (searchInput && !searchInput._nxBound) {
        searchInput._nxBound = true;
        searchInput.addEventListener("input", function () {
          const term = (searchInput.value || "").toLowerCase().trim();
          const cats = palPanel.querySelectorAll(".nx-pal-category");
          cats.forEach((catEl) => {
            const btn = catEl.querySelector(
              ".nx-pal-category-head[data-tpl-cat]"
            );
            if (!btn) {
              catEl.style.display = term ? "none" : "";
              return;
            }
            const title = (btn.textContent || "").toLowerCase();
            const match = !term || title.indexOf(term) !== -1;
            catEl.style.display = match ? "" : "none";
          });
        });
      }

      return;
    }

    // Standardmodus (ohne Templates): ursprüngliche Palette + PRESETS-Hover

    var palOpts = {
      group: { name: "nx-builder", pull: "clone", put: false },
      sort: false,
      animation: 150,
      ghostClass: "ghost",
      // Ebenfalls HTML5-Drag, damit sich alles gleich anfühlt
      fallbackOnBody: false,
      forceFallback: false,
      onStart() {
        document.body.classList.add("nx-dragging");
        nxClearAllMarks();
      },
      onMove: function (evt) {
        if (!evt.to) return true;
        // Palette-Widgets dürfen grundsätzlich überall hin gezogen werden – keine Blockierung
        nxMark(evt.to, true);
        return true;
      },
      onEnd() {
        document.body.classList.remove("nx-dragging");
        nxClearAllMarks();
      },
    };
    palList.querySelectorAll(".nx-pal-list").forEach(function (listEl) {
      new Sortable(listEl, Object.assign({}, palOpts, {
        draggable: ".nx-pal-item",
        handle: ".nx-pal-handle, .nx-pal-item",
      }));
    });

    // Hover auf Palette: passende Beispielvorlagen im Examples-Panel zeigen
    ensureExamplesPanel();
    if (palPanel) {
      palPanel.querySelectorAll(".nx-pal-item").forEach((item) => {
        item.addEventListener("mouseenter", () => {
          const key =
            item.getAttribute("data-pal-key") ||
            item.getAttribute("data-nx-key") ||
            "";
          if (!key || !PRESETS[key]) return;
          const title =
            item.getAttribute("data-pal-title") ||
            item.getAttribute("data-nx-title") ||
            key;
          cancelHideExamples();
          renderExamplesForKey(key, title);
        });
      });
    }
    if (examplesPanel && palPanel) {
      examplesPanel.addEventListener("mouseenter", cancelHideExamples);
      examplesPanel.addEventListener("mouseleave", hideExamplesPanelSoon);
      palPanel.addEventListener("mouseleave", hideExamplesPanelSoon);
    }

    // Suche in der Palette
    const searchInput = document.getElementById("nx-pal-search");
    if (searchInput && !searchInput._nxBound) {
      searchInput._nxBound = true;
      searchInput.addEventListener("input", function () {
        const term = (searchInput.value || "").toLowerCase().trim();
        const cats = palPanel.querySelectorAll(".nx-pal-category");
        cats.forEach((cat) => {
          let visibleCount = 0;
          cat.querySelectorAll(".nx-pal-item").forEach((item) => {
            const title = (item.getAttribute("data-pal-title") || item.getAttribute("data-nx-title") || "").toLowerCase();
            const key = (item.getAttribute("data-pal-key") || item.getAttribute("data-nx-key") || "").toLowerCase();
            const match = !term || title.indexOf(term) !== -1 || key.indexOf(term) !== -1;
            item.style.display = match ? "" : "none";
            if (match) visibleCount++;
          });
          const badge = cat.querySelector(".nx-pal-category-head .badge");
          if (badge) badge.textContent = String(visibleCount);
          cat.style.display = visibleCount > 0 ? "" : "none";
        });
      });
    }

    btnToggle?.addEventListener("click", () => {
      const next = !(btnToggle.getAttribute("aria-expanded") === "true");
      setPaletteVisible(next, true);
    });

  }

  // Basis-Design: Klick in Capture-Phase abfangen (Delegation), damit nichts anderes den Klick schluckt
  document.addEventListener(
    "click",
    function (e) {
      var el = e.target && e.target.closest ? e.target.closest(".nx-pal-global") : null;
      if (!el) return;
      e.preventDefault();
      e.stopPropagation();
      if (typeof openGlobalOptikSidebar === "function") openGlobalOptikSidebar();
    },
    true
  );
  document.addEventListener(
    "keydown",
    function (e) {
      if (e.key !== "Enter" && e.key !== " ") return;
      var el = e.target && e.target.closest ? e.target.closest(".nx-pal-global") : null;
      if (!el) return;
      e.preventDefault();
      if (typeof openGlobalOptikSidebar === "function") openGlobalOptikSidebar();
    },
    true
  );

  function setPaletteVisible(v, persist) {
    if (!palPanel) return;
    if (v) {
      palPanel.classList.remove("is-hidden");
      btnToggle?.setAttribute("aria-expanded", "true");
      document.body.classList.remove("nx-palette-hidden");
    } else {
      palPanel.classList.add("is-hidden");
      btnToggle?.setAttribute("aria-expanded", "false");
      document.body.classList.add("nx-palette-hidden");
    }
    if (persist)
      try {
        localStorage.setItem(PAL_VISIBLE_KEY, v ? "1" : "0");
      } catch (e) {}
  }

  function ensureIconPicker() {
    if (iconPickerEl) return;

    // einfache Styles für Icon-Picker injizieren
    if (!iconPickerInitialized) {
      const styleEl = document.createElement("style");
      styleEl.textContent = `
      .nx-icon-picker-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.45);
        z-index: 2147483000;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .nx-icon-picker {
        background: #fff;
        border-radius: .75rem;
        box-shadow: 0 20px 45px rgba(15,23,42,.35);
        max-width: 520px;
        width: 100%;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
      }
      .nx-icon-picker-header {
        padding: .75rem 1rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: .5rem;
        justify-content: space-between;
      }
      .nx-icon-picker-body {
        padding: .75rem 1rem 1rem;
        overflow: auto;
      }
      .nx-icon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
        gap: .5rem;
      }
      .nx-icon-btn {
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        padding: .35rem .25rem;
        background: #fff;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .15rem;
        font-size: .7rem;
        text-align: center;
        transition: border-color .12s ease, box-shadow .12s ease, transform .08s ease;
      }
      .nx-icon-btn:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13,110,253,.15);
        transform: translateY(-1px);
      }
      .nx-icon-btn i {
        font-size: 1.2rem;
      }
      `;
      document.head.appendChild(styleEl);
      iconPickerInitialized = true;
    }

    const backdrop = document.createElement("div");
    backdrop.className = "nx-icon-picker-backdrop";

    const picker = document.createElement("div");
    picker.className = "nx-icon-picker";

    const header = document.createElement("div");
    header.className = "nx-icon-picker-header";
    header.innerHTML = `
      <div class="d-flex flex-column">
        <strong class="small">Icon auswählen</strong>
        <span class="small text-muted">Bootstrap Icons – Klick zum Übernehmen</span>
      </div>
      <button type="button" class="btn btn-sm btn-outline-secondary nx-icon-picker-close">Schließen</button>
    `;

    const body = document.createElement("div");
    body.className = "nx-icon-picker-body";

    const searchWrap = document.createElement("div");
    searchWrap.className = "mb-2";
    searchWrap.innerHTML = `
      <input type="search" class="form-control form-control-sm" placeholder="Iconnamen filtern, z.B. star" />
    `;

    const grid = document.createElement("div");
    grid.className = "nx-icon-grid";

    ICON_LIST.forEach((name) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "nx-icon-btn";
      btn.setAttribute("data-icon-name", name);
      btn.innerHTML = `<i class="bi ${name}"></i><span>${name.replace("bi-", "")}</span>`;
      grid.appendChild(btn);
    });

    body.appendChild(searchWrap);
    body.appendChild(grid);

    picker.appendChild(header);
    picker.appendChild(body);
    backdrop.appendChild(picker);

    document.body.appendChild(backdrop);

    const close = () => {
      iconPickerTarget = null;
      if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
      iconPickerEl = null;
    };

    header
      .querySelector(".nx-icon-picker-close")
      .addEventListener("click", close);

    backdrop.addEventListener("click", (e) => {
      if (e.target === backdrop) close();
    });

    const searchInput = searchWrap.querySelector("input");
    const applyFilter = (term) => {
      const t = (term || "").toLowerCase();
      grid.querySelectorAll(".nx-icon-btn").forEach((btn) => {
        const name = (btn.getAttribute("data-icon-name") || "").toLowerCase();
        btn.style.display = name.indexOf(t) !== -1 ? "" : "none";
      });
    };
    searchInput.addEventListener("input", (e) => applyFilter(e.target.value));
    searchInput.addEventListener("keyup", (e) => applyFilter(e.target.value));

    // Suchfeld direkt fokussieren, damit man sofort tippen kann
    try {
      searchInput.focus();
      searchInput.select();
    } catch (e) {}

    grid.addEventListener("click", (e) => {
      const btn = e.target.closest(".nx-icon-btn");
      if (!btn || !iconPickerTarget) return;
      const name = btn.getAttribute("data-icon-name") || "";
      iconPickerTarget.value = name;
      iconPickerTarget.dispatchEvent(new Event("input", { bubbles: true }));
      close();
    });

    iconPickerEl = backdrop;
  }

  function openIconPicker(fieldKey) {
    if (!settingsFields) return;
    const input = settingsFields.querySelector(
      `[data-nx-field="${fieldKey}"]`
    );
    if (!input) return;
    iconPickerTarget = input;
    ensureIconPicker();
  }

  function createShellFromPalette(li) {
    const key =
      li.getAttribute("data-pal-key") ||
      li.getAttribute("data-nx-key") ||
      "widget";
    const title =
      li.getAttribute("data-pal-title") ||
      li.getAttribute("data-nx-title") ||
      key;
    // Vorkonfigurierte Settings aus einem Preset (Palette-Variante) übernehmen, falls vorhanden
    let presetSettings = {};
    const rawPreset = li.getAttribute("data-pal-settings");
    if (rawPreset) {
      try {
        const parsed = JSON.parse(rawPreset);
        if (parsed && typeof parsed === "object") {
          presetSettings = parsed;
        }
      } catch (e) {
        presetSettings = {};
      }
    }
    // Defaults: core_nav_demo soll im Header-Kontext nicht "leer" wirken
    if (key === "core_nav_demo" && presetSettings && typeof presetSettings === "object") {
      if (typeof presetSettings.overlayMode === "undefined") presetSettings.overlayMode = true;
      if (typeof presetSettings.scrollFill === "undefined") presetSettings.scrollFill = true;
    }
    li.className = "nx-live-item";
    li.removeAttribute("data-pal-key");
    li.removeAttribute("data-pal-title");
    li.removeAttribute("data-pal-settings");
    li.setAttribute("data-nx-iid", uid());
    li.setAttribute("data-nx-key", key);
    li.setAttribute("data-nx-title", title);
    try {
      li.setAttribute(
        "data-nx-settings",
        JSON.stringify(presetSettings || {})
      );
    } catch (e) {
      li.setAttribute("data-nx-settings", "{}");
    }
    const hintText = ALIGN_KEYS.indexOf(key) !== -1 ? "Klick: Ausrichtung · Doppelklick: Text bearbeiten" : "Doppelklick: Text bearbeiten";
    li.innerHTML = `<div class="nx-drag-handle" title="Ziehen zum Verschieben">⋮⋮</div>
      <div class="nx-live-controls btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-light btn-settings" title="Einstellungen"><i class="bi bi-sliders"></i></button>
        <button type="button" class="btn btn-outline-danger btn-remove" title="Entfernen"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="nx-live-content" title="Ziehen, um in eine andere Zone zu verschieben"><div class="text-muted small">Lade Widget …</div></div>
      <div class="nx-inline-hint">${hintText}</div>`;
    return li;
  }

  function bindZone(zone) {
    nxDebug("bindZone " + (zone.getAttribute("data-nx-zone") || "?"));
    if (zone._nxBound) return;
    zone._nxBound = true;
    new Sortable(zone, {
      group: { name: "nx-builder", pull: true, put: true },
      animation: 150,
      ghostClass: "ghost",
      // Einheitlich zum Vorlagen-/Paletten-Drag: nativer HTML5-Drag ohne erzwungenen Fallback
      fallbackOnBody: false,
      forceFallback: false,
      emptyInsertThreshold: 24,
      draggable: ".nx-live-item, .nx-pal-item",
      handle: ".nx-drag-handle, .nx-live-content, .nx-pal-handle, .nx-pal-item",
      filter: ".nx-live-controls, .nx-live-controls *, .nx-drop-hint",
      preventOnFilter: true,
      onStart() {
        document.body.classList.add("nx-dragging");
        nxClearAllMarks();
      },
      onMove: (evt) => {
        // Versuche, die Zone zu finden, die sich tatsächlich unter dem Mauszeiger befindet.
        // WICHTIG: Den gerade gezogenen Block kurzfristig aus dem Hit-Testing nehmen,
        // damit wir die Zone *darunter* treffen (sonst liefert elementFromPoint nur das Ghost-Element).
        const ev = evt.originalEvent || evt.event;
        if (ev && typeof ev.clientX === "number" && typeof ev.clientY === "number") {
          const dragEl = evt.dragged;
          // Sowohl Original-Element als auch evtl. Ghost-Element für das Hit-Testing kurz deaktivieren
          const ghostEl = document.querySelector(".sortable-ghost") || document.querySelector(".sortable-chosen");
          const prev = [];
          [dragEl, ghostEl].forEach((node, idx) => {
            if (node && node.style) {
              prev[idx] = node.style.pointerEvents || "";
              node.style.pointerEvents = "none";
            }
          });
          // Alle Elemente unter dem Mauszeiger holen und die *innerste* Zone wählen
          const stack = document.elementsFromPoint(ev.clientX, ev.clientY) || [];
          let el = null;
          for (let i = 0; i < stack.length; i++) {
            if (stack[i].closest) {
              el = stack[i];
              break;
            }
          }
          [dragEl, ghostEl].forEach((node, idx) => {
            if (node && node.style) {
              node.style.pointerEvents = prev[idx] || "";
            }
          });
          let hz = null;
          if (stack.length) {
            // Von innen nach außen laufen und die tiefste data-nx-zone wählen
            for (let i = stack.length - 1; i >= 0; i--) {
              const cand = stack[i];
              if (cand && cand.getAttribute && cand.hasAttribute("data-nx-zone")) {
                hz = cand;
                break;
              }
            }
          }
          // Spezialfall: große Layout-Container (core_container, core_row, core_col)
          // Wenn noch keine Zone gefunden wurde, aber wir über einem solchen Wrapper sind,
          // dann dessen innere nx-live-zone als Drop-Ziel verwenden.
          const itemHost = el && el.closest ? el.closest(".nx-live-item") : null;
          if (!hz && itemHost) {
            const key = itemHost.getAttribute("data-nx-key") || "";
            if (key === "core_container" || key === "core_row" || key === "core_col") {
              const innerZone = itemHost.querySelector(".nx-live-zone[data-nx-zone]");
              if (innerZone) hz = innerZone;
            }
          }
          if (hz) {
            lastHoverZone = hz;
          }
        }

        const targetZoneEl = (lastHoverZone && lastHoverZone.isConnected && lastHoverZone) || evt.to;
        const toZone =
          (targetZoneEl && targetZoneEl.getAttribute("data-nx-zone")) || "";
        const key =
          (evt.dragged &&
            (evt.dragged.getAttribute("data-nx-key") ||
              evt.dragged.getAttribute("data-pal-key"))) ||
          "";
        // Während des Draggen niemals blockieren – Sortable soll überall hingehen können.
        // Wir nutzen die Zone nur noch für die optische Hervorhebung.
        if (!toZone || !key) {
          nxMark(targetZoneEl || evt.to, true);
          return true;
        }
        nxMark(targetZoneEl || evt.to, true);
        return true;
      },
      onAdd: async (evt) => {
        if (evt.item && (evt.item.classList.contains("nx-drop-hint") || evt.item.classList.contains("builder-placeholder"))) {
          nxClearAllMarks();
          return;
        }
        try {
          nxDebug("bindZone onAdd start");
          // Nutze beim Drop bevorzugt die Zone, die zuletzt unter dem Mauszeiger war (feineres Einfügen in verschachtelte Zonen)
          let effectiveZoneEl =
            (lastHoverZone && lastHoverZone.isConnected && lastHoverZone) ||
            zone;
          if (evt.item && effectiveZoneEl !== evt.item.parentNode) {
            try {
              effectiveZoneEl.appendChild(evt.item);
            } catch (e) {
              // Fallback: falls append fehlschlägt, bleibt das Element dort, wo Sortable es eingefügt hat
            }
          }

          const toZone =
            (effectiveZoneEl && effectiveZoneEl.getAttribute("data-nx-zone")) ||
            "";
          const key =
            (evt.item &&
              (evt.item.getAttribute("data-nx-key") ||
                evt.item.getAttribute("data-pal-key"))) ||
            "";
          nxDebug("bindZone onAdd: key=" + key + " toZone=" + toZone);
          // Zonen-Restriktionen vorübergehend deaktiviert: keine Blockierung über
          // nxIsAllowedUniversal, damit Vorlagen und Palette-Items überall abgelegt
          // werden können.
          if (evt.item && evt.item.hasAttribute("data-pal-bundle")) {
            let bundle = null;
            try {
              bundle = JSON.parse(evt.item.getAttribute("data-pal-bundle") || "null");
            } catch (e) {
              bundle = null;
            }
            if (Array.isArray(bundle) && bundle.length) {
              const parent = effectiveZoneEl || evt.item.parentNode;
              const anchor = evt.item.nextSibling;
              const inserted = [];
              try {
                evt.item.parentNode && evt.item.parentNode.removeChild(evt.item);
              } catch (e) {}

              bundle.forEach((entry) => {
                try {
                  const node = createShellFromBundleEntry(entry);
                  if (parent) parent.insertBefore(node, anchor);
                  inserted.push(node);
                } catch (e) {}
              });

              await saveState();
              for (let i = 0; i < inserted.length; i++) {
                await renderInto(inserted[i]);
              }
              getZones().forEach(bindZone);
              if (inserted[0]) openSettingsForItem(inserted[0]);
              return;
            }
          }

          if (evt.item && evt.item.hasAttribute("data-pal-key")) {
            createShellFromPalette(evt.item);
            var palKey = evt.item.getAttribute("data-nx-key");
            if (palKey === "core_col" && toZone.indexOf("row_") === 0) {
              var s = {};
              try { s = JSON.parse(evt.item.getAttribute("data-nx-settings") || "{}"); } catch (e) {}
              if (s.span === undefined) s.span = 6;
              evt.item.setAttribute("data-nx-settings", JSON.stringify(s));
              evt.item.setAttribute("data-nx-col-span", String(s.span));
              evt.item.classList.add("col-12", "col-md-" + s.span);
            }
            // Footer-Widgets immer in der Content-Zone am Ende platzieren (auch wenn schon in content gedropt)
            if (palKey && palKey.indexOf("core_footer_") === 0) {
              const contentZone = document.querySelector('[data-nx-zone="content"]');
              if (contentZone) {
                contentZone.appendChild(evt.item);
                effectiveZoneEl = contentZone;
              }
            }
          }
          await saveState();
          nxDebug("bindZone onAdd: saveState done");
          if (evt.item) {
            await renderInto(evt.item);
            nxDebug("bindZone onAdd: renderInto done");
            getZones().forEach(bindZone);
            openSettingsForItem(evt.item);
          }
        } finally {
          ensureDropHints();
          nxClearAllMarks();
        }
      },
      onUpdate: async () => {
        try {
          await saveState();
        } catch (err) {
          console.error("Save after reorder failed", err);
        } finally {
          ensureDropHints();
        }
      },
      onEnd() {
        document.body.classList.remove("nx-dragging");
        lastHoverZone = null;
        ensureDropHints();
        nxClearAllMarks();
      },
    });
  }

  document.addEventListener("click", async (e) => {
    const iconBtn = e.target.closest(".nx-icon-picker-btn");
    if (iconBtn) {
      const fieldKey = iconBtn.getAttribute("data-icon-target");
      if (fieldKey) {
        openIconPicker(fieldKey);
      }
      e.preventDefault();
      return;
    }

    const rm = e.target.closest(".nx-live-controls .btn-remove");
    if (rm) {
      const item = rm.closest(".nx-live-item");
      const iid = item && item.getAttribute("data-nx-iid");
      if (iid) removedInstanceIds.push(iid);
      item?.parentNode?.removeChild(item);
      try {
        await saveState();
      } catch (err) {
        console.error("Save after remove failed", err);
      }
      return;
    }

    const dup = e.target.closest(".nx-live-controls .btn-duplicate");
    if (dup) {
      const item = dup.closest(".nx-live-item");
      if (!item) return;
      const clone = item.cloneNode(true);
      clone.setAttribute("data-nx-iid", uid());
      const parent = item.parentNode;
      if (parent) {
        parent.insertBefore(clone, item.nextSibling);
      }
      try {
        await saveState();
        await renderInto(clone);
      } catch (err) {
        console.error("Duplicate/render failed", err);
      }
      return;
    }
    const set = e.target.closest(".nx-live-controls .btn-settings");
    if (set) {
      const item = set.closest(".nx-live-item");
      if (item) openSettingsForItem(item);
    }
  });

  function ensureSettingsSidebar() {
    if (settingsSidebar) return;
    settingsSidebar = document.getElementById("nx-settings-sidebar");
    settingsContent = document.getElementById("nx-settings-content");
    settingsPlaceholder = document.getElementById("nx-settings-placeholder");
    settingsForm = document.getElementById("nx-settings-form");
    settingsTextarea = document.getElementById("nx-settings-json");
    settingsError = document.getElementById("nx-settings-error");
      settingsFields = document.getElementById("nx-settings-fields");
    if (settingsForm && settingsTextarea) {
      settingsForm.addEventListener("submit", async (ev) => {
        ev.preventDefault();
        if (globalOptikActive) {
          const opts = {};
          if (settingsFields) {
            settingsFields.querySelectorAll("[data-nx-theme-field]").forEach((el) => {
              const key = el.getAttribute("data-nx-theme-field");
              if (key) opts[key] = el.type === "checkbox" ? !!el.checked : (el.value || "");
            });
          }
          window._nxCurrentThemeOptions = opts;
          if (settingsError) settingsError.textContent = "";
          try {
            await saveState();
            if (settingsError) settingsError.textContent = "Gespeichert.";
            if (settingsError) settingsError.classList.remove("text-danger");
            if (settingsError) settingsError.classList.add("text-success");
            setTimeout(() => { if (settingsError) { settingsError.textContent = ""; settingsError.classList.remove("text-success"); } }, 2000);
          } catch (e) {
            if (settingsError) { settingsError.textContent = "Fehler: " + (e.message || "Speichern fehlgeschlagen"); settingsError.classList.add("text-danger"); }
          }
          return;
        }
        if (!currentSettingsItem) return;
        const raw = settingsTextarea.value || "{}";
        if (settingsError) settingsError.textContent = "";
        try {
          let obj = raw.trim() ? JSON.parse(raw) : {};
          if (settingsFields) {
            settingsFields.querySelectorAll("[data-nx-field]").forEach((el) => {
              const key = el.getAttribute("data-nx-field");
              if (!key) return;
              if (el.type === "checkbox") obj[key] = !!el.checked;
              else if (el.tagName === "SELECT") obj[key] = el.value;
              else obj[key] = el.value;
            });
          }
          // Speziell für Navigation: numerische Padding-Werte als px-Werte speichern
          const currentKey = currentSettingsItem?.getAttribute("data-nx-key") || "";
          if (currentKey === "core_nav_demo") {
            // Overlay/ScrollFill robust normalisieren, damit Header-Nav nach Speichern
            // nicht in einen inkonsistenten Zustand fällt.
            const toBool = (v, fallback = false) => {
              if (typeof v === "boolean") return v;
              if (typeof v === "number") return v === 1;
              if (typeof v === "string") {
                const s = v.trim().toLowerCase();
                if (s === "1" || s === "true" || s === "yes" || s === "on") return true;
                if (s === "0" || s === "false" || s === "no" || s === "off" || s === "") return false;
              }
              return fallback;
            };
            if (typeof obj.paddingY === "string" && /^[0-9]+$/.test(obj.paddingY)) {
              obj.paddingY = obj.paddingY + "px";
            }
            if (typeof obj.paddingX === "string" && /^[0-9]+$/.test(obj.paddingX)) {
              obj.paddingX = obj.paddingX + "px";
            }
            obj.overlayMode = toBool(obj.overlayMode, true);
            obj.scrollFill = toBool(obj.scrollFill, true);
            const ofsRaw = String(obj.scrollFillOffset ?? "").trim();
            obj.scrollFillOffset = /^[0-9]+$/.test(ofsRaw) ? parseInt(ofsRaw, 10) : 80;
            if (obj.overlayTextMode !== "dark") obj.overlayTextMode = "light";
            if (!["", "shadow-sm", "shadow", "shadow-lg"].includes(String(obj.filledShadow ?? ""))) {
              obj.filledShadow = "";
            }
          }
          if (obj.targetBlank === true) obj.target = "_blank";
          else if (obj.targetBlank === false && obj.target === "_blank") delete obj.target;
          if (Object.prototype.hasOwnProperty.call(obj, "targetBlank")) delete obj.targetBlank;
          if (settingsTextarea) settingsTextarea.value = JSON.stringify(obj, null, 2);
          currentSettingsItem.setAttribute("data-nx-settings", JSON.stringify(obj));
          if (currentKey === "core_col") {
            var pz = currentSettingsItem.closest("[data-nx-zone]");
            if (pz && (pz.getAttribute("data-nx-zone") || "").indexOf("row_") === 0) {
              var span = Math.max(1, Math.min(12, parseInt(obj.span, 10) || 6));
              currentSettingsItem.setAttribute("data-nx-col-span", String(span));
              Array.from(currentSettingsItem.classList).forEach(function (c) { if (c.indexOf("col-md-") === 0) currentSettingsItem.classList.remove(c); });
              currentSettingsItem.classList.add("col-12", "col-md-" + span);
            }
          }
          await saveState();
          // Navigation: bei menuSource "plugin" neu rendern, damit Plugin-Menü im Canvas erscheint; sonst kein renderInto (Gear-Icons bleiben).
          if (currentKey !== "core_nav_demo") {
            await renderInto(currentSettingsItem);
          } else if (currentKey === "core_nav_demo" && (obj.menuSource === "plugin")) {
            await renderInto(currentSettingsItem);
          }
        } catch (err) {
          if (settingsError) settingsError.textContent = "Ungültiges JSON: " + err.message;
          else alert("Ungültiges JSON: " + err.message);
        }
      });
    }
    if (!window._nxImgDropBound) {
      window._nxImgDropBound = true;
      document.addEventListener("dragover", function (e) {
        var zone = e.target.closest("#nx-settings-sidebar .nx-img-drop-zone");
        if (!zone || !e.dataTransfer.types.includes("Files")) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = "copy";
        document.querySelectorAll("#nx-settings-sidebar .nx-img-drop-zone.nx-drag-over").forEach(function (z) { z.classList.remove("nx-drag-over"); });
        zone.classList.add("nx-drag-over");
      }, false);
      document.addEventListener("dragleave", function (e) {
        document.querySelectorAll(".nx-img-drop-zone.nx-drag-over").forEach(function (z) {
          if (!e.relatedTarget || !z.contains(e.relatedTarget)) z.classList.remove("nx-drag-over");
        });
      }, false);
      document.addEventListener("drop", async function (e) {
        var zone = e.target.closest("#nx-settings-sidebar .nx-img-drop-zone");
        if (!zone) return;
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove("nx-drag-over");
        var file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (!file || !file.type.startsWith("image/")) return;
        var input = zone.querySelector("input[data-nx-field]");
        if (!input || !currentSettingsItem) return;
        var vars = window.NXB_BUILDER_VARS || {};
        var uploadUrl = (vars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
        var fd = new FormData();
        fd.append("file", file);
        fd.append("csrf", vars.CSRF || "");
        try {
          var r = await fetch(uploadUrl, { method: "POST", body: fd, credentials: "same-origin" });
          var data = await r.json();
          if (data && data.ok && data.url) {
            input.value = data.url;
            var obj = {};
            if (currentSettingsItem) {
              try { obj = JSON.parse(currentSettingsItem.getAttribute("data-nx-settings") || "{}"); } catch (e) {}
            }
            if (settingsFields) {
              settingsFields.querySelectorAll("[data-nx-field]").forEach(function (el) {
                var key = el.getAttribute("data-nx-field");
                if (!key) return;
                if (el.type === "checkbox") obj[key] = !!el.checked;
                else if (el.tagName === "SELECT") obj[key] = el.value;
                else obj[key] = el.value;
              });
            }
            if (obj.targetBlank === true) obj.target = "_blank";
            else if (obj.targetBlank === false && obj.target === "_blank") delete obj.target;
            if (Object.prototype.hasOwnProperty.call(obj, "targetBlank")) delete obj.targetBlank;
            currentSettingsItem.setAttribute("data-nx-settings", JSON.stringify(obj));
            if (settingsTextarea) settingsTextarea.value = JSON.stringify(obj, null, 2);
            await saveState();
            const key = currentSettingsItem.getAttribute("data-nx-key") || "";
            if (key !== "core_nav_demo") await renderInto(currentSettingsItem);
          }
        } catch (err) { console.error("Image drop upload failed", err); }
      }, false);
    }
    if (!window._nxImgClickBound) {
      window._nxImgClickBound = true;
      var sidebar = document.getElementById("nx-settings-sidebar");
      var fileInput = document.createElement("input");
      fileInput.type = "file";
      fileInput.accept = "image/jpeg,image/png,image/webp,image/gif";
      fileInput.className = "d-none";
      if (sidebar) sidebar.appendChild(fileInput);
      var currentFieldInput = null;
      fileInput.addEventListener("change", async function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file || !file.type.startsWith("image/") || !currentFieldInput || !currentSettingsItem) {
          fileInput.value = "";
          return;
        }
        var vars = window.NXB_BUILDER_VARS || {};
        var uploadUrl = (vars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
        var fd = new FormData();
        fd.append("file", file);
        fd.append("csrf", vars.CSRF || "");
        try {
          var r = await fetch(uploadUrl, { method: "POST", body: fd, credentials: "same-origin" });
          var data = await r.json();
          if (data && data.ok && data.url) {
            currentFieldInput.value = data.url;
            var obj = {};
            if (currentSettingsItem) {
              try { obj = JSON.parse(currentSettingsItem.getAttribute("data-nx-settings") || "{}"); } catch (e) {}
            }
            if (settingsFields) {
              settingsFields.querySelectorAll("[data-nx-field]").forEach(function (el) {
                var key = el.getAttribute("data-nx-field");
                if (!key) return;
                if (el.type === "checkbox") obj[key] = !!el.checked;
                else if (el.tagName === "SELECT") obj[key] = el.value;
                else obj[key] = el.value;
              });
            }
            if (obj.targetBlank === true) obj.target = "_blank";
            else if (obj.targetBlank === false && obj.target === "_blank") delete obj.target;
            if (Object.prototype.hasOwnProperty.call(obj, "targetBlank")) delete obj.targetBlank;
            currentSettingsItem.setAttribute("data-nx-settings", JSON.stringify(obj));
            if (settingsTextarea) settingsTextarea.value = JSON.stringify(obj, null, 2);
            await saveState();
            const key = currentSettingsItem.getAttribute("data-nx-key") || "";
            if (key !== "core_nav_demo") await renderInto(currentSettingsItem);
          }
        } catch (err) {
          console.error("Image click upload failed", err);
        } finally {
          fileInput.value = "";
        }
      });
      document.addEventListener("dblclick", function (e) {
        var zone = e.target.closest("#nx-settings-sidebar .nx-img-drop-zone");
        if (!zone || !currentSettingsItem) return;
        if (e.target.closest("button")) return;
        var input = zone.querySelector("input[data-nx-field]");
        if (!input) return;
        e.preventDefault();
        e.stopPropagation();
        currentFieldInput = input;
        fileInput.click();
      }, false);
    }
  }

  function nxParseColor(str) {
    if (!str || typeof str !== "string") return null;
    str = str.trim();
    var m = str.match(/^#([0-9a-fA-F]{3})$/);
    if (m) {
      var s = m[1];
      return { r: parseInt(s[0] + s[0], 16), g: parseInt(s[1] + s[1], 16), b: parseInt(s[2] + s[2], 16), a: 1 };
    }
    m = str.match(/^#?([0-9a-fA-F]{6})$/);
    if (m) {
      var hex = m[1];
      return { r: parseInt(hex.slice(0, 2), 16), g: parseInt(hex.slice(2, 4), 16), b: parseInt(hex.slice(4, 6), 16), a: 1 };
    }
    m = str.match(/^rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/);
    if (m) {
      var a = m[4] != null ? parseFloat(m[4]) : 1;
      if (a > 1) a = a / 100;
      return { r: Math.max(0, Math.min(255, parseInt(m[1], 10))), g: Math.max(0, Math.min(255, parseInt(m[2], 10))), b: Math.max(0, Math.min(255, parseInt(m[3], 10))), a: Math.max(0, Math.min(1, a)) };
    }
    return null;
  }
  function nxToHex(r, g, b) {
    return "#" + [r, g, b].map(function (x) { var h = Math.max(0, Math.min(255, Math.round(x))).toString(16); return h.length === 1 ? "0" + h : h; }).join("");
  }
  function nxToRgba(r, g, b, a) {
    var R = Math.max(0, Math.min(255, Math.round(r)));
    var G = Math.max(0, Math.min(255, Math.round(g)));
    var B = Math.max(0, Math.min(255, Math.round(b)));
    var A = a == null || a === "" ? 1 : Math.max(0, Math.min(1, parseFloat(a)));
    return "rgba(" + R + "," + G + "," + B + "," + (A < 1 ? String(A) : "1") + ")";
  }
  function nxRgbToHsv(r, g, b) {
    r /= 255; g /= 255; b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b), d = max - min, h, s, v = max;
    if (d === 0) h = 0; else { s = v ? d / v : 0; switch (max) { case r: h = (g - b) / d + (g < b ? 6 : 0); break; case g: h = (b - r) / d + 2; break; default: h = (r - g) / d + 4; } h /= 6; }
    return { h: h * 360, s: (v ? d / v : 0) * 100, v: v * 100 };
  }
  function nxHsvToRgb(h, s, v) {
    h = (h % 360 + 360) % 360 / 60; s /= 100; v /= 100;
    var c = v * s, x = c * (1 - Math.abs((h % 2) - 1)), m = v - c, r = 0, g = 0, b = 0;
    if (h < 1) { r = c; g = x; } else if (h < 2) { r = x; g = c; } else if (h < 3) { g = c; b = x; } else if (h < 4) { g = x; b = c; } else if (h < 5) { r = x; b = c; } else { r = c; b = x; }
    return { r: Math.round((r + m) * 255), g: Math.round((g + m) * 255), b: Math.round((b + m) * 255) };
  }

  var nxColorPickerEl = null;
  function nxShowColorPicker(anchor, textInput, rIn, gIn, bIn, aIn) {
    var parsed = nxParseColor(textInput.value);
    var r = parsed ? parsed.r : 0, g = parsed ? parsed.g : 0, b = parsed ? parsed.b : 0, a = parsed ? parsed.a : 1;
    var hsv = nxRgbToHsv(r, g, b);
    var state = { h: hsv.h, s: hsv.s, v: hsv.v, a: a };

    if (!nxColorPickerEl) {
      nxColorPickerEl = document.createElement("div");
      nxColorPickerEl.className = "nx-cp-overlay";
      nxColorPickerEl.style.display = "none";
      nxColorPickerEl.innerHTML = '<div class="nx-cp-popup"><div class="nx-cp-header">' +
        '<button type="button" class="nx-cp-btn nx-cp-btn-cancel" title="Abbrechen" aria-label="Abbrechen"><i class="bi bi-x-lg"></i></button>' +
        '<div class="nx-cp-hex-wrap"><input type="text" class="nx-cp-hex-input" placeholder="#000000" maxlength="9" spellcheck="false"></div>' +
        '<button type="button" class="nx-cp-btn nx-cp-btn-apply" title="Übernehmen" aria-label="Übernehmen"><i class="bi bi-check-lg"></i></button></div>' +
        '<div class="nx-cp-sv-wrap"><div class="nx-cp-sv-inner"></div><div class="nx-cp-sv-cursor"></div></div>' +
        '<div class="nx-cp-strip-wrap nx-cp-hue"><div class="nx-cp-strip-cursor"></div></div>' +
        '<div class="nx-cp-strip-wrap nx-cp-alpha-wrap"><div class="nx-cp-alpha-inner"></div><div class="nx-cp-strip-cursor"></div></div></div>';
      document.body.appendChild(nxColorPickerEl);
    }
    var popup = nxColorPickerEl.querySelector(".nx-cp-popup");
    var hexInput = nxColorPickerEl.querySelector(".nx-cp-hex-input");
    var svWrap = nxColorPickerEl.querySelector(".nx-cp-sv-wrap");
    var svInner = nxColorPickerEl.querySelector(".nx-cp-sv-inner");
    var svCursor = nxColorPickerEl.querySelector(".nx-cp-sv-wrap .nx-cp-sv-cursor");
    var hueWrap = nxColorPickerEl.querySelector(".nx-cp-hue");
    var hueCursor = nxColorPickerEl.querySelector(".nx-cp-hue .nx-cp-strip-cursor");
    var alphaWrap = nxColorPickerEl.querySelector(".nx-cp-alpha-wrap");
    var alphaInner = nxColorPickerEl.querySelector(".nx-cp-alpha-inner");
    var alphaCursor = nxColorPickerEl.querySelector(".nx-cp-alpha-wrap .nx-cp-strip-cursor");

    function updateFromState() {
      var rgb = nxHsvToRgb(state.h, state.s, state.v);
      var hex = nxToHex(rgb.r, rgb.g, rgb.b);
      hexInput.value = state.a < 1 ? nxToRgba(rgb.r, rgb.g, rgb.b, state.a) : hex;
      svInner.style.setProperty("--nx-cp-h", String(state.h));
      alphaInner.style.setProperty("--nx-cp-alpha-color", hex);
      var svRect = svWrap.getBoundingClientRect();
      var svW = svRect.width, svH = svRect.height;
      svCursor.style.left = (state.s / 100) * svW + "px";
      svCursor.style.top = (1 - state.v / 100) * svH + "px";
      hueCursor.style.left = (state.h / 360) * hueWrap.offsetWidth + "px";
      alphaCursor.style.left = (state.a * alphaWrap.offsetWidth) + "px";
      if (rIn) { if (rIn.value !== undefined) rIn.value = String(rgb.r); else rIn.textContent = String(rgb.r); }
      if (gIn) { if (gIn.value !== undefined) gIn.value = String(rgb.g); else gIn.textContent = String(rgb.g); }
      if (bIn) { if (bIn.value !== undefined) bIn.value = String(rgb.b); else bIn.textContent = String(rgb.b); }
      if (aIn) { var aVal = state.a >= 1 ? "100" : String(Math.round(state.a * 100)); if (aIn.value !== undefined) aIn.value = aVal; else aIn.textContent = aVal; }
    }
    function setRgbaVal(el, v) {
      if (!el) return;
      if (el.value !== undefined) el.value = String(v); else el.textContent = String(v);
    }
    function commit() {
      var rgb = nxHsvToRgb(state.h, state.s, state.v);
      textInput.value = state.a < 1 ? nxToRgba(rgb.r, rgb.g, rgb.b, state.a) : nxToHex(rgb.r, rgb.g, rgb.b);
      setRgbaVal(rIn, rgb.r);
      setRgbaVal(gIn, rgb.g);
      setRgbaVal(bIn, rgb.b);
      setRgbaVal(aIn, state.a >= 1 ? "100" : Math.round(state.a * 100));
      var wrap = anchor.closest(".nx-theme-color-wrap");
      var swatch = wrap && wrap.querySelector(".nx-color-swatch");
      if (swatch) {
        swatch.style.backgroundColor = nxToHex(rgb.r, rgb.g, rgb.b);
        if (wrap.getAttribute("data-nx-theme-color-key") === "theme_link_hover_color") {
          swatch.classList.remove("nx-swatch-no-color");
          swatch.style.color = "";
          var xEl = swatch.querySelector(".nx-swatch-x");
          if (xEl) xEl.remove();
        }
      }
      nxColorPickerEl.style.display = "none";
    }
    function dragStrip(stripEl, ev, setValue) {
      function move(e) {
        var r = stripEl.getBoundingClientRect();
        var x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
        setValue(Math.max(0, Math.min(1, x / r.width)));
        updateFromState();
      }
      function stop() { document.removeEventListener("mousemove", move); document.removeEventListener("mouseup", stop); document.removeEventListener("touchmove", move); document.removeEventListener("touchend", stop); }
      document.addEventListener("mousemove", move);
      document.addEventListener("mouseup", stop);
      document.addEventListener("touchmove", move, { passive: true });
      document.addEventListener("touchend", stop);
      move(ev);
    }
    svInner.addEventListener("mousedown", function (e) {
      e.preventDefault();
      var rect = svWrap.getBoundingClientRect();
      var w = rect.width, h = rect.height;
      function move(e) {
        var x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
        var y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
        state.s = Math.max(0, Math.min(100, (x / w) * 100));
        state.v = Math.max(0, Math.min(100, 100 - (y / h) * 100));
        updateFromState();
      }
      function stop() { document.removeEventListener("mousemove", move); document.removeEventListener("mouseup", stop); document.removeEventListener("touchmove", move); document.removeEventListener("touchend", stop); }
      document.addEventListener("mousemove", move);
      document.addEventListener("mouseup", stop);
      document.addEventListener("touchmove", move, { passive: true });
      document.addEventListener("touchend", stop);
      move(e);
    });
    hueWrap.addEventListener("mousedown", function (e) {
      e.preventDefault();
      dragStrip(hueWrap, e, function (v) { state.h = v * 360; });
    });
    alphaWrap.addEventListener("mousedown", function (e) {
      e.preventDefault();
      dragStrip(alphaWrap, e, function (v) { state.a = v; });
    });
    hexInput.addEventListener("input", function () {
      var p = nxParseColor(hexInput.value);
      if (p) { var hsv2 = nxRgbToHsv(p.r, p.g, p.b); state.h = hsv2.h; state.s = hsv2.s; state.v = hsv2.v; state.a = p.a; updateFromState(); }
    });
    nxColorPickerEl.querySelector(".nx-cp-btn-cancel").onclick = function () { nxColorPickerEl.style.display = "none"; };
    nxColorPickerEl.querySelector(".nx-cp-btn-apply").onclick = commit;
    nxColorPickerEl.onclick = function (e) { if (e.target === nxColorPickerEl) nxColorPickerEl.style.display = "none"; };
    popup.onclick = function (e) { e.stopPropagation(); };
    updateFromState();
    nxColorPickerEl.style.display = "flex";
    hexInput.focus();
  }

  function nxInitThemeColorPickers(container) {
    if (!container) return;
    container.querySelectorAll(".nx-theme-color-wrap").forEach(function (wrap) {
      var key = wrap.getAttribute("data-nx-theme-color-key");
      var isLinkHover = (key === "theme_link_hover_color");
      var textInput = wrap.querySelector('input[data-nx-theme-field]');
      var swatch = wrap.querySelector(".nx-color-swatch");
      var rIn = wrap.querySelector(".nx-rgba-r");
      var gIn = wrap.querySelector(".nx-rgba-g");
      var bIn = wrap.querySelector(".nx-rgba-b");
      var aIn = wrap.querySelector(".nx-rgba-a");
      if (!textInput) return;
      function setRgbaVal(el, v) {
        if (!el) return;
        if (el.value !== undefined) el.value = String(v); else el.textContent = String(v);
      }
      function setSwatchEmpty() {
        if (!swatch || !isLinkHover) return;
        swatch.classList.add("nx-swatch-no-color");
        swatch.style.backgroundColor = "#e9ecef";
        swatch.style.color = "#6c757d";
        if (!swatch.querySelector(".nx-swatch-x")) {
          var x = document.createElement("span");
          x.className = "nx-swatch-x";
          x.textContent = "×";
          swatch.appendChild(x);
        }
      }
      function setSwatchFilled(hexOrRgb) {
        if (!swatch) return;
        if (isLinkHover) {
          swatch.classList.remove("nx-swatch-no-color");
          swatch.style.color = "";
          var xEl = swatch.querySelector(".nx-swatch-x");
          if (xEl) xEl.remove();
        }
        swatch.style.backgroundColor = hexOrRgb;
      }
      function applyFromParsed(parsed) {
        if (!parsed) {
          if (isLinkHover) setSwatchEmpty(); else if (swatch) swatch.style.backgroundColor = "#808080";
          return;
        }
        setRgbaVal(rIn, parsed.r);
        setRgbaVal(gIn, parsed.g);
        setRgbaVal(bIn, parsed.b);
        setRgbaVal(aIn, parsed.a >= 1 ? "100" : Math.round(parsed.a * 100));
        setSwatchFilled(nxToHex(parsed.r, parsed.g, parsed.b));
      }
      function applyFromText() {
        var raw = (textInput.value && textInput.value.trim()) || "";
        var parsed = nxParseColor(raw);
        if (parsed) applyFromParsed(parsed); else applyFromParsed(null);
      }
      function getInputValue() {
        var v = textInput.value;
        if (v != null && String(v).trim() !== "") return String(v).trim();
        v = textInput.getAttribute("value");
        return (v != null && String(v).trim() !== "") ? String(v).trim() : "";
      }
      var rawInit = getInputValue();
      var initial = nxParseColor(rawInit);
      if (initial) {
        applyFromParsed(initial);
      } else {
        if (isLinkHover) setSwatchEmpty(); else if (swatch) swatch.style.backgroundColor = "#808080";
      }
      if (swatch) {
        swatch.addEventListener("click", function (e) {
          e.preventDefault();
          nxShowColorPicker(wrap, textInput, rIn, gIn, bIn, aIn);
        });
      }
      textInput.addEventListener("input", function () { applyFromText(); });
      textInput.addEventListener("change", function () { applyFromText(); });
    });
  }

  function setGlobalOptikButtonActive(active) {
    var btn = document.querySelector(".nx-pal-global");
    if (btn) {
      if (active) btn.classList.add("nx-global-active"); else btn.classList.remove("nx-global-active");
    }
  }

  function openGlobalOptikSidebar() {
    globalOptikActive = true;
    setGlobalOptikButtonActive(true);
    if (currentSettingsItem) {
      currentSettingsItem.classList.remove("nx-live-active");
      currentSettingsItem = null;
    }
    document.querySelectorAll(".nx-live-item").forEach((el) => el.classList.remove("nx-live-active"));
    ensureSettingsSidebar();
    const labelEl = document.getElementById("nx-settings-label");
    if (labelEl) labelEl.textContent = "Basis-Design";
    const opts = (window.NXB_BUILDER_VARS && window.NXB_BUILDER_VARS.THEME_OPTIONS) ? window.NXB_BUILDER_VARS.THEME_OPTIONS : {};
    if (typeof opts === "object" && !Array.isArray(opts)) {
      window._nxCurrentThemeOptions = Object.assign({}, opts);
    } else {
      window._nxCurrentThemeOptions = {};
    }
    const fields = [
      { key: "theme_bg_color", label: "Hintergrundfarbe", type: "color", placeholder: "#fff oder rgba(255,255,255,0.5)" },
      { key: "theme_text_color", label: "Textfarbe", type: "color", placeholder: "#212529" },
      { key: "theme_primary", label: "Primärfarbe", type: "color", placeholder: "#0d6efd" },
      { key: "theme_secondary", label: "Sekundärfarbe", type: "color", placeholder: "#6c757d" },
      { key: "theme_link_color", label: "Linkfarbe", type: "color", placeholder: "#0d6efd" },
      { key: "theme_link_decoration", label: "Link-Decoration", type: "select", options: ["none", "underline"] },
      { key: "theme_link_hover_color", label: "Linkfarbe (Hover)", type: "color", placeholder: "wie Primärfarbe" },
      { key: "theme_link_hover_decoration", label: "Link-Decoration (Hover)", type: "select", options: ["none", "underline"] },
      { key: "theme_font_size", label: "Schriftgröße (Basis)", type: "text", placeholder: "1rem" },
    ];
    let html = '<div class="nx-global-optik-form small">';
    fields.forEach((f) => {
      let val = (opts[f.key] != null && opts[f.key] !== undefined) ? String(opts[f.key]).trim() : "";
      if (f.type === "color" && val) {
        var parsed = nxParseColor(val);
        if (parsed) val = nxToHex(parsed.r, parsed.g, parsed.b);
      }
      const valEsc = val.replace(/"/g, "&quot;").replace(/</g, "&lt;");
      if (f.type === "select") {
        html += '<div class="mb-2"><label class="form-label small">' + (f.label || f.key) + '</label><select class="form-select form-select-sm" data-nx-theme-field="' + f.key + '">';
        (f.options || []).forEach((opt) => { var sel = (val === opt || (val === "" && opt === "none")) ? ' selected' : ''; html += '<option value="' + opt + '"' + sel + '>' + opt + '</option>'; });
        html += '</select></div>';
      } else if (f.type === "color") {
        var isLinkHoverEmpty = (f.key === "theme_link_hover_color" && !val);
        html += '<div class="mb-2 nx-theme-color-wrap" data-nx-theme-color-key="' + f.key + '">';
        html += '<label class="form-label small">' + (f.label || f.key) + '</label>';
        html += '<div class="d-flex gap-1 align-items-center mb-1">';
        if (isLinkHoverEmpty) {
          html += '<button type="button" class="nx-color-swatch nx-swatch-no-color" data-nx-color-swatch title="Farbwähler öffnen" style="width:2.25rem;height:2rem;padding:0;cursor:pointer;border:1px solid #dee2e6;border-radius:4px;background-color:#e9ecef;color:#6c757d;font-size:1.1rem;line-height:1;display:flex;align-items:center;justify-content:center;"><span class="nx-swatch-x">×</span></button>';
        } else {
          html += '<button type="button" class="nx-color-swatch" data-nx-color-swatch title="Farbwähler öffnen" style="width:2.25rem;height:2rem;padding:0;cursor:pointer;border:1px solid #dee2e6;border-radius:4px;background-color:' + (val && val.indexOf("rgba") === -1 ? val : "#808080") + ';"></button>';
        }
        html += '<input type="text" class="form-control form-control-sm flex-grow-1" data-nx-theme-field="' + f.key + '" value="' + valEsc + '" placeholder="' + (f.placeholder || "") + '" spellcheck="false" style="min-width:8rem;">';
        html += '</div>';
        html += '<div class="d-flex gap-1 align-items-center small text-muted">';
        html += '<span>RGBA</span>';
        html += '<span class="nx-rgba-r nx-rgba-display" style="width:2.25rem;text-align:right;">0</span>';
        html += '<span class="nx-rgba-g nx-rgba-display" style="width:2.25rem;text-align:right;">0</span>';
        html += '<span class="nx-rgba-b nx-rgba-display" style="width:2.25rem;text-align:right;">0</span>';
        html += '<span class="nx-rgba-a nx-rgba-display" style="width:2.25rem;text-align:right;">100</span>';
        html += '</div></div>';
      } else {
        html += '<div class="mb-2"><label class="form-label small">' + (f.label || f.key) + '</label><input type="' + (f.type || "text") + '" class="form-control form-control-sm" data-nx-theme-field="' + f.key + '" value="' + valEsc + '" placeholder="' + (f.placeholder || "") + '"></div>';
      }
    });
    html += "</div>";
    if (settingsFields) settingsFields.innerHTML = html;
    nxInitThemeColorPickers(settingsFields);
    if (settingsPlaceholder) settingsPlaceholder.classList.add("d-none");
    if (settingsContent) {
      settingsContent.classList.remove("d-none");
      const jsonBlock = settingsContent.querySelector(".mb-2.flex-grow-1.d-flex.flex-column.min-h-0");
      if (jsonBlock) jsonBlock.classList.add("d-none");
    }
    if (settingsError) settingsError.textContent = "";
  }

  async function openSettingsForItem(item) {
    if (!item) return;
    globalOptikActive = false;
    setGlobalOptikButtonActive(false);
    // Aktives Widget im Canvas optisch hervorheben
    if (currentSettingsItem && currentSettingsItem !== item) {
      currentSettingsItem.classList.remove("nx-live-active");
    }
    item.classList.add("nx-live-active");
    const current = item.getAttribute("data-nx-settings") || "{}";
    currentSettingsItem = item;
    ensureSettingsSidebar();

    const labelEl = document.getElementById("nx-settings-label");
    if (labelEl) {
      const k = item.getAttribute("data-nx-key") || "";
      const t = item.getAttribute("data-nx-title") || k;
      labelEl.textContent = (k.startsWith("core_") ? "Core: " : "Widget: ") + t;
    }

    let parsedSettings = {};
    if (settingsTextarea) {
      try {
        parsedSettings = current.trim() ? JSON.parse(current) : {};
        settingsTextarea.value = JSON.stringify(parsedSettings, null, 2);
      } catch (e) {
        settingsTextarea.value = current;
      }
    }
    if (settingsFields) {
      buildSettingsFields(item.getAttribute("data-nx-key") || "", parsedSettings);
    }
    if (settingsError) settingsError.textContent = "";

    if (settingsPlaceholder) settingsPlaceholder.classList.add("d-none");
    if (settingsContent) settingsContent.classList.remove("d-none");

    // Navigation: Bei menuSource "plugin" und leerem menu einmal Plugin-Menü laden und setzen (editierbar im Builder).
    if (item.getAttribute("data-nx-key") === "core_nav_demo") {
      var s = {};
      try { s = JSON.parse(item.getAttribute("data-nx-settings") || "{}"); } catch (e) {}

      // Fehlende Defaults aus dem aktuell gerenderten Nav übernehmen, damit die UI
      // (Selects) den aktiven Zustand widerspiegelt (z.B. Header+Navbar Preset).
      try {
        const navEl = item.querySelector(".nx-live-content nav.nx-nav-core-demo");
        if (navEl) {
          const nxToBool = (v, fallback = false) => {
            if (typeof v === "boolean") return v;
            if (typeof v === "number") return v === 1;
            if (typeof v === "string") {
              const str = v.trim().toLowerCase();
              if (str === "1" || str === "true" || str === "yes" || str === "on") return true;
              if (str === "0" || str === "false" || str === "no" || str === "off" || str === "") return false;
            }
            return fallback;
          };
          const overlayAttr = navEl.getAttribute("data-nx-overlay");
          const scrollAttr = navEl.getAttribute("data-nx-scrollfill");
          const textAttr = navEl.getAttribute("data-nx-overlay-text");
          const ofsAttr = navEl.getAttribute("data-nx-scrollfill-offset");
          const filledShadowAttr = navEl.getAttribute("data-nx-filled-shadow");
          const fillSchemeAttr = navEl.getAttribute("data-nx-fill-scheme");

          const overlayFallback = overlayAttr === "1";
          const scrollFallback = scrollAttr === "1";
          const ofsFallback = ofsAttr && /^[0-9]+$/.test(String(ofsAttr)) ? parseInt(String(ofsAttr), 10) : 80;
          const textFallback = textAttr === "dark" ? "dark" : "light";
          const schemeFallback = fillSchemeAttr === "dark" ? "dark" : "light";

          if (typeof s.overlayMode === "undefined") s.overlayMode = overlayFallback;
          if (typeof s.scrollFill === "undefined") s.scrollFill = scrollFallback;
          // Wenn Overlay an ist und scrollFill fehlt, soll es standardmäßig AN sein (wie gewollt im Frontend)
          if (nxToBool(s.overlayMode) === true && typeof s.scrollFill === "undefined") s.scrollFill = true;
          if (typeof s.scrollFillOffset === "undefined") s.scrollFillOffset = ofsFallback;
          if (typeof s.overlayTextMode === "undefined") s.overlayTextMode = textFallback;
          if (typeof s.scheme === "undefined") s.scheme = schemeFallback;
          if (typeof s.filledShadow === "undefined") s.filledShadow = (filledShadowAttr && String(filledShadowAttr)) || "";
        }
      } catch (e) {}

      // Defaults in item + JSON-Textarea spiegeln, damit beim Speichern nichts "leer" bleibt.
      try {
        item.setAttribute("data-nx-settings", JSON.stringify(s));
        if (settingsTextarea) settingsTextarea.value = JSON.stringify(s, null, 2);
      } catch (e) {}

      // Felder neu aufbauen, damit die Selects sicher passend befüllt werden
      if (settingsFields) {
        buildSettingsFields("core_nav_demo", s);
      }

      if (s.menuSource === "plugin" && (!Array.isArray(s.menu) || s.menu.length === 0)) {
        try {
          var r = await fetch(RENDER_ENDPOINT, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-Token": CSRF },
            body: JSON.stringify({ action: "get_plugin_nav_menu", csrf: CSRF }),
            credentials: "same-origin",
          });
          var data = await r.json();
          if (data && data.ok && Array.isArray(data.menu) && data.menu.length > 0) {
            s.menu = data.menu;
            item.setAttribute("data-nx-settings", JSON.stringify(s));
            if (settingsTextarea) settingsTextarea.value = JSON.stringify(s, null, 2);
            if (typeof saveState === "function") await saveState();
            if (typeof renderInto === "function") await renderInto(item);
            if (settingsFields) buildSettingsFields("core_nav_demo", s);
          }
        } catch (e) { console.error("Plugin menu fetch failed", e); }
      }
    }
  }

  document
    .getElementById("nx-live-save")
    ?.addEventListener("click", saveState);

  // === Inline-Editor: Ausrichtung, Text, Bild (ohne Endlosschleife: nur 1x renderInto bei Bild/Align) ===
  function ensureInlineEditing() {
    if (alignToolbarEl) return;
    const style = document.createElement("style");
    style.textContent = `
      .nx-align-toolbar{ position:absolute; z-index:2147480003; display:flex; gap:2px; background:#1e293b; color:#fff; padding:4px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.25); }
      .nx-align-toolbar button{ width:32px; height:32px; border:none; background:transparent; color:#94a3b8; border-radius:4px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
      .nx-align-toolbar button:hover{ background:rgba(255,255,255,.15); color:#fff; }
      .nx-align-toolbar button.active{ color:#38bdf8; }
      .nx-image-popover{ position:fixed; z-index:2147480004; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:12px; box-shadow:0 10px 40px rgba(0,0,0,.15); min-width:280px; }
      .nx-image-popover input{ width:100%; margin-bottom:8px; }
      .nx-image-popover .nx-img-actions{ display:flex; gap:8px; justify-content:flex-end; }
      .nx-inline-editing{ outline:2px solid #f97316; outline-offset:2px; }
      /* Keine Outline außer unserer blauen: alle Inhalte zurücksetzen (Header/Theme/Browser) */
      body.builder-active .nx-live-content,
      body.builder-active .nx-live-content *,
      body.builder-active .nx-live-content *:focus,
      body.builder-active .nx-live-content *:focus-visible{ outline: none !important; }
      /* Nur diese Umrandung: schmale blaue beim Hover auf bearbeitbarem Inhalt */
      body.builder-active .nx-live-content [data-nx-inline]{ outline: none !important; outline-offset: 0; border-radius:2px; }
      body.builder-active .nx-live-content [data-nx-inline]:hover{ outline: 2px solid #c6c6c6 !important; outline-offset: 0px; }
      body.builder-active .nx-live-content [data-nx-inline]:focus{ outline: 2px dashed #f97316 !important; outline-offset: 0px; background-color: rgba(13,110,253,.06); }
      body.builder-active .nx-live-content [data-nx-inline="src"]:hover,
      body.builder-active .nx-live-content [data-nx-inline="image"]:hover{ cursor: pointer; }
      body.builder-active .nx-live-content [data-nx-inline]:not([data-nx-inline="src"]):not([data-nx-inline="image"]):hover{ cursor: text; }
      /* Block-Hinweis: Klick = Ausrichtung */
      .nx-inline-hint{ position:absolute; left:50%; transform:translateX(-50%); top:-22px; font-size:10px; color:#64748b; background:rgba(255,255,255,.95); padding:2px 8px; border-radius:4px; white-space:nowrap; pointer-events:none; box-shadow:0 1px 3px rgba(0,0,0,.08); opacity:0; transition:opacity .15s; z-index:1; }
      .nx-live-item:hover .nx-inline-hint,
      .nx-live-item.nx-live-active .nx-inline-hint{ opacity:1; }
      body.nx-dragging .nx-inline-hint{ opacity:0 !important; }
      .nx-img-drop-zone{ transition: background .15s, box-shadow .15s; }
      .nx-img-drop-zone.nx-drag-over{ background: rgba(13,110,253,.08) !important; box-shadow: 0 0 0 2px #0d6efd; }
      [data-nx-inline="image"].nx-canvas-drag-over, [data-nx-inline="src"].nx-canvas-drag-over{ background: rgba(13,110,253,.12) !important; box-shadow: 0 0 0 2px #0d6efd; }
      .nx-inline-img-drop-zone.nx-drag-over{ background: rgba(13,110,253,.1) !important; border-color: #0d6efd !important; }
      /* Kontext-Popover für Navigation-Links (nur im Builder) */
      .nx-nav-popover{ position:fixed; z-index:2147480005; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:8px; box-shadow:0 4px 8px rgba(0,0,0,.15); min-width:220px; }
      .nx-nav-popover .btn{ display:block; width:100%; text-align:left; margin-bottom:4px; font-size:11px; padding:4px 8px; }
      .nx-nav-popover .btn:last-child{ margin-bottom:0; }
      /* Aktiver Nav-Link beim Bearbeiten (ähnlich orange-dashed-Zone) */
      body.builder-active .nx-live-content .nx-nav-edit-active{
        outline:2px dashed #f97316 !important;
        outline-offset:0px;
        border-radius:4px;
      }
      /* Gear-Button an Nav-Links (nur Builder, schwebend am Rahmen) */
      body.builder-active .nx-live-content .nav-item,
      body.builder-active .nx-live-content .dropdown-menu li{ position:relative; }
      body.builder-active .nx-live-content .nx-nav-gear{
        position:absolute;
        top:0px;
        right:0px;
        padding:2px 6px;
        font-size:11px;
        line-height:1.2;
        z-index:2147480006;
        opacity:0;
        pointer-events:none;
        background-color: #f97316;
        border:1px solid #f97316;
        border-radius: 0px;
        box-shadow: none;
        color:#fff;
      }
      body.builder-active .nx-live-content .nx-nav-gear i{ font-size:12px; }
      body.builder-active .nx-live-content .nav-item:hover > .nx-nav-gear,
      body.builder-active .nx-live-content .nav-item:focus-within > .nx-nav-gear,
      body.builder-active .nx-live-content .dropdown-menu li:hover > .nx-nav-gear,
      body.builder-active .nx-live-content .dropdown-menu li:focus-within > .nx-nav-gear{
        opacity:1;
        pointer-events:auto;
      }
      /* In der Builder-Vorschau kein zusätzliches vertikales Padding über .navbar-brand,
         damit die Gesamthöhe primär durch Logo-Höhe + Nav-Item-Padding bestimmt wird. */
      body.builder-active .nx-live-content .navbar-brand{
        padding-top:0 !important;
        padding-bottom:0 !important;
        margin-top:0 !important;
        margin-bottom:0 !important;
      }
    `;
    document.head.appendChild(style);
    alignToolbarEl = document.createElement("div");
    alignToolbarEl.className = "nx-align-toolbar";
    alignToolbarEl.innerHTML = '<button type="button" title="Links" data-align="start"><i class="bi bi-text-left"></i></button><button type="button" title="Zentriert" data-align="center"><i class="bi bi-text-center"></i></button><button type="button" title="Rechts" data-align="end"><i class="bi bi-text-right"></i></button>';
    alignToolbarEl.hidden = true;
    alignToolbarEl.addEventListener("mousedown", (e) => e.preventDefault(), false);
    document.body.appendChild(alignToolbarEl);
    imagePopoverEl = document.createElement("div");
    imagePopoverEl.className = "nx-image-popover";
    imagePopoverEl.innerHTML = '<div class="nx-inline-img-drop-zone border border-2 border-dashed rounded p-4 text-center mb-2" style="min-height:80px;"><span class="text-muted small">Bild hier ablegen</span></div><div class="d-flex gap-2 align-items-center"><button type="button" class="btn btn-sm btn-primary nx-inline-img-choose">Datei auswählen</button><button type="button" class="btn btn-sm btn-outline-secondary nx-inline-img-cancel">Abbrechen</button></div><input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" id="nx-inline-img-file" />';
    imagePopoverEl.hidden = true;
    document.body.appendChild(imagePopoverEl);

    // Kontext-Popover für Navigationslinks (Menu-Items)
    let navPopoverEl = document.createElement("div");
    navPopoverEl.className = "nx-nav-popover";
    navPopoverEl.innerHTML = ''
      + '<div class="mb-2" data-nx-nav-url-row>'
      + '  <label class="form-label small mb-1">URL oder Pfad</label>'
      + '  <input type="text" class="form-control form-control-sm mb-1" data-nx-nav-url-input placeholder="/preise oder https://..." />'
      + '  <button type="button" class="btn btn-sm btn-primary w-100" data-nx-nav-action="save_url">URL speichern</button>'
      + '</div>'
      + '<button type="button" class="btn btn-sm btn-outline-primary" data-nx-nav-action="add_dropdown">In Dropdown umwandeln</button>'
      + '<button type="button" class="btn btn-sm btn-outline-primary" data-nx-nav-action="add_sublink">+ Unterlink hinzufügen</button>'
      + '<button type="button" class="btn btn-sm btn-outline-warning" data-nx-nav-action="remove_dropdown">Dropdown entfernen</button>'
      + '<button type="button" class="btn btn-sm btn-outline-success" data-nx-nav-action="add_link_after">+ Link nach diesem einfügen</button>'
      + '<button type="button" class="btn btn-sm btn-outline-danger" data-nx-nav-action="remove_link">Link löschen</button>'
      + '<button type="button" class="btn btn-sm btn-outline-danger" data-nx-nav-action="remove_sublink">Unterlink löschen</button>';
    navPopoverEl.hidden = true;
    document.body.appendChild(navPopoverEl);
    navUrlInputEl = navPopoverEl.querySelector("[data-nx-nav-url-input]");

    if (!window._nxCanvasImgDropBound) {
      window._nxCanvasImgDropBound = true;
      document.addEventListener("dragover", function (e) {
        if (!e.dataTransfer.types.includes("Files")) return;
        var el = e.target.closest(".nx-live-content [data-nx-inline=\"image\"], .nx-live-content [data-nx-inline=\"src\"]");
        if (!el) {
          document.querySelectorAll(".nx-canvas-drag-over").forEach(function (z) { z.classList.remove("nx-canvas-drag-over"); });
          return;
        }
        e.preventDefault();
        e.dataTransfer.dropEffect = "copy";
        document.querySelectorAll(".nx-canvas-drag-over").forEach(function (z) { z.classList.remove("nx-canvas-drag-over"); });
        el.classList.add("nx-canvas-drag-over");
      }, false);
      document.addEventListener("dragleave", function (e) {
        var el = e.target.closest("[data-nx-inline=\"image\"], [data-nx-inline=\"src\"]");
        if (el && (!e.relatedTarget || !el.contains(e.relatedTarget))) el.classList.remove("nx-canvas-drag-over");
      }, false);
      document.addEventListener("drop", async function (e) {
        var el = e.target.closest(".nx-live-content [data-nx-inline=\"image\"], .nx-live-content [data-nx-inline=\"src\"]");
        if (!el) return;
        e.preventDefault();
        e.stopPropagation();
        el.classList.remove("nx-canvas-drag-over");
        var file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (!file || !file.type.startsWith("image/")) return;
        var item = el.closest(".nx-live-item");
        if (!item) return;
        var fieldName = el.getAttribute("data-nx-inline");
        var vars = window.NXB_BUILDER_VARS || {};
        var uploadUrl = (vars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
        var fd = new FormData();
        fd.append("file", file);
        fd.append("csrf", vars.CSRF || "");
        try {
          var r = await fetch(uploadUrl, { method: "POST", body: fd, credentials: "same-origin" });
          var data = await r.json();
          if (data && data.ok && data.url) {
            var settings = JSON.parse(item.getAttribute("data-nx-settings") || "{}");
            settings[fieldName] = data.url;
            item.setAttribute("data-nx-settings", JSON.stringify(settings));
            await saveState();
            const key = item.getAttribute("data-nx-key") || "";
            if (key !== "core_nav_demo") await renderInto(item);
          }
        } catch (err) { console.error("Canvas image drop upload failed", err); }
      }, false);
    }

    function ensureHintsOnItems() {
      document.querySelectorAll(".nx-live-item").forEach((item) => {
        let hint = item.querySelector(".nx-inline-hint");
        const key = item.getAttribute("data-nx-key") || "";
        const text = ALIGN_KEYS.indexOf(key) !== -1 ? "Klick: Ausrichtung · Doppelklick: Text bearbeiten" : "Doppelklick: Text bearbeiten";
        if (!hint) {
          hint = document.createElement("div");
          hint.className = "nx-inline-hint";
          item.appendChild(hint);
        }
        hint.textContent = text;
      });
    }
    ensureHintsOnItems();

    let alignTargetItem = null;
    let imageTargetItem = null;
    let imageFieldName = null;
    let navPopoverItem = null;
    let navPopoverPath = "";
    let navActiveEl = null;
    let navActiveDropdownRoot = null;
    /** Beim Inline-Bearbeiten: das DOM-Element, das text-start/center/end trägt (wird live umgehängt) */
    let inlineEditAlignTargetEl = null;

    function getAlignTarget(el) {
      let n = el;
      const content = el.closest(".nx-live-content");
      while (n && content && content.contains(n)) {
        if (n.classList && (n.classList.contains("text-start") || n.classList.contains("text-center") || n.classList.contains("text-end")))
          return n;
        n = n.parentElement;
      }
      return el;
    }

    function applyAlignClass(el, align) {
      if (!el || !el.classList) return;
      el.classList.remove("text-start", "text-center", "text-end");
      if (align === "center") el.classList.add("text-center");
      else if (align === "end") el.classList.add("text-end");
      else el.classList.add("text-start");
    }

    function showAlignToolbarForEl(item, el) {
      const rect = el.getBoundingClientRect();
      const toolW = 120;
      alignToolbarEl.style.left = (rect.left + rect.width / 2 - toolW / 2) + "px";
      alignToolbarEl.style.top = (rect.top - 44) + "px";
      alignTargetItem = item;
      const currentAlign = (JSON.parse(item.getAttribute("data-nx-settings") || "{}")).align || "start";
      alignToolbarEl.querySelectorAll("button").forEach((b) => b.classList.toggle("active", b.getAttribute("data-align") === currentAlign));
      alignToolbarEl.hidden = false;
    }

    function hideAlignToolbar() {
      alignToolbarEl.hidden = true;
      alignTargetItem = null;
      inlineEditAlignTargetEl = null;
    }
    function hideImagePopover() {
      imagePopoverEl.hidden = true;
      imageTargetItem = null;
      imageFieldName = null;
    }
    function hideNavPopover() {
      if (navPopoverEl) {
        navPopoverEl.hidden = true;
      }
      if (navActiveEl && navActiveEl.classList) {
        navActiveEl.classList.remove("nx-nav-edit-active");
      }
      if (navActiveDropdownRoot) {
        const toggleEl = navActiveDropdownRoot.querySelector('[data-bs-toggle="dropdown"]');
        const menuEl = navActiveDropdownRoot.querySelector(".dropdown-menu");
        if (menuEl && toggleEl) {
          menuEl.classList.remove("show");
          toggleEl.classList.remove("show");
          toggleEl.setAttribute("aria-expanded", "false");
        }
        navActiveDropdownRoot = null;
      }
      navActiveEl = null;
      navPopoverItem = null;
      navPopoverPath = "";
    }

    alignToolbarEl.querySelectorAll("button").forEach((btn) => {
      btn.addEventListener("click", async (e) => {
        e.stopPropagation();
        if (!alignTargetItem) return;
        const align = btn.getAttribute("data-align") || "start";
        const settings = JSON.parse(alignTargetItem.getAttribute("data-nx-settings") || "{}");
        settings.align = align;
        alignTargetItem.setAttribute("data-nx-settings", JSON.stringify(settings));
        if (inlineEditAlignTargetEl) {
          applyAlignClass(inlineEditAlignTargetEl, align);
          alignToolbarEl.querySelectorAll("button").forEach((b) => b.classList.toggle("active", b.getAttribute("data-align") === align));
          try {
            await saveState();
          } catch (err) {
            console.error("Inline align save failed", err);
          }
          return;
        }
        hideAlignToolbar();
        try {
          await saveState();
          await renderInto(alignTargetItem);
        } catch (err) {
          console.error("Inline align save failed", err);
        }
      });
    });
    imagePopoverEl.querySelector(".nx-inline-img-cancel").addEventListener("click", hideImagePopover);
    var inlineImgFileInput = imagePopoverEl.querySelector("#nx-inline-img-file");
    imagePopoverEl.querySelector(".nx-inline-img-choose").addEventListener("click", function () { inlineImgFileInput.click(); });
    function updateInlineImageInDom(item, fieldName, url) {
      if (!item || !url) return;
      var sel = "[data-nx-inline=\"" + fieldName + "\"]";
      var el = item.querySelector(sel);
      if (!el) return;
      if (el.tagName === "IMG") {
        el.src = url;
        el.removeAttribute("data-nx-placeholder");
        return;
      }
      var img = document.createElement("img");
      img.src = url;
      img.setAttribute("data-nx-inline", fieldName);
      img.alt = "";
      el.replaceWith(img);
    }
    async function applyInlineImageUpload(file) {
      if (!imageTargetItem || !imageFieldName || !file || !file.type.startsWith("image/")) return;
      var vars = window.NXB_BUILDER_VARS || {};
      var uploadUrl = (vars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      var fd = new FormData();
      fd.append("file", file);
      fd.append("csrf", vars.CSRF || "");
      try {
        var r = await fetch(uploadUrl, { method: "POST", body: fd, credentials: "same-origin" });
        var data = await r.json();
        if (data && data.ok && data.url) {
          var settings = JSON.parse(imageTargetItem.getAttribute("data-nx-settings") || "{}");
          settings[imageFieldName] = data.url;
          imageTargetItem.setAttribute("data-nx-settings", JSON.stringify(settings));
          updateInlineImageInDom(imageTargetItem, imageFieldName, data.url);
          hideImagePopover();
          await saveState();
          const key = imageTargetItem.getAttribute("data-nx-key") || "";
          if (key !== "core_nav_demo") await renderInto(imageTargetItem);
        }
      } catch (err) { console.error("Inline image upload failed", err); }
      if (inlineImgFileInput) inlineImgFileInput.value = "";
    }
    inlineImgFileInput.addEventListener("change", function () {
      var file = inlineImgFileInput.files && inlineImgFileInput.files[0];
      if (file) applyInlineImageUpload(file);
    });
    var dropZone = imagePopoverEl.querySelector(".nx-inline-img-drop-zone");
    if (dropZone) {
      dropZone.addEventListener("dblclick", function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (inlineImgFileInput) inlineImgFileInput.click();
      });
      dropZone.addEventListener("dragover", function (e) {
        if (e.dataTransfer.types.indexOf("Files") === -1) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = "copy";
        dropZone.classList.add("nx-drag-over");
      });
      dropZone.addEventListener("dragleave", function (e) {
        if (!e.relatedTarget || !dropZone.contains(e.relatedTarget)) dropZone.classList.remove("nx-drag-over");
      });
      dropZone.addEventListener("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove("nx-drag-over");
        var file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (file) applyInlineImageUpload(file);
      });
    }

    function getMenuPathParts(path) {
      if (!path) return null;
      const parts = String(path).split(".").filter(Boolean);
      if (!parts.length) return null;
      return parts.map((p) => (p === "children" ? "children" : Number.isFinite(Number(p)) ? Number(p) : p));
    }
    function ensureMenuDefaults(settings) {
      if (!settings || typeof settings !== "object") return settings;
      if (!Array.isArray(settings.menu)) settings.menu = [];
      return settings;
    }
    function setMenuAtPath(settings, pathStr, patch) {
      const parts = getMenuPathParts(pathStr);
      if (!parts) return settings;
      ensureMenuDefaults(settings);
      let ref = settings.menu;
      // parts Struktur: [index, 'children', index] bis zum Item
      for (let i = 0; i < parts.length; i++) {
        const part = parts[i];
        const isLast = i === parts.length - 1;
        if (typeof part === "number") {
          if (!Array.isArray(ref)) return settings;
          if (!ref[part]) ref[part] = { label: "Neuer Link", url: "#", children: [] };
          if (isLast) {
            ref[part] = { ...ref[part], ...patch };
            return settings;
          }
          ref = ref[part];
        } else if (part === "children") {
          if (!ref.children) ref.children = [];
          ref = ref.children;
        } else {
          // unsupported
          return settings;
        }
      }
      return settings;
    }

    function showNavPopoverForEl(targetEl, item, pathStr) {
      if (!navPopoverEl || !item || !pathStr) return;
      navPopoverItem = item;
      navPopoverPath = pathStr;

      // Menü-Kontext ermitteln
      let settings = {};
      try {
        settings = JSON.parse(item.getAttribute("data-nx-settings") || "{}");
      } catch (err) {
        settings = {};
      }
      ensureMenuDefaults(settings);
      const parts = getMenuPathParts(pathStr) || [];
      const isChild = parts.length >= 3 && parts[1] === "children" && typeof parts[2] === "number";
      const topIndex = typeof parts[0] === "number" ? parts[0] : null;
      const parent = topIndex !== null && Array.isArray(settings.menu) ? settings.menu[topIndex] : null;
      const hasChildren = !!(parent && Array.isArray(parent.children) && parent.children.length > 0);

      const setVis = (action, show) => {
        const btn = navPopoverEl.querySelector('[data-nx-nav-action="' + action + '"]');
        if (!btn) return;
        btn.style.display = show ? "" : "none";
      };
      const urlRow = navPopoverEl.querySelector("[data-nx-nav-url-row]");

      // aktuellen URL-Wert in das Eingabefeld schreiben
      if (navUrlInputEl) {
        let currentUrl = "";
        if (pathStr === "login") {
          currentUrl = settings.login_url || "";
        } else if (isChild && parent && Array.isArray(parent.children)) {
          const ci = parts[2];
          const c = parent.children[ci] || {};
          currentUrl = c.url || "";
        } else if (!isChild && topIndex !== null && settings.menu[topIndex]) {
          currentUrl = settings.menu[topIndex].url || "";
        }
        navUrlInputEl.value = currentUrl;
      }

      if (pathStr === "login") {
        // Login-Link: nur URL-Zeile anzeigen, keine Menü-Aktionen
        if (urlRow) urlRow.style.display = "";
        setVis("save_url", true);
        setVis("add_dropdown", false);
        setVis("add_sublink", false);
        setVis("remove_dropdown", false);
        setVis("add_link_after", false);
        setVis("remove_link", false);
        setVis("remove_sublink", false);
      } else if (isChild) {
        // Unterlink: URL-Editor + Unterlink löschen
        if (urlRow) urlRow.style.display = "";
        setVis("remove_sublink", true);
        setVis("add_dropdown", false);
        setVis("add_sublink", false);
        setVis("remove_dropdown", false);
        setVis("add_link_after", false);
        setVis("remove_link", false);
      } else if (topIndex !== null && hasChildren) {
        // Ober-Link mit Dropdown (keine eigene URL, nur Struktur)
        if (urlRow) urlRow.style.display = "none";
        setVis("add_dropdown", false);
        setVis("add_sublink", true);
        setVis("remove_dropdown", true);
        setVis("add_link_after", true);
        setVis("remove_link", true);
        setVis("remove_sublink", false);
      } else {
        // Ober-Link ohne Dropdown: URL-Editor sichtbar
        if (urlRow) urlRow.style.display = "";
        setVis("add_dropdown", true);
        setVis("add_sublink", false);
        setVis("remove_dropdown", false);
        setVis("add_link_after", true);
        setVis("remove_link", true);
        setVis("remove_sublink", false);
      }

      // aktives Element markieren (orangener Rahmen) und ggf. Dropdown offen halten
      if (navActiveEl && navActiveEl.classList) {
        navActiveEl.classList.remove("nx-nav-edit-active");
      }
      // Für Gear-Button: zuerst versuchen, den benachbarten Link zu markieren
      let candidate = null;
      if (targetEl.classList && targetEl.classList.contains("nx-nav-gear")) {
        const prev = targetEl.previousElementSibling;
        if (prev && (prev.matches("a.nav-link") || prev.matches("a.dropdown-item"))) {
          candidate = prev;
        }
      }
      if (!candidate) {
        candidate = targetEl.matches("a") ? targetEl : targetEl.closest("a.nav-link, a.dropdown-item");
      }
      navActiveEl = candidate || targetEl;
      if (navActiveEl && navActiveEl.classList) {
        navActiveEl.classList.add("nx-nav-edit-active");
      }
      const dropdownRoot = targetEl.closest(".dropdown");
      if (dropdownRoot) {
        const toggleEl = dropdownRoot.querySelector('[data-bs-toggle="dropdown"]');
        const menuEl = dropdownRoot.querySelector(".dropdown-menu");
        if (menuEl && toggleEl) {
          menuEl.classList.add("show");
          toggleEl.classList.add("show");
          toggleEl.setAttribute("aria-expanded", "true");
        }
        navActiveDropdownRoot = dropdownRoot;
      } else {
        navActiveDropdownRoot = null;
      }

      const rect = targetEl.getBoundingClientRect();
      const left = rect.left;
      navPopoverEl.hidden = false;
      // erst sichtbar machen, dann messen/positionieren
      const popH = navPopoverEl.offsetHeight || 180;
      const desiredTop = rect.bottom + 8;
      const top = desiredTop + popH > window.innerHeight ? Math.max(rect.top - popH - 8, 40) : desiredTop;
      navPopoverEl.style.top = `${top}px`;
      navPopoverEl.style.left = `${Math.max(left, 10)}px`;
      // Pfad an Buttons hängen
      navPopoverEl.querySelectorAll("[data-nx-nav-action]").forEach((btn) => {
        btn.setAttribute("data-nx-nav-path", pathStr);
      });
    }

    // Sprachumschalter: in Capture-Phase (läuft vor allen anderen), Navigation explizit ausführen.
    // Hinweis: Der Selector setzt die gewählte Sprache (Flagge), die Website schaltet aber derzeit nicht zuverlässig um (offenes Thema).
    document.addEventListener("click", function (e) {
      const langLink = e.target.closest("a[data-nx-lang-link], a[href*='lang='], a[href*='setlang=']");
      if (!langLink) return;
      const href = langLink.getAttribute("href");
      if (!href || href === "#") return;
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      window.top.location.href = href;
    }, true);

    document.addEventListener("click", async (e) => {
      if (e.target.closest(".nx-live-controls")) return;

      // Klick auf Gear-Button: Konfig-Popover öffnen (früh behandeln)
      if (!e.target.closest(".nx-nav-popover")) {
        const gear = e.target.closest('[data-nx-nav-gear="1"]');
        if (gear && gear.closest(".nx-live-content")) {
          const item = gear.closest(".nx-live-item");
          const pathStr = gear.getAttribute("data-nx-nav-path") || "";
          showNavPopoverForEl(gear, item, pathStr);
          e.preventDefault();
          e.stopPropagation();
          return;
        }
      }

      const navActionEl = e.target.closest("[data-nx-nav-action]");
      if (navActionEl) {
        const item = navActionEl.closest(".nx-live-item") || navPopoverItem;
        if (!item) return;
        const action = navActionEl.getAttribute("data-nx-nav-action") || "";
        const path = navActionEl.getAttribute("data-nx-nav-path") || navPopoverPath || "";
        let settings = {};
        try { settings = JSON.parse(item.getAttribute("data-nx-settings") || "{}"); } catch (err) {}
        ensureMenuDefaults(settings);

        const addTopItem = (afterIndex) => {
          const newItem = { label: "Neuer Link", url: "#", children: [] };
          const idx = Number.isFinite(Number(afterIndex)) ? Number(afterIndex) : settings.menu.length - 1;
          settings.menu.splice(idx + 1, 0, newItem);
        };
        const removeTopItem = (index) => {
          const idx = Number(index);
          if (!Number.isFinite(idx)) return;
          settings.menu.splice(idx, 1);
        };
        const addChildItem = (parentIndex) => {
          const p = settings.menu[Number(parentIndex)];
          if (!p) return;
          if (!Array.isArray(p.children)) p.children = [];
          p.children.push({ label: "Neuer Unterlink", url: "#" });
        };
        const removeChildItem = (parentIndex, childIndex) => {
          const p = settings.menu[Number(parentIndex)];
          if (!p || !Array.isArray(p.children)) return;
          const ci = Number(childIndex);
          if (!Number.isFinite(ci)) return;
          p.children.splice(ci, 1);
          if (p.children.length === 0) delete p.children;
        };
        const makeDropdown = (parentIndex) => {
          const p = settings.menu[Number(parentIndex)];
          if (!p) return;
          if (!Array.isArray(p.children) || p.children.length === 0) {
            p.children = [{ label: "Unterlink 1", url: "#" }];
          }
        };
        const removeDropdown = (parentIndex) => {
          const p = settings.menu[Number(parentIndex)];
          if (!p) return;
          delete p.children;
        };
        const editUrl = (targetPath) => {};

        // path Syntax: "0" oder "0.children.1"
        const parts = getMenuPathParts(path) || [];
        const topIndex = typeof parts[0] === "number" ? parts[0] : null;
        const childIndex = parts.length >= 3 && parts[1] === "children" && typeof parts[2] === "number" ? parts[2] : null;

        if (action === "add_link_after" && topIndex !== null) addTopItem(topIndex);
        else if (action === "remove_link" && topIndex !== null) removeTopItem(topIndex);
        else if (action === "add_dropdown" && topIndex !== null) makeDropdown(topIndex);
        else if (action === "remove_dropdown" && topIndex !== null) removeDropdown(topIndex);
        else if (action === "add_sublink" && topIndex !== null) addChildItem(topIndex);
        else if (action === "remove_sublink" && topIndex !== null && childIndex !== null) removeChildItem(topIndex, childIndex);
        else if (action === "save_url") {
          if (!navUrlInputEl) {
            e.preventDefault();
            e.stopPropagation();
            return;
          }
          const pathForUrl = navPopoverPath || path;
          const rawUrl = (navUrlInputEl.value || "").trim();
          const finalUrl = rawUrl || "#";
          if (pathForUrl === "login") {
            settings.login_url = finalUrl;
          } else {
            const partsForUrl = getMenuPathParts(pathForUrl) || [];
            if (!partsForUrl || typeof partsForUrl[0] !== "number") {
              e.preventDefault();
              e.stopPropagation();
              return;
            }
            const tIdx = partsForUrl[0];
            const isChildUrl = partsForUrl.length >= 3 && partsForUrl[1] === "children" && typeof partsForUrl[2] === "number";
            if (isChildUrl) {
              const p = settings.menu[tIdx] || {};
              if (!Array.isArray(p.children)) p.children = [];
              if (!p.children[partsForUrl[2]]) p.children[partsForUrl[2]] = { label: "Neuer Unterlink", url: "#" };
              p.children[partsForUrl[2]].url = finalUrl;
              settings.menu[tIdx] = p;
            } else {
              const base = settings.menu[tIdx] || { label: "Neuer Link", url: "#", children: [] };
              base.url = finalUrl;
              settings.menu[tIdx] = base;
            }
          }
        }

        item.setAttribute("data-nx-settings", JSON.stringify(settings));
        try {
          await saveState();
          await renderInto(item);
        } catch (err) {
          console.error("Nav menu action failed", err);
        }
        hideNavPopover();
        e.preventDefault();
        e.stopPropagation();
        return;
      }
      // Klick außerhalb von Gear/Popover schließt das Popover
      if (!e.target.closest(".nx-nav-popover")) {
        hideNavPopover();
      }
      const content = e.target.closest(".nx-live-content");
      if (!content) {
        if (!e.target.closest(".nx-align-toolbar") && !e.target.closest(".nx-image-popover")) {
          hideAlignToolbar();
          hideImagePopover();
        }
        return;
      }
      // Sprachumschalter-Links durchlassen (nicht abfangen), damit Sprache im Frontend/Builder übernommen wird
      const langLink = e.target.closest("a[data-nx-lang-link], a[href*='lang='], a[href*='setlang=']");
      if (langLink && content.contains(langLink)) {
        return;
      }
      const item = content.closest(".nx-live-item");
      if (!item) return;
      // Ein einfacher Klick auf einen Widget-Inhalt öffnet immer die Einstellungen
      openSettingsForItem(item);
      const inlineEl = e.target.closest("[data-nx-inline]");
      if (inlineEl && (inlineEl.tagName === "A" || inlineEl.closest("a"))) {
        e.preventDefault();
      }
      const key = item.getAttribute("data-nx-key") || "";

      const imgOrPlaceholder = e.target.closest("[data-nx-inline]");
      const inlineField = imgOrPlaceholder && imgOrPlaceholder.getAttribute("data-nx-inline");
      if (inlineField === "src" || inlineField === "image") {
        // Spezialfall: Logo in Navigation (core_nav_demo) – leite auf Logo-Upload im Sidebar-Panel um
        if (key === "core_nav_demo" && settingsFields) {
          const logoBtn = settingsFields.querySelector("[data-nx-navlogo-upload-btn]");
          if (logoBtn) {
            e.preventDefault();
            e.stopPropagation();
            logoBtn.click();
            return;
          }
        }
        e.preventDefault();
        e.stopPropagation();
        imageTargetItem = item;
        imageFieldName = inlineField;
        hideAlignToolbar();
        var fileInput = imagePopoverEl.querySelector("#nx-inline-img-file");
        if (fileInput) fileInput.click();
        return;
      }

      if (e.target.closest(".nx-image-popover") || e.target.closest(".nx-align-toolbar")) return;
      if (ALIGN_KEYS.indexOf(key) === -1) return;
      const rect = content.getBoundingClientRect();
      const toolW = 120;
      alignToolbarEl.style.left = (rect.left + rect.width / 2 - toolW / 2) + "px";
      alignToolbarEl.style.top = (rect.top - 44) + "px";
      alignTargetItem = item;
      const currentAlign = (JSON.parse(item.getAttribute("data-nx-settings") || "{}")).align || "start";
      alignToolbarEl.querySelectorAll("button").forEach((b) => b.classList.toggle("active", b.getAttribute("data-align") === currentAlign));
      alignToolbarEl.hidden = false;
      hideImagePopover();
    });

    document.addEventListener("dblclick", (e) => {
      if (inlineEditingActive) return;
      const el = e.target.closest("[data-nx-inline]");
      if (!el || !el.closest(".nx-live-content")) return;
      const field = el.getAttribute("data-nx-inline");
      if (field === "src" || field === "image") return;
      const item = el.closest(".nx-live-item");
      if (!item) return;
      e.preventDefault();
      e.stopPropagation();
      const settings = JSON.parse(item.getAttribute("data-nx-settings") || "{}");
      const isHtml = field === "html";
      const currentVal = isHtml ? el.innerHTML : (el.textContent || "").trim();
      if (isHtml) {
        el.contentEditable = "true";
        el.focus();
        el.classList.add("nx-inline-editing");
        inlineEditingActive = true;
        const key = item.getAttribute("data-nx-key") || "";
        if (ALIGN_KEYS.indexOf(key) !== -1) {
          inlineEditAlignTargetEl = getAlignTarget(el);
          showAlignToolbarForEl(item, el);
        }
        function finishHtml() {
          el.contentEditable = "false";
          el.classList.remove("nx-inline-editing");
          let currentSettings = {};
          try {
            currentSettings = JSON.parse(item.getAttribute("data-nx-settings") || "{}");
          } catch (e) {}
          currentSettings.html = el.innerHTML;
          item.setAttribute("data-nx-settings", JSON.stringify(currentSettings));
          inlineEditingActive = false;
          hideAlignToolbar();
          saveState();
          el.removeEventListener("blur", finishHtml);
          document.removeEventListener("keydown", onKey);
        }
        function onKey(ev) {
          if (ev.key === "Escape") {
            el.innerHTML = currentVal;
            finishHtml();
          }
        }
        el.addEventListener("blur", finishHtml, { once: true });
        document.addEventListener("keydown", onKey);
        return;
      }
      const map = { text: "text", title: "title", subtitle: "subtitle", label: "label", caption: "caption", primaryLabel: "primaryLabel", secondaryLabel: "secondaryLabel", author: "author", source: "source" };
      const isMenuField = field && field.startsWith("menu:");
      const settingsKey = map[field] || field;
      const key = item.getAttribute("data-nx-key") || "";
      /* Direkt im Element bearbeiten (contentEditable), kein Overlay */
      el.contentEditable = "true";
      el.classList.add("nx-inline-editing");
      inlineEditingActive = true;
      if (ALIGN_KEYS.indexOf(key) !== -1) {
        inlineEditAlignTargetEl = getAlignTarget(el);
        showAlignToolbarForEl(item, el);
      }
      el.focus();
      /* Cursor ans Ende setzen */
      try {
        const range = document.createRange();
        const sel = window.getSelection();
        range.selectNodeContents(el);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      } catch (err) {}
      const PLACEHOLDER_TEXTS = ["Untertitel – Doppelklick zum Hinzufügen", "Bildunterschrift – Doppelklick zum Hinzufügen", "Text – Doppelklick zum Hinzufügen", "Button – Doppelklick", "Zweiter Button – Doppelklick", "Autor – Doppelklick", "Quelle – Doppelklick", "Überschrift – Doppelklick", "Antwort – Doppelklick zum Hinzufügen"];
      function finish() {
        let val;
        // Für Features-Textareas (z. B. planX_features) sollen Zeilenumbrüche erhalten bleiben.
        if (settingsKey && settingsKey.endsWith("_features")) {
          let raw = (el.innerText || "");
          raw = raw.replace(/\r\n/g, "\n").replace(/\r/g, "\n");
          const lines = raw
            .split("\n")
            .map((l) => l.trim())
            // aufeinanderfolgende komplett leere Zeilen auf eine reduzieren
            .filter((l, i, arr) => !(l === "" && (i === 0 || arr[i - 1] === "")));
          val = lines.join("\n").trim();
        } else {
          val = (el.textContent || "").trim();
          if (PLACEHOLDER_TEXTS.indexOf(val) !== -1) val = "";
        }
        el.contentEditable = "false";
        el.classList.remove("nx-inline-editing");
        let currentSettings = {};
        try {
          currentSettings = JSON.parse(item.getAttribute("data-nx-settings") || "{}");
        } catch (e) {}
        if (isMenuField) {
          // field format: menu:<path>:label  (path uses dots, e.g. 0 or 0.children.1)
          const parts = field.split(":");
          const pathStr = parts[1] || "";
          const prop = parts[2] || "label";
          if (prop === "label") {
            setMenuAtPath(currentSettings, pathStr, { label: val });
          }
        } else {
          currentSettings[settingsKey] = val;
        }
        item.setAttribute("data-nx-settings", JSON.stringify(currentSettings));
        if (el.tagName === "FIGCAPTION" && val === "") {
          el.textContent = "";
          if (el.classList.contains("d-none") === false) el.classList.add("d-none");
        } else {
          el.textContent = val;
          if (el.tagName === "FIGCAPTION" && el.classList.contains("d-none")) el.classList.remove("d-none");
        }
        inlineEditingActive = false;
        hideAlignToolbar();
        saveState();
        el.removeEventListener("blur", finish);
        document.removeEventListener("keydown", onKey);
      }
      function onKey(ev) {
        if (ev.key === "Enter") {
          ev.preventDefault();
          finish();
        } else if (ev.key === "Escape") {
          el.textContent = currentVal;
          el.contentEditable = "false";
          el.classList.remove("nx-inline-editing");
          inlineEditingActive = false;
          hideAlignToolbar();
          el.removeEventListener("blur", finish);
          document.removeEventListener("keydown", onKey);
        }
      }
      el.addEventListener("blur", finish, { once: true });
      document.addEventListener("keydown", onKey);
    });
  }

  function buildSettingsFields(widgetKey, settings) {
    if (!settingsFields) return;
    settingsFields.innerHTML = "";

    const key = (widgetKey || "").trim();

    // Nur für Core-Widgets spezielle Felder anzeigen
    if (key === "core_heading") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Text</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="text" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Level</label>
            <select class="form-select form-select-sm" data-nx-field="level">
              <option value="1">H1</option>
              <option value="2">H2</option>
              <option value="3">H3</option>
              <option value="4">H4</option>
              <option value="5">H5</option>
              <option value="6">H6</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="">Standard</option>
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
        </div>
        <hr class="my-2">
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit (responsive)</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer sichtbar</option>
            <option value="d-none d-md-block">Ab Tablet (versteckt auf Mobil)</option>
            <option value="d-block d-md-none">Nur auf Mobil</option>
            <option value="d-none d-lg-block">Nur auf Desktop</option>
            <option value="d-block d-lg-none">Versteckt auf Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_nav_demo") {
      const wrapper = document.createElement("div");
      const clanFromSettings = (typeof CLANNAME === "string" ? CLANNAME : "").trim();
      const brandPlaceholder = clanFromSettings ? "Aktuell aus Einstellungen: " + clanFromSettings : "z. B. Nexpell (wenn Clanname nicht gesetzt)";
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Brand-Name (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="" data-bs-toggle="tooltip" data-bs-placement="top" title="" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Logo-Bild (optional)</label>
          <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control form-control-sm" data-nx-field="image" placeholder="/images/logo.svg" data-bs-toggle="tooltip" data-bs-placement="top" title="Wenn ein Logo gesetzt ist, wird es anstelle des Text-Brand-Namens angezeigt." />
            <button type="button" class="btn btn-outline-secondary" data-nx-navlogo-upload-btn title="Logo hochladen">Hochladen</button>
          </div>
          <input type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" class="d-none" data-nx-navlogo-upload-input />
        </div>
        <div class="mb-2">
          <label class="form-label small">Menüquelle</label>
          <select class="form-select form-select-sm" data-nx-field="menuSource" data-bs-toggle="tooltip" data-bs-placement="top" title="Bei Plugin-Navigation kommen Einträge aus der Datenbank zusammenhängend mit dem Plugin &quot;Navigation.&quot; Logo, Padding, Hover-Effekt bleiben anpassbar.">
            <option value="custom">Eigenes Menü (im Canvas bearbeiten)</option>
            <option value="plugin">Plugin-Navigation (Kategorien & Links aus Admin)</option>
          </select>
        </div>
        <hr class="my-2">
        <div class="row g-2 mb-2">
          <div class="col-4">
            <label class="form-label small">Farbschema</label>
            <select class="form-select form-select-sm" data-nx-field="scheme">
              <option value="light">Hell (Standard)</option>
              <option value="dark">Dunkel</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label small">Schatten</label>
            <select class="form-select form-select-sm" data-nx-field="shadow">
              <option value="">Kein Schatten</option>
              <option value="shadow-sm">Leicht</option>
              <option value="shadow">Mittel</option>
              <option value="shadow-lg">Stark</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label small">Container</label>
            <select class="form-select form-select-sm" data-nx-field="container">
              <option value="fluid">Volle Breite (fluid)</option>
              <option value="fixed">Zentriert (container)</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer sichtbar</option>
            <option value="d-none d-md-block">Ab Tablet (versteckt auf Mobil)</option>
            <option value="d-block d-md-none">Nur auf Mobil</option>
            <option value="d-none d-lg-block">Nur auf Desktop</option>
            <option value="d-block d-lg-none">Versteckt auf Desktop</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label small">Vertikales Padding (oben/unten)</label>
          <input type="range" class="form-range" data-nx-field="paddingY" min="0" max="50" step="1" />
          <div class="d-flex justify-content-between small text-muted">
            <span>0 px</span>
            <span><span data-nx-nav-padding-value>26</span> px</span>
            <span>50 px</span>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Horizontales Padding (links/rechts)</label>
          <input type="range" class="form-range" data-nx-field="paddingX" min="0" max="50" step="1" />
          <div class="d-flex justify-content-between small text-muted">
            <span>0 px</span>
            <span><span data-nx-nav-paddingx-value>16</span> px</span>
            <span>50 px</span>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Hover-Effekt</label>
          <select class="form-select form-select-sm" data-nx-field="hoverEffect">
            <option value="none">Kein Effekt (Standard)</option>
            <option value="default">Linie von links</option>
            <option value="center">Linie aus der Mitte</option>
            <option value="swipe">Linie von links, raus nach rechts</option>
          </select>
        </div>
        <hr class="my-2">
        <div class="mb-2">
          <label class="form-label small">Overlay über erstem Hero (transparent)</label>
          <select class="form-select form-select-sm" data-nx-field="overlayMode">
            <option value="0">Aus</option>
            <option value="1">An</option>
          </select>
          <div class="form-text small text-muted">Legt die Navbar transparent über den ersten Abschnitt.</div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Overlay-Textfarbe</label>
          <select class="form-select form-select-sm" data-nx-field="overlayTextMode">
            <option value="light">Hell</option>
            <option value="dark">Dunkel</option>
          </select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Füllen nach Scroll</label>
            <select class="form-select form-select-sm" data-nx-field="scrollFill">
              <option value="0">Aus</option>
              <option value="1">An</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Offset (px)</label>
            <input type="number" class="form-control form-control-sm" data-nx-field="scrollFillOffset" min="0" max="9999" step="1" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Schatten (gefüllt)</label>
          <select class="form-select form-select-sm" data-nx-field="filledShadow">
            <option value="">Kein Schatten</option>
            <option value="shadow-sm">Leicht</option>
            <option value="shadow">Mittel</option>
            <option value="shadow-lg">Stark</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
      const brandInput = wrapper.querySelector('[data-nx-field="title"]');
      if (brandInput) {
        brandInput.placeholder = brandPlaceholder;
        brandInput.setAttribute("title", clanFromSettings
          ? "Priorität hat der Clanname aus den Backend-Einstellungen (aktuell: \"" + clanFromSettings + "\"). Feld leer lassen = Clanname anzeigen; hier nur Override eingeben, wenn gewünscht."
          : "Priorität hat der Clanname aus den Backend-Einstellungen. Wenn dort nichts gesetzt ist, erscheint dieser Text (oder ein Logo-Bild).");
      }
      // Bei Wechsel auf "Plugin-Navigation" Menü aus Admin laden und setzen (editierbar)
      const menuSourceSelect = wrapper.querySelector('[data-nx-field="menuSource"]');
      if (menuSourceSelect) {
        menuSourceSelect.addEventListener("change", async function () {
          if (this.value !== "plugin" || !currentSettingsItem) return;
          try {
            var res = await fetch(RENDER_ENDPOINT, {
              method: "POST",
              headers: { "Content-Type": "application/json", "X-CSRF-Token": CSRF },
              body: JSON.stringify({ action: "get_plugin_nav_menu", csrf: CSRF }),
              credentials: "same-origin",
            });
            var data = await res.json();
            if (data && data.ok && Array.isArray(data.menu)) {
              var obj = {};
              try { obj = JSON.parse(currentSettingsItem.getAttribute("data-nx-settings") || "{}"); } catch (e) {}
              obj.menuSource = "plugin";
              obj.menu = data.menu;
              currentSettingsItem.setAttribute("data-nx-settings", JSON.stringify(obj));
              if (settingsTextarea) settingsTextarea.value = JSON.stringify(obj, null, 2);
              if (typeof saveState === "function") await saveState();
              if (typeof renderInto === "function") await renderInto(currentSettingsItem);
            }
          } catch (e) { console.error("Plugin menu fetch failed", e); }
        });
      }

      // Live-Vorschau für Schema / Schatten / Container / Padding im Builder anpassen
      const schemeSelect = wrapper.querySelector('[data-nx-field="scheme"]');
      const shadowSelect = wrapper.querySelector('[data-nx-field="shadow"]');
      const containerSelect = wrapper.querySelector('[data-nx-field="container"]');
      const paddingInput = wrapper.querySelector('[data-nx-field="paddingY"]');
      const paddingXInput = wrapper.querySelector('[data-nx-field="paddingX"]');
      const hoverEffectSelect = wrapper.querySelector('[data-nx-field="hoverEffect"]');
      const overlayModeSelect = wrapper.querySelector('[data-nx-field="overlayMode"]');
      const overlayTextModeSelect = wrapper.querySelector('[data-nx-field="overlayTextMode"]');
      const scrollFillSelect = wrapper.querySelector('[data-nx-field="scrollFill"]');
      const scrollFillOffsetInput = wrapper.querySelector('[data-nx-field="scrollFillOffset"]');
      const filledShadowSelect = wrapper.querySelector('[data-nx-field="filledShadow"]');
      const paddingValueLabel = wrapper.querySelector("[data-nx-nav-padding-value]");
      const paddingXValueLabel = wrapper.querySelector("[data-nx-nav-paddingx-value]");
      const applyNavDemoPreview = () => {
        if (!currentSettingsItem) return;
        const nav = currentSettingsItem.querySelector(".nx-live-content nav.navbar");
        if (!nav) return;
        const scheme = schemeSelect ? schemeSelect.value || "light" : "light";
        const shadow = shadowSelect ? shadowSelect.value || "" : "";
        const containerMode = containerSelect ? containerSelect.value || "fluid" : "fluid";
        const padRaw = paddingInput ? (paddingInput.value || "").trim() : "";
        const padNumeric = padRaw && /^[0-9]+$/.test(padRaw) ? parseInt(padRaw, 10) : null;
        const padY = padNumeric !== null ? padNumeric + "px" : padRaw;
        const padXRaw = paddingXInput ? (paddingXInput.value || "").trim() : "";
        const padXNumeric = padXRaw && /^[0-9]+$/.test(padXRaw) ? parseInt(padXRaw, 10) : null;
        const padX = padXNumeric !== null ? padXNumeric + "px" : padXRaw;
        const hoverEffect = hoverEffectSelect ? hoverEffectSelect.value || "default" : "default";
        const overlayMode = overlayModeSelect ? overlayModeSelect.value === "1" : false;
        const overlayTextMode = overlayTextModeSelect ? overlayTextModeSelect.value || "light" : "light";
        // Overlay + ScrollFill sollen zusammen funktionieren:
        // Wenn Overlay aktiv ist, ist ScrollFill standardmäßig AN (sonst wirkt es "weiß & statisch").
        if (overlayMode && scrollFillSelect && scrollFillSelect.value !== "1") {
          scrollFillSelect.value = "1";
        }
        const scrollFill = scrollFillSelect ? scrollFillSelect.value === "1" : false;
        const scrollFillOffsetRaw = scrollFillOffsetInput ? String(scrollFillOffsetInput.value || "").trim() : "";
        const scrollFillOffset = scrollFillOffsetRaw && /^[0-9]+$/.test(scrollFillOffsetRaw) ? parseInt(scrollFillOffsetRaw, 10) : 80;
        const filledShadow = filledShadowSelect ? filledShadowSelect.value || "" : "";

        // Farbschema
        nav.classList.remove("navbar-light", "navbar-dark", "bg-white", "bg-dark", "border-bottom");
        nav.classList.remove("bg-transparent");
        if (overlayMode) {
          nav.classList.add("bg-transparent");
          if (overlayTextMode === "dark") nav.classList.add("navbar-light");
          else nav.classList.add("navbar-dark");
        } else if (scheme === "dark") {
          nav.classList.add("navbar-dark", "bg-dark");
        } else {
          // Keine zusätzliche Border-Bottom-Linie – die Unterstreichungs-Effekte übernehmen die Betonung
          nav.classList.add("navbar-light", "bg-white");
        }

        // Schatten
        nav.classList.remove("shadow-sm", "shadow", "shadow-lg");
        if (!overlayMode && shadow) nav.classList.add(shadow);
        // Zusätzliche, explizite Schatten im Builder setzen, falls Theme/Bootstrap die Utility-Klassen überschreibt
        nav.style.boxShadow = "";
        if (!overlayMode && shadow === "shadow-sm") {
          nav.style.boxShadow = "0 .125rem .25rem rgba(0,0,0,.075)";
        } else if (!overlayMode && shadow === "shadow") {
          nav.style.boxShadow = "0 .5rem 1rem rgba(0,0,0,.15)";
        } else if (!overlayMode && shadow === "shadow-lg") {
          nav.style.boxShadow = "0 1rem 3rem rgba(15,23,42,.25)";
        }

        // Vertikales Padding: auf die Links anwenden (.navbar-nav .nav-link),
        // damit der Hover-Hintergrund genau so hoch ist wie die Navbar.
        const navLinks = nav.querySelectorAll(".navbar-nav .nav-link");
        navLinks.forEach((linkEl) => {
          linkEl.style.paddingTop = "";
          linkEl.style.paddingBottom = "";
          linkEl.style.paddingLeft = "";
          linkEl.style.paddingRight = "";
          if (padY) {
            linkEl.style.paddingTop = padY;
            linkEl.style.paddingBottom = padY;
          }
          if (padX) {
            linkEl.style.paddingLeft = padX;
            linkEl.style.paddingRight = padX;
          }
          // Hover-Effekt-Klassen setzen
          linkEl.classList.remove("nx-nav-effect-default", "nx-nav-effect-center", "nx-nav-effect-swipe");
          if (hoverEffect === "default") {
            linkEl.classList.add("nx-nav-effect-default");
          } else if (hoverEffect === "center") {
            linkEl.classList.add("nx-nav-effect-center");
          } else if (hoverEffect === "swipe") {
            linkEl.classList.add("nx-nav-effect-swipe");
          } // "none" = keine Klasse
        });
        if (paddingValueLabel && padNumeric !== null) {
          paddingValueLabel.textContent = String(padNumeric);
        }
        if (paddingXValueLabel && padXNumeric !== null) {
          paddingXValueLabel.textContent = String(padXNumeric);
        }

        // Padding-Wert auch direkt in den Widget-Settings speichern,
        // damit er im Live-Builder erhalten bleibt (z.B. nach erneutem Öffnen / globalem Speichern).
        if (currentSettingsItem) {
          try {
            const rawSettings = currentSettingsItem.getAttribute("data-nx-settings") || "{}";
            const parsed = rawSettings.trim() ? JSON.parse(rawSettings) : {};
            parsed.paddingY = padY || "";
            parsed.paddingX = padX || "";
            parsed.hoverEffect = hoverEffect;
            parsed.overlayMode = overlayMode;
            parsed.overlayTextMode = overlayTextMode;
            parsed.scrollFill = scrollFill;
            parsed.scrollFillOffset = scrollFillOffset;
            parsed.filledShadow = filledShadow;
            currentSettingsItem.setAttribute("data-nx-settings", JSON.stringify(parsed));
            if (settingsTextarea) {
              settingsTextarea.value = JSON.stringify(parsed, null, 2);
            }
          } catch (err) {
            console.warn("Konnte Nav-Padding nicht in Settings schreiben", err);
          }
        }

        nav.setAttribute("data-nx-overlay", overlayMode ? "1" : "0");
        nav.setAttribute("data-nx-overlay-text", overlayTextMode);
        // Für die gefüllte Hintergrundfarbe (scheme light/dark) – damit Header+Nav identisch zur standalone Nav wirkt
        nav.setAttribute("data-nx-fill-scheme", scheme);
        nav.setAttribute("data-nx-scrollfill", scrollFill ? "1" : "0");
        nav.setAttribute("data-nx-scrollfill-offset", String(scrollFillOffset));
        nav.setAttribute("data-nx-filled-shadow", filledShadow);

        // Container-Typ
        const inner = nav.querySelector(".container, .container-fluid");
        if (inner) {
          inner.classList.remove("container", "container-fluid");
          if (containerMode === "fixed") {
            inner.classList.add("container");
          } else {
            inner.classList.add("container-fluid");
          }
          // WICHTIG: Immer nx-keep-container setzen, damit der Builder dieses Demo-Container-Layout nicht auf volle Breite zieht
          inner.classList.add("nx-keep-container");
        }
        // Nav-Marker-Klasse für Builder (fixed vs. fluid)
        nav.classList.remove("nx-nav-fixed", "nx-nav-fluid");
        nav.classList.add(containerMode === "fixed" ? "nx-nav-fixed" : "nx-nav-fluid");
      };

      const applyNavLogoPreview = (item, logoUrl) => {
        if (!item || !logoUrl) return;
        const nav = item.querySelector(".nx-live-content nav.navbar");
        if (!nav) return;
        const brand = nav.querySelector(".navbar-brand");
        if (!brand) return;
        // Ersetze den Brand-Inhalt durch ein Logo-Bild mit finaler Höhe
        brand.classList.remove("d-flex", "align-items-center", "gap-2");
        brand.innerHTML =
          '<img src="' +
          logoUrl +
          '" alt="" style="max-height:calc(70px);height:70px;" class="d-inline-block align-text-bottom" data-nx-inline="image" title="Klick: Logo ändern">';
      };
      // Initialwerte aus Settings übernehmen
      if (paddingInput) {
        let initial = 0;
        if (typeof settings.paddingY === "string") {
          const m = settings.paddingY.match(/([0-9]+)/);
          if (m) initial = parseInt(m[1], 10);
        }
        if (!Number.isFinite(initial)) initial = 0;
        paddingInput.value = String(initial);
        if (paddingValueLabel) paddingValueLabel.textContent = String(initial);
      }
      if (paddingXInput) {
        let initialX = 16;
        if (typeof settings.paddingX === "string") {
          const m2 = settings.paddingX.match(/([0-9]+)/);
          if (m2) initialX = parseInt(m2[1], 10);
        }
        if (!Number.isFinite(initialX)) initialX = 16;
        paddingXInput.value = String(initialX);
        if (paddingXValueLabel) paddingXValueLabel.textContent = String(initialX);
      }
      if (hoverEffectSelect) {
        const initialEffect =
          typeof settings.hoverEffect === "string" && settings.hoverEffect
            ? settings.hoverEffect
            : "default";
        hoverEffectSelect.value = initialEffect;
      }

      // Fallback: Wenn Settings im JSON fehlen (z.B. nach Bundle/Reload),
      // nehmen wir die bereits gerenderten Nav-Attribute als Quelle.
      const navEl = currentSettingsItem
        ? currentSettingsItem.querySelector(".nx-live-content nav.nx-nav-core-demo")
        : null;
      const navOverlayAttr = navEl ? navEl.getAttribute("data-nx-overlay") : null;
      const navScrollFillAttr = navEl ? navEl.getAttribute("data-nx-scrollfill") : null;
      const navOverlayTextAttr = navEl ? navEl.getAttribute("data-nx-overlay-text") : null;
      const navOffsetAttr = navEl ? navEl.getAttribute("data-nx-scrollfill-offset") : null;
      const navFilledShadowAttr = navEl ? navEl.getAttribute("data-nx-filled-shadow") : null;

      const nxToBool = (v, fallback = false) => {
        if (typeof v === "boolean") return v;
        if (typeof v === "number") return v === 1;
        if (typeof v === "string") {
          const s = v.trim().toLowerCase();
          if (s === "1" || s === "true" || s === "yes" || s === "on") return true;
          if (s === "0" || s === "false" || s === "no" || s === "off" || s === "") return false;
        }
        return fallback;
      };

      const setSelectValueSafe = (sel, val, fallbackVal) => {
        if (!sel) return;
        const v = String(val ?? "");
        const exists = Array.from(sel.options || []).some((o) => o && o.value === v);
        if (exists) {
          sel.value = v;
          return;
        }
        const fb = String(fallbackVal ?? "");
        const fbExists = Array.from(sel.options || []).some((o) => o && o.value === fb);
        sel.value = fbExists ? fb : (sel.options && sel.options[0] ? sel.options[0].value : "");
      };

      const overlayModeFallback = navOverlayAttr === "1";
      const scrollFillFallback = navScrollFillAttr === "1";
      const offsetFallback = navOffsetAttr && /^[0-9]+$/.test(String(navOffsetAttr)) ? String(navOffsetAttr) : "80";
      const overlayTextFallback = navOverlayTextAttr === "dark" ? "dark" : "light";
      const filledShadowFallback = typeof navFilledShadowAttr === "string" && navFilledShadowAttr ? navFilledShadowAttr : "";

      // Defaults: im Header+Navbar Preset sollen diese beiden Optionen nachvollziehbar AN sein.
      setSelectValueSafe(
        overlayModeSelect,
        nxToBool(settings && settings.overlayMode, true) ? "1" : "0",
        "1"
      );
      if (overlayTextModeSelect) overlayTextModeSelect.value =
        (settings && typeof settings.overlayTextMode === "string" && settings.overlayTextMode) || overlayTextFallback;
      setSelectValueSafe(
        scrollFillSelect,
        nxToBool(settings && settings.scrollFill, true) ? "1" : "0",
        "1"
      );
      if (scrollFillOffsetInput) scrollFillOffsetInput.value =
        settings && typeof settings.scrollFillOffset !== "undefined" ? String(settings.scrollFillOffset) : offsetFallback;
      setSelectValueSafe(
        filledShadowSelect,
        (settings && typeof settings.filledShadow === "string" ? settings.filledShadow : "") || filledShadowFallback,
        ""
      );

      // Wenn Selects aus irgendeinem Grund leer bleiben, hart auf Defaults setzen.
      if (overlayModeSelect && (overlayModeSelect.value !== "0" && overlayModeSelect.value !== "1")) {
        overlayModeSelect.value = "1";
      }
      if (scrollFillSelect && (scrollFillSelect.value !== "0" && scrollFillSelect.value !== "1")) {
        scrollFillSelect.value = "1";
      }

      [
        schemeSelect,
        shadowSelect,
        containerSelect,
        paddingInput,
        paddingXInput,
        hoverEffectSelect,
        overlayModeSelect,
        overlayTextModeSelect,
        scrollFillSelect,
        scrollFillOffsetInput,
        filledShadowSelect,
      ].forEach((sel) => {
        if (sel) {
          sel.addEventListener("change", applyNavDemoPreview);
        }
      });
      if (paddingInput) {
        paddingInput.addEventListener("input", applyNavDemoPreview);
      }
      if (paddingXInput) {
        paddingXInput.addEventListener("input", applyNavDemoPreview);
      }
      if (scrollFillOffsetInput) {
        scrollFillOffsetInput.addEventListener("input", applyNavDemoPreview);
      }

      // Direkt einmal aufrufen, damit die Builder-Vorschau (Transparenz/Background)
      // exakt zu den aktuell gerenderten Nav-Attributen passt.
      applyNavDemoPreview();

      // Defaults auch in den Settings persistieren, damit beim nächsten Öffnen
      // die Selects nicht "leer" wirken (Header+Navbar soll nachvollziehbar AN sein).
      try {
        if (currentSettingsItem) {
          const raw = currentSettingsItem.getAttribute("data-nx-settings") || "{}";
          const parsed = raw.trim() ? JSON.parse(raw) : {};
          if (typeof parsed.overlayMode === "undefined") parsed.overlayMode = true;
          if (typeof parsed.scrollFill === "undefined") parsed.scrollFill = true;
          currentSettingsItem.setAttribute("data-nx-settings", JSON.stringify(parsed));
          if (settingsTextarea) settingsTextarea.value = JSON.stringify(parsed, null, 2);
        }
      } catch (e) {}

      // Upload-Button für Logo-Bild anbinden (gleiches Muster wie Header/Images)
      const navLogoUploadBtn = wrapper.querySelector("[data-nx-navlogo-upload-btn]");
      const navLogoUploadInput = wrapper.querySelector("[data-nx-navlogo-upload-input]");
      const navLogoField = wrapper.querySelector('[data-nx-field="image"]');
      const vars = window.NXB_BUILDER_VARS || {};
      const uploadUrl = (vars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      if (navLogoUploadBtn && navLogoUploadInput && navLogoField && uploadUrl) {
        navLogoUploadBtn.addEventListener("click", function () { navLogoUploadInput.click(); });
        navLogoUploadInput.addEventListener("change", function () {
          const file = navLogoUploadInput.files && navLogoUploadInput.files[0];
          if (!file) return;
          const fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", vars.CSRF || "");
          navLogoUploadBtn.disabled = true;
          fetch(uploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then((r) => r.json())
            .then(async (data) => {
              if (data && data.ok && data.url) {
                navLogoField.value = data.url;
                navLogoField.dispatchEvent(new Event("input", { bubbles: true }));
                // Aktives Nav-Widget im Canvas suchen (Bearbeitungszustand) und Logo sofort darin austauschen
                const activeNavItem =
                  document.querySelector('.nx-live-item.nx-live-active[data-nx-key="core_nav_demo"]') ||
                  currentSettingsItem ||
                  null;
                if (activeNavItem) {
                  applyNavLogoPreview(activeNavItem, data.url);
                }
                // Settings speichern, damit das Logo nach Reload erhalten bleibt.
                // WICHTIG: Bestehende Settings (z. B. menu für Dropdowns) vom Item übernehmen,
                // nur Formularfelder überschreiben – sonst gehen Dropdowns und Gear-Icons verloren.
                if (activeNavItem && settingsFields) {
                  let obj = {};
                  try {
                    obj = JSON.parse(activeNavItem.getAttribute("data-nx-settings") || "{}");
                  } catch (e) {}
                  settingsFields.querySelectorAll("[data-nx-field]").forEach(function (el) {
                    const key = el.getAttribute("data-nx-field");
                    if (!key) return;
                    if (el.type === "checkbox") obj[key] = !!el.checked;
                    else if (el.tagName === "SELECT") obj[key] = el.value;
                    else obj[key] = el.value;
                  });
                  // Speziell für Navigation: Padding-Werte wieder als px normalisieren,
                  // damit das Frontend korrekt rendert (nicht "26" sondern "26px").
                  const currentKey = activeNavItem.getAttribute("data-nx-key") || "";
                  if (currentKey === "core_nav_demo") {
                    if (typeof obj.paddingY === "string" && /^[0-9]+$/.test(obj.paddingY)) {
                      obj.paddingY = obj.paddingY + "px";
                    }
                    if (typeof obj.paddingX === "string" && /^[0-9]+$/.test(obj.paddingX)) {
                      obj.paddingX = obj.paddingX + "px";
                    }
                  }
                  activeNavItem.setAttribute("data-nx-settings", JSON.stringify(obj));
                  if (settingsTextarea) settingsTextarea.value = JSON.stringify(obj, null, 2);
                }
                if (typeof saveState === "function") {
                  await saveState();
                }
                // Nach Logo-Upload Vorschau der Navigation aktualisieren (Padding, Farben, Effekt)
                if (typeof applyNavDemoPreview === "function") {
                  applyNavDemoPreview();
                }
              }
            })
            .finally(() => {
              navLogoUploadBtn.disabled = false;
              navLogoUploadInput.value = "";
            });
        });
      }
    } else if (key === "core_header") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Überschrift" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="z. B. Bereichsname oder Eyecatcher" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Bild (optional)</label>
          <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control form-control-sm" data-nx-field="image" placeholder="/images/content/…" />
            <button type="button" class="btn btn-outline-secondary" data-nx-header-upload-btn title="Bild hochladen">Hochladen</button>
          </div>
          <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" data-nx-header-upload-input />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Bildhöhe (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="imageHeight" placeholder="z. B. 280" />
          </div>
          <div class="col-6">
            <label class="form-label small">Einheit</label>
            <select class="form-select form-select-sm" data-nx-field="imageHeightUnit">
              <option value="px">px</option>
              <option value="rem">rem</option>
              <option value="vh">vh</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Vignette Größe <span class="nx-vignette-size-val text-muted">40</span>%</label>
          <input type="range" class="form-range" data-nx-field="vignetteSize" min="0" max="100" value="40" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Vignette Deckkraft <span class="nx-vignette-opacity-val text-muted">50</span>%</label>
          <input type="range" class="form-range" data-nx-field="vignetteOpacity" min="0" max="100" value="50" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Level</label>
            <select class="form-select form-select-sm" data-nx-field="level">
              <option value="1">H1</option>
              <option value="2">H2</option>
              <option value="3">H3</option>
              <option value="4">H4</option>
              <option value="5">H5</option>
              <option value="6">H6</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Größe (optional)</label>
            <select class="form-select form-select-sm" data-nx-field="display">
              <option value="">Standard</option>
              <option value="display-6">Display 6</option>
              <option value="display-5">Display 5</option>
              <option value="display-4">Display 4</option>
              <option value="display-3">Display 3</option>
              <option value="display-2">Display 2</option>
              <option value="display-1">Display 1</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
      var headerUploadBtn = wrapper.querySelector("[data-nx-header-upload-btn]");
      var headerUploadInput = wrapper.querySelector("[data-nx-header-upload-input]");
      var headerImgInput = wrapper.querySelector("[data-nx-field=\"image\"]");
      var headerVars = window.NXB_BUILDER_VARS || {};
      var headerUploadUrl = (headerVars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      if (headerUploadBtn && headerUploadInput && headerImgInput && headerUploadUrl) {
        headerUploadBtn.addEventListener("click", function () { headerUploadInput.click(); });
        headerUploadInput.addEventListener("change", function () {
          var file = headerUploadInput.files && headerUploadInput.files[0];
          if (!file) return;
          var fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", headerVars.CSRF || "");
          headerUploadBtn.disabled = true;
          fetch(headerUploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (data && data.ok && data.url) {
                headerImgInput.value = data.url;
                headerImgInput.dispatchEvent(new Event("input", { bubbles: true }));
                if (typeof saveState === "function") saveState();
              }
            })
            .finally(function () { headerUploadBtn.disabled = false; headerUploadInput.value = ""; });
        });
      }
      var vignetteSizeRange = wrapper.querySelector("[data-nx-field=\"vignetteSize\"]");
      var vignetteOpacityRange = wrapper.querySelector("[data-nx-field=\"vignetteOpacity\"]");
      var sizeValSpan = wrapper.querySelector(".nx-vignette-size-val");
      var opacityValSpan = wrapper.querySelector(".nx-vignette-opacity-val");
      if (vignetteSizeRange && sizeValSpan) {
        vignetteSizeRange.addEventListener("input", function () { sizeValSpan.textContent = vignetteSizeRange.value; });
      }
      if (vignetteOpacityRange && opacityValSpan) {
        vignetteOpacityRange.addEventListener("input", function () { opacityValSpan.textContent = vignetteOpacityRange.value; });
      }
    } else if (key === "core_button") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Label</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="label" />
          </div>
          <div class="col-6">
            <label class="form-label small">Link (URL)</label>
            <input type="url" class="form-control form-control-sm" placeholder="https://…" data-nx-field="url" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Zusätzliche CSS-Klassen (optional)</label>
          <input type="text" class="form-control form-control-sm" placeholder="z.B. rounded-pill" data-nx-field="class" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Stil</label>
            <select class="form-select form-select-sm" data-nx-field="style">
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
              <option value="success">Success</option>
              <option value="danger">Danger</option>
              <option value="warning">Warning</option>
              <option value="info">Info</option>
              <option value="light">Light</option>
              <option value="dark">Dark</option>
              <option value="outline-primary">Outline Primary</option>
              <option value="outline-secondary">Outline Secondary</option>
              <option value="outline-success">Outline Success</option>
              <option value="outline-danger">Outline Danger</option>
              <option value="outline-warning">Outline Warning</option>
              <option value="outline-info">Outline Info</option>
              <option value="outline-light">Outline Light</option>
              <option value="outline-dark">Outline Dark</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Größe</label>
            <select class="form-select form-select-sm" data-nx-field="size">
              <option value="md">Normal</option>
              <option value="sm">Klein</option>
              <option value="lg">Groß</option>
            </select>
          </div>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" id="nx-field-block" data-nx-field="block">
          <label class="form-check-label small" for="nx-field-block">Über gesamte Breite</label>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" id="nx-field-targetblank" data-nx-field="targetBlank">
          <label class="form-check-label small" for="nx-field-targetblank">In neuem Tab öffnen</label>
        </div>
        <hr class="my-2">
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_text") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Text (einfach)</label>
          <textarea class="form-control form-control-sm" rows="3" data-nx-field="text"></textarea>
          <div class="form-text small">Für komplexere Inhalte kannst du unten den HTML/JSON-Block verwenden.</div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_image") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Bild</label>
          <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control form-control-sm" data-nx-field="src" placeholder="https://… oder /images/content/…" />
            <button type="button" class="btn btn-outline-secondary" data-nx-upload-btn title="Bild hochladen">Hochladen</button>
          </div>
          <small class="text-muted d-block mt-1">oder Bild per Drag &amp; Drop hier ablegen</small>
          <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" data-nx-upload-input />
        </div>
        <div class="mb-2">
          <label class="form-label small">Alt-Text</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="alt" placeholder="Barrierefreier Beschreibungstext" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Bildunterschrift</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="caption" placeholder="Optionale Caption unter dem Bild" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Link (optional)</label>
          <input type="url" class="form-control form-control-sm" data-nx-field="href" placeholder="Ziel-URL beim Klick" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Seitenverhältnis (optional)</label>
            <select class="form-select form-select-sm" data-nx-field="ratio">
              <option value="">Automatisch</option>
              <option value="16:9">16:9</option>
              <option value="4:3">4:3</option>
              <option value="1:1">1:1</option>
            </select>
          </div>
        </div>
        <div class="form-check form-switch mb-1">
          <input class="form-check-input" type="checkbox" id="nx-field-img-rounded" data-nx-field="rounded">
          <label class="form-check-label small" for="nx-field-img-rounded">Abgerundete Ecken</label>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" id="nx-field-img-shadow" data-nx-field="shadow">
          <label class="form-check-label small" for="nx-field-img-shadow">Leichter Schatten</label>
        </div>
        <hr class="my-2">
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
      const uploadBtn = wrapper.querySelector("[data-nx-upload-btn]");
      const uploadInput = wrapper.querySelector("[data-nx-upload-input]");
      const srcInput = wrapper.querySelector("[data-nx-field=\"src\"]");
      const vars = window.NXB_BUILDER_VARS || {};
      const uploadUrl = (vars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      if (uploadBtn && uploadInput && srcInput && uploadUrl) {
        uploadBtn.addEventListener("click", () => uploadInput.click());
        uploadInput.addEventListener("change", () => {
          const file = uploadInput.files && uploadInput.files[0];
          if (!file) return;
          const fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", vars.CSRF || "");
          uploadBtn.disabled = true;
          fetch(uploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then((r) => r.json())
            .then((data) => {
              if (data && data.ok && data.url) {
                srcInput.value = data.url;
                srcInput.dispatchEvent(new Event("input", { bubbles: true }));
                if (typeof saveState === "function") saveState();
              }
            })
            .finally(() => { uploadBtn.disabled = false; uploadInput.value = ""; });
        });
      }
    } else if (key === "core_hero" || key === "core_hero_split") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Starker Hero-Titel" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurzer Eyecatcher über der Überschrift" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Text</label>
          <textarea class="form-control form-control-sm" rows="3" data-nx-field="text" placeholder="Kurze Beschreibung oder Nutzenargumente"></textarea>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Haupt-Button Label</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="primaryLabel" placeholder="z.B. Jetzt starten" />
          </div>
          <div class="col-6">
            <label class="form-label small">Haupt-Button URL</label>
            <input type="url" class="form-control form-control-sm" data-nx-field="primaryUrl" placeholder="https://…" />
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Sekundär-Button Label</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="secondaryLabel" placeholder="Optional, z.B. Mehr erfahren" />
          </div>
          <div class="col-6">
            <label class="form-label small">Sekundär-Button URL</label>
            <input type="url" class="form-control form-control-sm" data-nx-field="secondaryUrl" placeholder="https://…" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Hintergrundbild</label>
          <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control form-control-sm" data-nx-field="bgImage" placeholder="/images/content/…" />
            <button type="button" class="btn btn-outline-secondary" data-nx-hero-upload-btn title="Bild hochladen">Hochladen</button>
          </div>
          <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" data-nx-hero-upload-input />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Hintergrund</label>
            <select class="form-select form-select-sm" data-nx-field="bg">
              <option value="bg-dark">Dunkel</option>
              <option value="bg-primary">Primary</option>
              <option value="bg-light">Hell</option>
              <option value="">Transparent</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Textmodus</label>
            <select class="form-select form-select-sm" data-nx-field="mode">
              <option value="light">Hell (für dunklen Hintergrund)</option>
              <option value="dark">Dunkel (für hellen Hintergrund)</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Hero-Höhe</label>
            <select class="form-select form-select-sm" data-nx-field="heightMode">
              <option value="">Automatisch (Inhalt)</option>
              <option value="vh-40">Kompakt (ca. 40vh)</option>
              <option value="vh-50">Mittel (ca. 50vh)</option>
              <option value="vh-60">Groß (ca. 60vh)</option>
              <option value="vh-80">Sehr groß (ca. 80vh)</option>
              <option value="vh-100">Vollbild (100vh)</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Vertikales Padding</label>
            <select class="form-select form-select-sm" data-nx-field="padding">
              <option value="py-5">Standard</option>
              <option value="py-4">Kompakter</option>
              <option value="py-6">Groß</option>
              <option value="py-0">Ohne</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
      const heroUploadBtn = wrapper.querySelector("[data-nx-hero-upload-btn]");
      const heroUploadInput = wrapper.querySelector("[data-nx-hero-upload-input]");
      const heroBgInput = wrapper.querySelector("[data-nx-field=\"bgImage\"]");
      const heroVars = window.NXB_BUILDER_VARS || {};
      const heroUploadUrl = (heroVars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      if (heroUploadBtn && heroUploadInput && heroBgInput && heroUploadUrl) {
        heroUploadBtn.addEventListener("click", () => heroUploadInput.click());
        heroUploadInput.addEventListener("change", () => {
          const file = heroUploadInput.files && heroUploadInput.files[0];
          if (!file) return;
          const fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", heroVars.CSRF || "");
          heroUploadBtn.disabled = true;
          fetch(heroUploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then((r) => r.json())
            .then((data) => {
              if (data && data.ok && data.url) {
                heroBgInput.value = data.url;
                heroBgInput.dispatchEvent(new Event("input", { bubbles: true }));
                if (typeof saveState === "function") saveState();
              }
            })
            .finally(() => { heroUploadBtn.disabled = false; heroUploadInput.value = ""; });
        });
      }
    } else if (key === "core_container") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Container-Typ</label>
          <select class="form-select form-select-sm" data-nx-field="container">
            <option value="container">Container (zentriert, max-width)</option>
            <option value="container-fluid">Container Fluid (volle Breite)</option>
          </select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Padding</label>
            <select class="form-select form-select-sm" data-nx-field="padding">
              <option value="py-3">Klein (py-3)</option>
              <option value="py-4">Standard (py-4)</option>
              <option value="py-5">Groß (py-5)</option>
              <option value="py-0">Ohne</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Hintergrund (CSS-Klasse)</label>
            <input type="text" class="form-control form-control-sm" placeholder="z.B. bg-light" data-nx-field="bg" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
        <p class="small text-muted mb-0 mt-2">Widgets in den Container ziehen, um sie zu begrenzen.</p>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_row") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Container-Typ</label>
          <select class="form-select form-select-sm" data-nx-field="container">
            <option value="container">Container (zentriert)</option>
            <option value="container-fluid">Container Fluid (volle Breite)</option>
          </select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Padding</label>
            <select class="form-select form-select-sm" data-nx-field="padding">
              <option value="py-3">Klein (py-3)</option>
              <option value="py-4">Standard (py-4)</option>
              <option value="py-5">Groß (py-5)</option>
              <option value="py-0">Ohne</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Hintergrund (CSS-Klasse)</label>
            <input type="text" class="form-control form-control-sm" placeholder="z.B. bg-light" data-nx-field="bg" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
        <p class="small text-muted mb-0 mt-2">Nur Cols in die Row ziehen – dann in jede Col beliebige Blöcke.</p>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_col") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Breite (Bootstrap-Spalten)</label>
          <select class="form-select form-select-sm" data-nx-field="span">
            <option value="12">Ganz (12/12)</option>
            <option value="6">Halb (6/12)</option>
            <option value="4">Drittel (4/12)</option>
            <option value="3">Viertel (3/12)</option>
            <option value="8">2 Drittel (8/12)</option>
            <option value="9">3 Viertel (9/12)</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
        <p class="small text-muted mb-0 mt-2">Col muss in einer Row liegen. In die Col beliebige Blöcke ziehen.</p>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_section_full" || key === "core_section_two_col" || key === "core_section_three_col") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Hintergrundfarbe (CSS-Klasse)</label>
            <input type="text" class="form-control form-control-sm" placeholder="z.B. bg-light, bg-dark" data-nx-field="bg" />
          </div>
          <div class="col-6">
            <label class="form-label small">Padding</label>
            <select class="form-select form-select-sm" data-nx-field="padding">
              <option value="py-5">Standard (py-5)</option>
              <option value="py-4">Kompakt (py-4)</option>
              <option value="py-6">Groß (py-6)</option>
              <option value="py-0">Ohne</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Container-Typ</label>
          <select class="form-select form-select-sm" data-nx-field="container">
            <option value="container">Container</option>
            <option value="container-fluid">Container Fluid</option>
          </select>
        </div>
        <hr class="my-2">
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_feature_grid") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Spalten</label>
          <select class="form-select form-select-sm" data-nx-field="columns">
            <option value="2">2 Spalten</option>
            <option value="3">3 Spalten</option>
            <option value="4">4 Spalten</option>
          </select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Features</p>
        ${[1, 2, 3].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="mb-1">
            <label class="form-label small mb-0">Titel ${i}</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_title" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Text ${i}</label>
            <textarea class="form-control form-control-sm" rows="2" data-nx-field="item${i}_text"></textarea>
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Icon-Klasse ${i}</label>
            <div class="input-group input-group-sm">
              <input type="text" class="form-control" placeholder="z.B. bi-star" data-nx-field="item${i}_icon" />
              <button type="button" class="btn btn-outline-secondary nx-icon-picker-btn" data-icon-target="item${i}_icon">
                <i class="bi bi-grid-3x3-gap"></i>
              </button>
            </div>
          </div>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_faq") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="FAQ" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurze Einleitung zu den Fragen" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Effekt</label>
            <select class="form-select form-select-sm" data-nx-field="effect">
              <option value="slide">Slide</option>
              <option value="fade">Fade</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Caption-Stil</label>
            <select class="form-select form-select-sm" data-nx-field="captionStyle">
              <option value="dark">Dunkler Hintergrund</option>
              <option value="light">Heller Hintergrund</option>
              <option value="none">Ohne Hintergrund</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-indicators" data-nx-field="showIndicators" checked>
              <label class="form-check-label small" for="nx-slider-indicators">Indikatoren anzeigen</label>
            </div>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-controls" data-nx-field="showControls" checked>
              <label class="form-check-label small" for="nx-slider-controls">Pfeile anzeigen</label>
            </div>
          </div>
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-autoplay" data-nx-field="autoPlay" checked>
              <label class="form-check-label small" for="nx-slider-autoplay">Automatisch abspielen</label>
            </div>
            <div class="mb-1">
              <label class="form-label small mb-0">Intervall (ms)</label>
              <input type="number" min="1000" max="20000" step="500" class="form-control form-control-sm" data-nx-field="interval" placeholder="5000" />
            </div>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">FAQ-Einträge (bis zu 6 Fragen &amp; Antworten)</p>
        ${[1, 2, 3, 4, 5, 6].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="mb-1">
            <label class="form-label small mb-0">Frage ${i}</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_title" placeholder="Frage ${i}" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Antwort ${i}</label>
            <textarea class="form-control form-control-sm" rows="2" data-nx-field="item${i}_content" placeholder="Antwort ${i}"></textarea>
          </div>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_testimonials") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Was unsere Nutzer sagen" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurze Einleitung zu den Referenzen" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Spalten</label>
            <select class="form-select form-select-sm" data-nx-field="columns">
              <option value="1">1 Spalte</option>
              <option value="2">2 Spalten</option>
              <option value="3">3 Spalten</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Testimonials (bis zu 6 Referenzen)</p>
        ${[1, 2, 3, 4, 5, 6].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="mb-1">
            <label class="form-label small mb-0">Zitat ${i}</label>
            <textarea class="form-control form-control-sm" rows="2" data-nx-field="item${i}_quote" placeholder="„Großartiges Projekt…“"></textarea>
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Name ${i} (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_name" placeholder="Name" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Rolle (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_role" placeholder="z. B. Teamleiter" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Firma (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_company" placeholder="Firma / Projekt" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Bild (optional)</label>
            <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
              <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_image" placeholder="/images/content/…" />
            </div>
          </div>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_timeline") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Ablauf / Roadmap" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurze Einleitung zur Timeline" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Effekt</label>
            <select class="form-select form-select-sm" data-nx-field="effect">
              <option value="slide">Slide</option>
              <option value="fade">Fade</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Caption-Stil</label>
            <select class="form-select form-select-sm" data-nx-field="captionStyle">
              <option value="dark">Dunkler Hintergrund</option>
              <option value="light">Heller Hintergrund</option>
              <option value="none">Ohne Hintergrund</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-indicators" data-nx-field="showIndicators" checked>
              <label class="form-check-label small" for="nx-slider-indicators">Indikatoren anzeigen</label>
            </div>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-controls" data-nx-field="showControls" checked>
              <label class="form-check-label small" for="nx-slider-controls">Pfeile anzeigen</label>
            </div>
          </div>
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-autoplay" data-nx-field="autoPlay" checked>
              <label class="form-check-label small" for="nx-slider-autoplay">Automatisch abspielen</label>
            </div>
            <div class="mb-1">
              <label class="form-label small mb-0">Intervall (ms)</label>
              <input type="number" min="1000" max="20000" step="500" class="form-control form-control-sm" data-nx-field="interval" placeholder="5000" />
            </div>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Schritte (bis zu 8 Timeline-Einträge)</p>
        ${[1, 2, 3, 4, 5, 6, 7, 8].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="mb-1">
            <label class="form-label small mb-0">Titel ${i}</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_title" placeholder="Schritt ${i}" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Meta / Datum ${i} (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="item${i}_meta" placeholder="z. B. Q1 2026, Phase ${i}" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Beschreibung ${i} (optional)</label>
            <textarea class="form-control form-control-sm" rows="2" data-nx-field="item${i}_text" placeholder="Kurze Beschreibung"></textarea>
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Status ${i} (optional)</label>
            <select class="form-select form-select-sm" data-nx-field="item${i}_status">
              <option value="">Standard</option>
              <option value="done">Abgeschlossen</option>
              <option value="active">Aktiv</option>
              <option value="upcoming">Bevorstehend</option>
            </select>
          </div>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_slider") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Carousel-Titel (optional)" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurze Beschreibung des Carousels" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Effekt</label>
            <select class="form-select form-select-sm" data-nx-field="effect">
              <option value="slide">Slide</option>
              <option value="fade">Fade</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Caption-Stil</label>
            <select class="form-select form-select-sm" data-nx-field="captionStyle">
              <option value="dark">Dunkler Hintergrund</option>
              <option value="light">Heller Hintergrund</option>
              <option value="none">Ohne Hintergrund</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-indicators" data-nx-field="showIndicators" checked>
              <label class="form-check-label small" for="nx-slider-indicators">Indikatoren anzeigen</label>
            </div>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-controls" data-nx-field="showControls" checked>
              <label class="form-check-label small" for="nx-slider-controls">Pfeile anzeigen</label>
            </div>
          </div>
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-slider-autoplay" data-nx-field="autoPlay" checked>
              <label class="form-check-label small" for="nx-slider-autoplay">Automatisch abspielen</label>
            </div>
            <div class="mb-1">
              <label class="form-label small mb-0">Intervall (ms)</label>
              <input type="number" min="1000" max="20000" step="500" class="form-control form-control-sm" data-nx-field="interval" placeholder="5000" />
            </div>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Slides (bis zu 6 Bilder mit Caption)</p>
        ${[1, 2, 3, 4, 5, 6].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="mb-1">
            <label class="form-label small mb-0">Bild ${i}</label>
            <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
              <input type="text" class="form-control" placeholder="/images/content/…" data-nx-field="item${i}_src" />
              <button type="button" class="btn btn-outline-secondary nx-slider-upload-btn" data-slider-index="${i}">Hochladen</button>
            </div>
            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" data-nx-slider-upload-input data-slider-index="${i}" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Alt-Text ${i} (optional)</label>
            <input type="text" class="form-control form-control-sm" placeholder="Alt-Text" data-nx-field="item${i}_alt" />
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Caption ${i} (optional)</label>
            <textarea class="form-control form-control-sm" rows="2" data-nx-field="item${i}_caption" placeholder="Kurzbeschreibung"></textarea>
          </div>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
      const sliderVars = window.NXB_BUILDER_VARS || {};
      const sliderUploadUrl = (sliderVars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      wrapper.querySelectorAll(".nx-slider-upload-btn").forEach((btn) => {
        const idx = btn.getAttribute("data-slider-index");
        const input = wrapper.querySelector(`[data-nx-slider-upload-input][data-slider-index="${idx}"]`);
        const srcInput = wrapper.querySelector(`[data-nx-field="item${idx}_src"]`);
        if (!btn || !input || !srcInput || !sliderUploadUrl) return;
        btn.addEventListener("click", () => input.click());
        input.addEventListener("change", () => {
          const file = input.files && input.files[0];
          if (!file) return;
          const fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", sliderVars.CSRF || "");
          btn.disabled = true;
          fetch(sliderUploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then((r) => r.json())
            .then((data) => {
              if (data && data.ok && data.url) {
                srcInput.value = data.url;
                srcInput.dispatchEvent(new Event("input", { bubbles: true }));
                if (typeof saveState === "function") saveState();
              }
            })
            .finally(() => { btn.disabled = false; input.value = ""; });
        });
      });
    } else if (key === "core_pricing") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Unsere Preise" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurze Einleitung zur Preisübersicht" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-4">
            <label class="form-label small">Spalten</label>
            <select class="form-select form-select-sm" data-nx-field="columns">
              <option value="2">2 Pläne</option>
              <option value="3" selected>3 Pläne</option>
              <option value="4">4 Pläne</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center" selected>Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Pläne (bis zu 4 Pricing-Karten)</p>
        ${[1, 2, 3, 4].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="mb-1">
            <label class="form-label small mb-0">Plan ${i} – Name</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="plan${i}_name" placeholder="z. B. Starter" />
          </div>
          <div class="row g-2 mb-1">
            <div class="col-6">
              <label class="form-label small mb-0">Preis ${i}</label>
              <input type="text" class="form-control form-control-sm" data-nx-field="plan${i}_price" placeholder="z. B. 9" />
            </div>
            <div class="col-6">
              <label class="form-label small mb-0">Zeitraum ${i} (optional)</label>
              <input type="text" class="form-control form-control-sm" data-nx-field="plan${i}_period" placeholder="z. B. mtl." />
            </div>
          </div>
          <div class="mb-1">
            <label class="form-label small mb-0">Features ${i} (ein Punkt pro Zeile)</label>
            <textarea class="form-control form-control-sm" rows="3" data-nx-field="plan${i}_features" placeholder="Feature eins&#10;Feature zwei&#10;Feature drei"></textarea>
          </div>
          <div class="row g-2 mb-1">
            <div class="col-6">
              <label class="form-label small mb-0">Button-Label ${i} (optional)</label>
              <input type="text" class="form-control form-control-sm" data-nx-field="plan${i}_buttonLabel" placeholder="z. B. Jetzt starten" />
            </div>
            <div class="col-6">
              <label class="form-label small mb-0">Button-URL ${i} (optional)</label>
              <input type="text" class="form-control form-control-sm" data-nx-field="plan${i}_buttonUrl" placeholder="#" />
            </div>
          </div>
          <div class="form-check form-switch mb-1">
            <input class="form-check-input" type="checkbox" id="nx-plan${i}-featured" data-nx-field="plan${i}_featured">
            <label class="form-check-label small" for="nx-plan${i}-featured">Plan ${i} hervorheben</label>
          </div>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_collapse") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Collapse-Titel" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Text</label>
          <textarea class="form-control form-control-sm" rows="4" data-nx-field="text" placeholder="Inhalt für den Collapse-Block"></textarea>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="open" id="nx-collapse-open">
              <label class="form-check-label small" for="nx-collapse-open">Standard geöffnet</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="flush" id="nx-collapse-flush">
              <label class="form-check-label small" for="nx-collapse-flush">Ohne Rahmen</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="borderless" id="nx-collapse-borderless">
              <label class="form-check-label small" for="nx-collapse-borderless">Transparenter Hintergrund</label>
            </div>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_list_group") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Titel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="List-Group Titel" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Untertitel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="subtitle" placeholder="Kurze Beschreibung" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="flush" id="nx-lg-flush">
              <label class="form-check-label small" for="nx-lg-flush">Flush</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="numbered" id="nx-lg-numbered">
              <label class="form-check-label small" for="nx-lg-numbered">Nummeriert</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="interactive" id="nx-lg-interactive">
              <label class="form-check-label small" for="nx-lg-interactive">Interaktiv (Links)</label>
            </div>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Einträge (Text + optional Badge)</p>
        ${Array.from({ length: 10 }).map((_, i) => {
          const idx = i + 1;
          return `
          <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
            <div class="mb-1">
              <label class="form-label small mb-0">Text ${idx}</label>
              <input type="text" class="form-control form-control-sm" data-nx-field="item${idx}_text" placeholder="Listeneintrag ${idx}" />
            </div>
            <div class="mb-1">
              <label class="form-label small mb-0">Badge ${idx} (optional)</label>
              <input type="text" class="form-control form-control-sm" data-nx-field="item${idx}_badge" placeholder="Badge-Text oder Zahl" />
            </div>
          </div>
          `;
        }).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_link") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Link-Text</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="label" placeholder="Link-Text" />
        </div>
        <div class="mb-2">
          <label class="form-label small">URL</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="href" placeholder="https://…" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-4">
            <label class="form-label small">Stil</label>
            <select class="form-select form-select-sm" data-nx-field="style">
              <option value="primary">Primär</option>
              <option value="secondary">Sekundär</option>
              <option value="muted">Dezent</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Zentriert</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="underline" id="nx-link-underline">
              <label class="form-check-label small" for="nx-link-underline">Unterstrichen</label>
            </div>
          </div>
          <div class="col-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" data-nx-field="targetBlank" id="nx-link-target">
              <label class="form-check-label small" for="nx-link-target">In neuem Tab</label>
            </div>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_spacer") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Abstandsgröße</label>
          <select class="form-select form-select-sm" data-nx-field="size">
            <option value="py-1">Sehr klein (py-1)</option>
            <option value="py-2">Klein (py-2)</option>
            <option value="py-3">Mittel (py-3)</option>
            <option value="py-4">Standard (py-4)</option>
            <option value="py-5">Groß (py-5)</option>
            <option value="py-6">Sehr groß (py-6)</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_quote") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Zitat-Text</label>
          <textarea class="form-control form-control-sm" rows="4" data-nx-field="text" placeholder="Zitat hier eingeben …"></textarea>
        </div>
        <div class="mb-2">
          <label class="form-label small">Autor (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="author" placeholder="Name des Autors" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Quelle (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="source" placeholder="Buch, Link, …" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_gallery") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Spalten</label>
          <select class="form-select form-select-sm" data-nx-field="columns">
            <option value="2">2 Spalten</option>
            <option value="3">3 Spalten</option>
            <option value="4">4 Spalten</option>
          </select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <hr class="my-2">
        <p class="small text-muted mb-1">Bilder (URL, Hochladen oder per Drag &amp; Drop ablegen)</p>
        ${[1, 2, 3, 4, 5, 6].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <label class="form-label small mb-1">Bild ${i}</label>
          <div class="input-group input-group-sm mb-1 nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control" placeholder="/images/content/…" data-nx-field="item${i}_src" />
            <button type="button" class="btn btn-outline-secondary nx-gallery-upload-btn" data-gallery-index="${i}">Hochladen</button>
          </div>
          <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" data-nx-gallery-upload-input data-gallery-index="${i}" />
          <input type="text" class="form-control form-control-sm mt-1" placeholder="Alt-Text" data-nx-field="item${i}_alt" />
          <input type="text" class="form-control form-control-sm mt-1" placeholder="Bildunterschrift (optional)" data-nx-field="item${i}_caption" />
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
      const galVars = window.NXB_BUILDER_VARS || {};
      const galUploadUrl = (galVars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      wrapper.querySelectorAll(".nx-gallery-upload-btn").forEach((btn) => {
        const idx = btn.getAttribute("data-gallery-index");
        const input = wrapper.querySelector(`[data-nx-gallery-upload-input][data-gallery-index="${idx}"]`);
        const srcInput = wrapper.querySelector(`[data-nx-field="item${idx}_src"]`);
        if (btn && input && srcInput && galUploadUrl) {
          btn.addEventListener("click", () => input.click());
          input.addEventListener("change", () => {
            const file = input.files && input.files[0];
            if (!file) return;
            const fd = new FormData();
            fd.append("file", file);
            fd.append("csrf", galVars.CSRF || "");
            btn.disabled = true;
            fetch(galUploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
              .then((r) => r.json())
              .then((data) => {
                if (data && data.ok && data.url) {
                  srcInput.value = data.url;
                  srcInput.dispatchEvent(new Event("input", { bubbles: true }));
                  if (typeof saveState === "function") saveState();
                }
              })
              .finally(() => { btn.disabled = false; input.value = ""; });
          });
        }
      });
    } else if (key === "core_tabs") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung Reiter</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <p class="small text-muted mb-2">Bis zu 5 Reiter. Titel und Inhalt pro Tab eingeben.</p>
        ${[1, 2, 3, 4, 5].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <label class="form-label small mb-1">Tab ${i} – Titel</label>
          <input type="text" class="form-control form-control-sm mb-1" placeholder="Reiter-Bezeichnung" data-nx-field="tab${i}_title" />
          <label class="form-label small mb-1">Tab ${i} – Inhalt</label>
          <textarea class="form-control form-control-sm" rows="3" placeholder="Inhalt für diesen Tab" data-nx-field="tab${i}_content"></textarea>
        </div>
        `).join("")}
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_divider") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Stil</label>
            <select class="form-select form-select-sm" data-nx-field="style">
              <option value="">Standard</option>
              <option value="opacity-25">Dezent</option>
              <option value="opacity-50">Mittel</option>
              <option value="opacity-100">Kräftig</option>
              <option value="my-4">Mit Abstand (my-4)</option>
              <option value="my-5">Großer Abstand (my-5)</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_table") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Spalten</label>
            <select class="form-select form-select-sm" data-nx-field="columns">
              <option value="2">2 Spalten</option>
              <option value="3" selected>3 Spalten</option>
              <option value="4">4 Spalten</option>
              <option value="5">5 Spalten</option>
              <option value="6">6 Spalten</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Zeilen</label>
            <select class="form-select form-select-sm" data-nx-field="rows">
              <option value="2">2 Zeilen</option>
              <option value="3" selected>3 Zeilen</option>
              <option value="4">4 Zeilen</option>
              <option value="5">5 Zeilen</option>
              <option value="6">6 Zeilen</option>
              <option value="8">8 Zeilen</option>
              <option value="10">10 Zeilen</option>
            </select>
          </div>
        </div>
        <div class="mb-2 form-check form-switch">
          <input class="form-check-input" type="checkbox" id="nx-table-has-header" data-nx-field="hasHeader" checked>
          <label class="form-check-label small" for="nx-table-has-header">Kopfzeile anzeigen</label>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-table-striped" data-nx-field="striped" checked>
              <label class="form-check-label small" for="nx-table-striped">Gestreift</label>
            </div>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-table-hover" data-nx-field="hover" checked>
              <label class="form-check-label small" for="nx-table-hover">Hover-Effekt</label>
            </div>
          </div>
          <div class="col-6">
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-table-bordered" data-nx-field="bordered">
              <label class="form-check-label small" for="nx-table-bordered">Rahmen</label>
            </div>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" id="nx-table-small" data-nx-field="small">
              <label class="form-check-label small" for="nx-table-small">Kompakte Zeilen</label>
            </div>
          </div>
        </div>
        <div class="mb-2 form-check form-switch">
          <input class="form-check-input" type="checkbox" id="nx-table-responsive" data-nx-field="responsive" checked>
          <label class="form-check-label small" for="nx-table-responsive">Horizontal scrollbar (responsive)</label>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Tabellenbeschreibung (Caption)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="caption" placeholder="z. B. Übersicht Server-Auslastung" />
        </div>
        <p class="small text-muted mb-0">Zellen direkt in der Tabelle per Doppelklick bearbeiten.</p>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_list") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Typ</label>
          <select class="form-select form-select-sm" data-nx-field="type">
            <option value="ul">Aufzählung (ul)</option>
            <option value="ol">Nummeriert (ol)</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label small">Icon (optional, z.B. bi-check2)</label>
          <input type="text" class="form-control form-control-sm" placeholder="leer = keine Icons" data-nx-field="icon" />
        </div>
        <p class="small text-muted mb-1">Einträge (bis zu 10)</p>
        ${[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((i) => `
        <input type="text" class="form-control form-control-sm mb-1" placeholder="Eintrag ${i}" data-nx-field="item${i}" />
        `).join("")}
        <div class="row g-2 mt-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_alert") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Variante</label>
          <select class="form-select form-select-sm" data-nx-field="variant">
            <option value="info">Info</option>
            <option value="success">Erfolg</option>
            <option value="warning">Warnung</option>
            <option value="danger">Fehler</option>
            <option value="primary">Primary</option>
            <option value="secondary">Secondary</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label small">Titel (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" placeholder="Überschrift der Box" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Text</label>
          <textarea class="form-control form-control-sm" rows="3" data-nx-field="text" placeholder="Hinweistext …"></textarea>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" data-nx-field="dismissible">
          <label class="form-check-label small">Schließen-Button anzeigen</label>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_badge") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Text</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="text" placeholder="z.B. Neu, Sale" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Stil</label>
            <select class="form-select form-select-sm" data-nx-field="variant">
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
              <option value="success">Success</option>
              <option value="danger">Danger</option>
              <option value="warning">Warning</option>
              <option value="info">Info</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Link (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="url" placeholder="URL" />
          </div>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" data-nx-field="rounded">
          <label class="form-check-label small">Pill-Form (abgerundet)</label>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_accordion") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <p class="small text-muted mb-2">Bis zu 6 Einträge (Titel + Inhalt).</p>
        ${[1, 2, 3, 4, 5, 6].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <label class="form-label small mb-1">Eintrag ${i} – Titel</label>
          <input type="text" class="form-control form-control-sm mb-1" data-nx-field="item${i}_title" placeholder="Überschrift" />
          <label class="form-label small mb-1">Eintrag ${i} – Inhalt</label>
          <textarea class="form-control form-control-sm" rows="2" data-nx-field="item${i}_content" placeholder="Inhalt"></textarea>
        </div>
        `).join("")}
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_video") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Video-URL</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="url" placeholder="YouTube oder Vimeo URL" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_html") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">HTML / Code</label>
          <textarea class="form-control form-control-sm font-monospace" rows="6" data-nx-field="html" placeholder="<div>…</div> oder Embed-Code"></textarea>
          <p class="form-text small">Nur vertrauenswürdigen Inhalt einfügen (Admin-Bereich).</p>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_card") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Breite der Card</label>
            <select class="form-select form-select-sm" data-nx-field="width">
              <option value="small">Klein (ca. 18rem)</option>
              <option value="medium">Mittel (ca. 24rem)</option>
              <option value="large">Groß (ca. 32rem)</option>
              <option value="full">Vollbreite (100%)</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Bild-Seitenverhältnis</label>
            <select class="form-select form-select-sm" data-nx-field="imageRatio">
              <option value="">Automatisch</option>
              <option value="16:9">16:9</option>
              <option value="4:3">4:3</option>
              <option value="1:1">1:1 (quadratisch)</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Bild (optional)</label>
          <div class="input-group input-group-sm nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control" data-nx-field="image" placeholder="/images/content/…" />
            <button type="button" class="btn btn-outline-secondary nx-card-upload-btn">Hochladen</button>
          </div>
          <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none" data-nx-card-upload-input />
        </div>
        <div class="mb-2">
          <label class="form-label small">Titel</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="title" />
        </div>
        <div class="mb-2">
          <label class="form-label small">Text</label>
          <textarea class="form-control form-control-sm" rows="3" data-nx-field="text"></textarea>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Button-Label</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="buttonLabel" />
          </div>
          <div class="col-6">
            <label class="form-label small">Button-URL</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="buttonUrl" placeholder="#" />
          </div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
      const cardVars = window.NXB_BUILDER_VARS || {};
      const cardUploadUrl = (cardVars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      const cardBtn = wrapper.querySelector(".nx-card-upload-btn");
      const cardInput = wrapper.querySelector("[data-nx-card-upload-input]");
      const cardImgInput = wrapper.querySelector("[data-nx-field=\"image\"]");
      if (cardBtn && cardInput && cardImgInput && cardUploadUrl) {
        cardBtn.addEventListener("click", () => cardInput.click());
        cardInput.addEventListener("change", () => {
          const file = cardInput.files && cardInput.files[0];
          if (!file) return;
          const fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", cardVars.CSRF || "");
          cardBtn.disabled = true;
          fetch(cardUploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then((r) => r.json())
            .then((data) => {
              if (data && data.ok && data.url) {
                cardImgInput.value = data.url;
                cardImgInput.dispatchEvent(new Event("input", { bubbles: true }));
                if (typeof saveState === "function") saveState();
              }
            })
            .finally(() => { cardBtn.disabled = false; cardInput.value = ""; });
        });
      }
    } else if (key === "core_button_group") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <p class="small text-muted mb-2">Bis zu 4 Buttons.</p>
        ${[1, 2, 3, 4].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="row g-1">
            <div class="col-5"><input type="text" class="form-control form-control-sm" placeholder="Label ${i}" data-nx-field="btn${i}_label" /></div>
            <div class="col-5"><input type="text" class="form-control form-control-sm" placeholder="URL" data-nx-field="btn${i}_url" /></div>
            <div class="col-2"><select class="form-select form-select-sm" data-nx-field="btn${i}_style">
              <option value="primary">Primary</option>
              <option value="outline-secondary">Outline</option>
              <option value="success">Success</option>
              <option value="danger">Danger</option>
            </select></div>
          </div>
        </div>
        `).join("")}
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_breadcrumb") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <p class="small text-muted mb-2">Bis zu 5 Einträge (letzter = aktuell).</p>
        ${[1, 2, 3, 4, 5].map((i) => `
        <div class="row g-1 mb-1">
          <div class="col-5"><input type="text" class="form-control form-control-sm" placeholder="Label ${i}" data-nx-field="item${i}_label" /></div>
          <div class="col-7"><input type="text" class="form-control form-control-sm" placeholder="URL (letzter kann leer sein)" data-nx-field="item${i}_url" /></div>
        </div>
        `).join("")}
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_columns") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="mb-2">
          <label class="form-label small">Anzahl Spalten</label>
          <select class="form-select form-select-sm" data-nx-field="columns">
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
          </select>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Padding</label>
            <select class="form-select form-select-sm" data-nx-field="padding">
              <option value="py-4">Standard</option>
              <option value="py-3">Klein</option>
              <option value="py-5">Groß</option>
              <option value="py-0">Ohne</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Hintergrund (CSS)</label>
            <input type="text" class="form-control form-control-sm" placeholder="z.B. bg-light" data-nx-field="bg" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
        <p class="small text-muted mb-0">Widgets in die Spalten ziehen.</p>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_counter") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Wert / Zahl</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="value" placeholder="z.B. 500" />
          </div>
          <div class="col-6">
            <label class="form-label small">Suffix (optional)</label>
            <input type="text" class="form-control form-control-sm" data-nx-field="suffix" placeholder="z.B. +, %" />
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Beschriftung (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="label" placeholder="z.B. Zufriedene Kunden" />
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_progress") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Wert (0–100)</label>
            <input type="number" class="form-control form-control-sm" min="0" max="100" data-nx-field="value" placeholder="75" />
          </div>
          <div class="col-6">
            <label class="form-label small">Stil</label>
            <select class="form-select form-select-sm" data-nx-field="variant">
              <option value="primary">Primary</option>
              <option value="success">Success</option>
              <option value="warning">Warning</option>
              <option value="danger">Danger</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Label im Balken (optional)</label>
          <input type="text" class="form-control form-control-sm" data-nx-field="label" />
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" data-nx-field="striped">
          <label class="form-check-label small">Gestreift</label>
        </div>
        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" data-nx-field="animated">
          <label class="form-check-label small">Animiert</label>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else if (key === "core_logo_row") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <p class="small text-muted mb-2">Bis zu 6 Logos/Bilder. Optional Link pro Bild.</p>
        ${[1, 2, 3, 4, 5, 6].map((i) => `
        <div class="border rounded-3 p-2 mb-2 bg-light-subtle">
          <div class="input-group input-group-sm mb-1 nx-img-drop-zone" title="Bild hier ablegen">
            <input type="text" class="form-control" placeholder="/images/content/…" data-nx-field="item${i}_src" />
            <button type="button" class="btn btn-outline-secondary nx-logo-upload-btn" data-logo-idx="${i}">Hochladen</button>
          </div>
          <input type="file" accept="image/*" class="d-none" data-nx-logo-upload-input data-logo-idx="${i}" />
          <input type="text" class="form-control form-control-sm mb-1" placeholder="Alt-Text" data-nx-field="item${i}_alt" />
          <input type="text" class="form-control form-control-sm" placeholder="Link-URL (optional)" data-nx-field="item${i}_url" />
        </div>
        `).join("")}
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Sichtbarkeit</label>
            <select class="form-select form-select-sm" data-nx-field="visibility">
              <option value="">Immer</option>
              <option value="d-none d-md-block">Ab Tablet</option>
              <option value="d-block d-md-none">Nur Mobil</option>
              <option value="d-none d-lg-block">Nur Desktop</option>
              <option value="d-block d-lg-none">Versteckt Desktop</option>
            </select>
          </div>
        </div>
      `;
      settingsFields.appendChild(wrapper);
      const logoVars = window.NXB_BUILDER_VARS || {};
      const logoUploadUrl = (logoVars.BASE_URL || "").replace(/\/$/, "") + "/admin/media_upload.php";
      wrapper.querySelectorAll(".nx-logo-upload-btn").forEach((btn) => {
        const idx = btn.getAttribute("data-logo-idx");
        const input = wrapper.querySelector(`[data-nx-logo-upload-input][data-logo-idx="${idx}"]`);
        const srcInput = wrapper.querySelector(`[data-nx-field="item${idx}_src"]`);
        if (!btn || !input || !srcInput || !logoUploadUrl) return;
        btn.addEventListener("click", () => input.click());
        input.addEventListener("change", () => {
          const file = input.files && input.files[0];
          if (!file) return;
          const fd = new FormData();
          fd.append("file", file);
          fd.append("csrf", logoVars.CSRF || "");
          btn.disabled = true;
          fetch(logoUploadUrl, { method: "POST", body: fd, credentials: "same-origin" })
            .then((r) => r.json())
            .then((data) => {
              if (data && data.ok && data.url) {
                srcInput.value = data.url;
                srcInput.dispatchEvent(new Event("input", { bubbles: true }));
                if (typeof saveState === "function") saveState();
              }
            })
            .finally(() => { btn.disabled = false; input.value = ""; });
        });
      });
    } else if (key === "core_social_links") {
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <p class="small text-muted mb-2">URLs eintragen – nur ausgefüllte werden angezeigt.</p>
        <div class="row g-2 mb-2">
          <div class="col-6"><label class="form-label small">Facebook</label><input type="url" class="form-control form-control-sm" data-nx-field="facebook" placeholder="https://…" /></div>
          <div class="col-6"><label class="form-label small">Twitter/X</label><input type="url" class="form-control form-control-sm" data-nx-field="twitter" placeholder="https://…" /></div>
          <div class="col-6"><label class="form-label small">Instagram</label><input type="url" class="form-control form-control-sm" data-nx-field="instagram" placeholder="https://…" /></div>
          <div class="col-6"><label class="form-label small">YouTube</label><input type="url" class="form-control form-control-sm" data-nx-field="youtube" placeholder="https://…" /></div>
          <div class="col-6"><label class="form-label small">LinkedIn</label><input type="url" class="form-control form-control-sm" data-nx-field="linkedin" placeholder="https://…" /></div>
          <div class="col-6"><label class="form-label small">GitHub</label><input type="url" class="form-control form-control-sm" data-nx-field="github" placeholder="https://…" /></div>
          <div class="col-6"><label class="form-label small">Xing</label><input type="url" class="form-control form-control-sm" data-nx-field="xing" placeholder="https://…" /></div>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label small">Größe</label>
            <select class="form-select form-select-sm" data-nx-field="size">
              <option value="fs-5">Klein</option>
              <option value="fs-4">Standard</option>
              <option value="fs-3">Groß</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small">Ausrichtung</label>
            <select class="form-select form-select-sm" data-nx-field="align">
              <option value="start">Links</option>
              <option value="center">Mittig</option>
              <option value="end">Rechts</option>
            </select>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label small">Sichtbarkeit</label>
          <select class="form-select form-select-sm" data-nx-field="visibility">
            <option value="">Immer</option>
            <option value="d-none d-md-block">Ab Tablet</option>
            <option value="d-block d-md-none">Nur Mobil</option>
            <option value="d-none d-lg-block">Nur Desktop</option>
            <option value="d-block d-lg-none">Versteckt Desktop</option>
          </select>
        </div>
      `;
      settingsFields.appendChild(wrapper);
    } else {
      // Für Nicht-Core-Widgets nur einen Hinweis anzeigen
      const info = document.createElement("p");
      info.className = "small text-muted mb-2";
      info.textContent =
        "Für dieses Widget stehen derzeit nur die JSON-Einstellungen zur Verfügung.";
      settingsFields.appendChild(info);
    }

    // Felder in logische Accordion-Sektionen gruppieren
    applySettingsAccordion();

    // vorhandene Werte in die Felder schreiben
    const fieldEls = settingsFields.querySelectorAll("[data-nx-field]");
    fieldEls.forEach((el) => {
      const fieldKey = el.getAttribute("data-nx-field");
      if (!fieldKey) return;
      let val =
        settings && Object.prototype.hasOwnProperty.call(settings, fieldKey)
          ? settings[fieldKey]
          : undefined;

      // Sonderfall: targetBlank wird aus settings.target abgeleitet
      if (fieldKey === "targetBlank") {
        val = settings && settings.target === "_blank";
      }
      if (el.type === "checkbox") {
        el.checked = !!val;
      } else if (el.tagName === "SELECT") {
        if (val !== undefined) el.value = String(val);
      } else if (el.tagName === "TEXTAREA" || el.tagName === "INPUT") {
        if (val !== undefined) el.value = String(val);
      }
    });
    var sizeRange = settingsFields.querySelector("[data-nx-field=\"vignetteSize\"]");
    var opacityRange = settingsFields.querySelector("[data-nx-field=\"vignetteOpacity\"]");
    if (sizeRange) {
      var sv = settingsFields.querySelector(".nx-vignette-size-val");
      if (sv) sv.textContent = sizeRange.value;
    }
    if (opacityRange) {
      var ov = settingsFields.querySelector(".nx-vignette-opacity-val");
      if (ov) ov.textContent = opacityRange.value;
    }
  }

  function applySettingsAccordion() {
    if (!settingsFields) return;
    const originalChildren = Array.from(settingsFields.children);
    if (!originalChildren.length) return;

    const buckets = {
      content: [],
      layout: [],
      responsive: [],
      advanced: [],
    };

    function classifyElement(el) {
      const fieldEls = el.querySelectorAll("[data-nx-field]");
      if (!fieldEls.length) return "content";
      let bucket = "advanced";
      fieldEls.forEach((f) => {
        const k = (f.getAttribute("data-nx-field") || "").toLowerCase();
        if (!k) return;
        if (k === "visibility") {
          bucket = "responsive";
        } else if (/^(align|padding|bg|background|container|columns?|rows?|span|ratio|effect|captionstyle)$/.test(k)) {
          if (bucket === "advanced") bucket = "layout";
        } else if (/(title|subtitle|text|label|html|quote|content|name|caption|item\d+_|plan\d+_)/.test(k)) {
          bucket = "content";
        }
      });
      return bucket;
    }

    originalChildren.forEach((el) => {
      const bucket = classifyElement(el);
      (buckets[bucket] || buckets.advanced).push(el);
    });

    const nonEmptyBuckets = Object.values(buckets).filter((arr) => arr.length > 0).length;
    if (nonEmptyBuckets <= 1) return;

    settingsFields.innerHTML = "";
    const acc = document.createElement("div");
    acc.className = "accordion accordion-flush";
    acc.id = "nx-settings-accordion";

    function addSection(id, label, items, open) {
      if (!items.length) return;
      const item = document.createElement("div");
      item.className = "accordion-item";
      const collapseId = id + "-body";
      item.innerHTML = `
        <h2 class="accordion-header" id="${id}-header">
          <button class="accordion-button ${open ? "" : "collapsed"} small" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${open ? "true" : "false"}" aria-controls="${collapseId}">
            ${label}
          </button>
        </h2>
        <div id="${collapseId}" class="accordion-collapse collapse ${open ? "show" : ""}" aria-labelledby="${id}-header" data-bs-parent="#nx-settings-accordion">
          <div class="accordion-body p-2"></div>
        </div>
      `;
      const body = item.querySelector(".accordion-body");
      items.forEach((el) => body.appendChild(el));
      acc.appendChild(item);
    }

    addSection("nx-settings-sec-content", "Inhalt", buckets.content, true);
    addSection("nx-settings-sec-layout", "Layout", buckets.layout, false);
    addSection("nx-settings-sec-responsive", "Responsiv", buckets.responsive, false);
    addSection("nx-settings-sec-advanced", "Erweitert", buckets.advanced, false);

    settingsFields.appendChild(acc);
  }

  var initAllDone = false;

  function initAll() {
    nxDebug("initAll START readyState=" + document.readyState);
    if (initAllDone) {
      nxDebug("initAll SKIP (already done)");
      return;
    }
    initAllDone = true;
    try {
      nxDebug("initAll bindPalette");
      bindPalette();
      loadUndoStack();
      updateSaveStatus();
      nxDebug("initAll getZones().forEach(bindZone)");
      getZones().forEach(bindZone);
      nxDebug("initAll ensureDropHints");
      ensureDropHints();
      ensureInlineEditing();

      // Undo/Redo Buttons
      const undoBtn = document.getElementById("nx-undo");
      const redoBtn = document.getElementById("nx-redo");
      async function applySnapshot(indexDelta) {
        if (!undoStack.length) return;
        let newIndex = undoIndex + indexDelta;
        if (newIndex < 0 || newIndex >= undoStack.length) return;
        const snap = undoStack[newIndex];
        try {
          const r = await fetch(SAVE_ENDPOINT, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": CSRF,
            },
            body: JSON.stringify(snap),
            credentials: "same-origin",
          });
          const j = await r.json().catch(() => null);
          if (!j || !j.ok) {
            console.warn("❌ Undo/Redo Save failed", j);
            return;
          }
          undoIndex = newIndex;
          persistUndoStack();
          window.location.reload();
        } catch (e) {
          console.error("Undo/Redo error", e);
        }
      }
      if (undoBtn) {
        undoBtn.addEventListener("click", () => applySnapshot(-1));
      }
      if (redoBtn) {
        redoBtn.addEventListener("click", () => applySnapshot(1));
      }
      document.addEventListener("keydown", (e) => {
        if (e.ctrlKey && !e.shiftKey && (e.key === "z" || e.key === "Z")) {
          e.preventDefault();
          applySnapshot(-1);
        } else if (e.ctrlKey && (e.key === "y" || (e.shiftKey && (e.key === "z" || e.key === "Z")))) {
          e.preventDefault();
          applySnapshot(1);
        }
      });

      nxDebug("initAll END OK");
    } catch (e) {
      nxDebug("initAll ERROR: " + (e && e.message));
      if (window.nxDebug) window.nxDebug("Stack: " + (e && e.stack));
    }
  }

  if (document.readyState === "loading") {
    nxDebug("waiting for DOMContentLoaded");
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }
})();