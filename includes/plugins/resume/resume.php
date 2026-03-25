<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

global $_database;

// LanguageService initialisieren (erst danach detectLanguage aufrufen)
$languageService = new LanguageService($_database);
$lang = $languageService->detectLanguage();
$currentLang = $languageService->currentLanguage;

// Admin-Modul-Sprache laden
$languageService->readPluginModule('resume');

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class' => $class,
    'title' => $languageService->get('resume_title'),
    'subtitle' => 'Changelog'
];

echo $tpl->loadTemplate("resume", "head", $data_array, 'plugin');

function getMultiLangText(string $text, string $lang): string
{
    // Alle Sprachbloecke extrahieren
    preg_match_all('/\[\[lang:([a-z]{2})\]\](.*?)(?=\[\[lang:|$)/is', $text, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        if ($match[1] === $lang) {
            return trim($match[2]);
        }
    }

    return isset($matches[0][2]) ? trim($matches[0][2]) : $text;
}
?>

<div class="card">
  <div class="card-body">
    <section id="resume" class="resume py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">
            <?php echo getMultiLangText('[[lang:de]]Entwicklungsgeschichte von nexpell[[lang:en]]Development History of nexpell[[lang:it]]Storia dello sviluppo di nexpell', $currentLang); ?>
          </h2>
          <p class="text-muted">
            <?php echo getMultiLangText('[[lang:de]]Seit 2018 kontinuierlich weiterentwickelt[[lang:en]]Continuously developed since 2018[[lang:it]]Sviluppato continuamente dal 2018', $currentLang); ?>
          </p>
        </div>

        <div class="row">
          <div class="col-lg-12">

            <div class="resume-item mb-4 p-4 text-bg-secondary">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2018 - Gruendung von Webspell-RM[[lang:en]]2018 - Founding of Webspell-RM[[lang:it]]2018 - Fondazione di Webspell-RM', $currentLang); ?>
              </h4>
              <p>
                <?php echo getMultiLangText('[[lang:de]]Am 12. September 2018 wurde Webspell-RM ins Leben gerufen, nachdem das Team von <a href="https://www.designperformance.de" target="_blank">Design Performance</a> das Projekt uebernommen hatte. Zuvor wurde das Projekt <strong>webSPELL-NOR</strong> infolge der neuen Datenschutzgrundverordnung (DSGVO) eingestellt. Auch nach Anpassungen an die Datenschutzvorgaben wurde am 31.10.2018 intern das Ende von NOR beschlossen und die Webseite abgeschaltet.[[lang:en]]On September 12, 2018, Webspell-RM was launched after the team from <a href="https://www.designperformance.de" target="_blank">Design Performance</a> took over the project. Previously, the project <strong>webSPELL-NOR</strong> was discontinued due to the new General Data Protection Regulation (GDPR). Even after adapting to data protection requirements, NOR was internally ended on 31.10.2018 and the website was shut down.[[lang:it]]Il 12 settembre 2018 e stato lanciato Webspell-RM, dopo che il team di <a href="https://www.designperformance.de" target="_blank">Design Performance</a> aveva preso in gestione il progetto. In precedenza, il progetto <strong>webSPELL-NOR</strong> era stato interrotto a seguito del nuovo Regolamento Generale sulla Protezione dei Dati (GDPR). Anche dopo le modifiche per conformarsi alle normative sulla privacy, il 31.10.2018 e stata decisa internamente la fine di NOR e il sito e stato chiuso.', $currentLang); ?>
              </p>
              <p>
                <?php echo getMultiLangText('[[lang:de]]Viele Ideen, die urspruenglich fuer NOR vorgesehen waren, konnten nicht umgesetzt werden. Mit Zustimmung der damaligen NOR-Administratoren wurde daher <strong>webSPELL | RM</strong> gegruendet - mit dem Ziel, diese Ideen neu zu verwirklichen und das beliebte CMS technisch wie gestalterisch auf ein neues Level zu heben.[[lang:en]]Many ideas originally planned for NOR could not be implemented. With the consent of the then NOR administrators, <strong>webSPELL | RM</strong> was founded with the aim of realizing these ideas anew and taking the popular CMS to a new technical and design level.[[lang:it]]Molte idee originariamente previste per NOR non sono state realizzate. Con il consenso degli amministratori di NOR dell epoca, e stato quindi fondato <strong>webSPELL | RM</strong> con l obiettivo di realizzare queste idee in modo nuovo e portare il CMS popolare a un nuovo livello tecnico e di design.', $currentLang); ?>
              </p>
              <p>
                <?php echo getMultiLangText('[[lang:de]]Alle bestehenden Plugins wurden vollstaendig ueberarbeitet, um mit der neuen RM-Version kompatibel zu sein. Webspell-RM war damit das letzte aktive Webspell-Projekt vor dem spaeteren Umstieg auf nexpell.[[lang:en]]All existing plugins were completely revised to be compatible with the new RM version. Webspell-RM was thus the last active Webspell project before the later switch to nexpell.[[lang:it]]Tutti i plugin esistenti sono stati completamente rivisti per essere compatibili con la nuova versione RM. Webspell-RM e stato cosi l ultimo progetto Webspell attivo prima del successivo passaggio a nexpell.', $currentLang); ?>
              </p>
            </div>

            <div class="resume-item mb-4 p-4">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2019-2020 - Einfuehrung neuer Features[[lang:en]]2019-2020 - Introduction of New Features[[lang:it]]2019-2020 - Introduzione di nuove funzionalita', $currentLang); ?>
              </h4>
              <ul>
                <li><?php echo getMultiLangText('[[lang:de]]Integration des Bootstrap 5 Frameworks fuer ein modernes Design[[lang:en]]Integration of Bootstrap 5 framework for a modern design[[lang:it]]Integrazione del framework Bootstrap 5 per un design moderno', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Einfuehrung des CKEditor 4 fuer eine verbesserte Textbearbeitung[[lang:en]]Introduction of CKEditor 4 for improved text editing[[lang:it]]Introduzione di CKEditor 4 per un editing del testo migliorato', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Plugin- und Template-Installer zur einfachen Erweiterung[[lang:en]]Plugin and template installer for easy extension[[lang:it]]Installer di plugin e template per un estensione semplice', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Mehrsprachigkeit: Deutsch, Englisch, Italienisch[[lang:en]]Multilingualism: German, English, Italian[[lang:it]]Multilingua: tedesco, inglese, italiano', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Updater fuer einfache Systemaktualisierungen[[lang:en]]Updater for easy system updates[[lang:it]]Updater per aggiornamenti di sistema facili', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Bis zu 84 Plugins und 13 Templates verfuegbar[[lang:en]]Up to 84 plugins and 13 templates available[[lang:it]]Fino a 84 plugin e 13 template disponibili', $currentLang); ?></li>
              </ul>
            </div>

            <div class="resume-item mb-4 p-4 text-bg-secondary">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2021 - Sicherheits- und Performance-Updates[[lang:en]]2021 - Security and Performance Updates[[lang:it]]2021 - Aggiornamenti di sicurezza e prestazioni', $currentLang); ?>
              </h4>
              <ul>
                <li><?php echo getMultiLangText('[[lang:de]]CKEditor Update auf Version 4.16.0 (Sicherheitsluecken geschlossen)[[lang:en]]CKEditor update to version 4.16.0 (security vulnerabilities fixed)[[lang:it]]Aggiornamento di CKEditor alla versione 4.16.0 (vulnerabilita di sicurezza risolte)', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Personalisierte Avatar-Icons fuer Module[[lang:en]]Personalized avatar icons for modules[[lang:it]]Icone avatar personalizzate per i moduli', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Ueberarbeitetes Dashboard mit "Express Settings"[[lang:en]]Reworked dashboard with "Express Settings"[[lang:it]]Dashboard rivisto con "Express Settings"', $currentLang); ?></li>
              </ul>
            </div>

            <div class="resume-item mb-4 p-4">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2023 - Kompatibilitaet und Benutzerfreundlichkeit[[lang:en]]2023 - Compatibility and Usability[[lang:it]]2023 - Compatibilita e facilita d uso', $currentLang); ?>
              </h4>
              <ul>
                <li><?php echo getMultiLangText('[[lang:de]]Admincenter modernisiert mit Bootstrap 5[[lang:en]]Admin center modernized with Bootstrap 5[[lang:it]]Centro admin modernizzato con Bootstrap 5', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]PHP 8.2 kompatibel[[lang:en]]PHP 8.2 compatible[[lang:it]]Compatibile con PHP 8.2', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Widget-Positionierung & Plugin-Konfiguration vereinfacht[[lang:en]]Widget positioning & plugin configuration simplified[[lang:it]]Posizionamento widget e configurazione plugin semplificati', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Neue Begruessungsseite im Adminbereich[[lang:en]]New welcome page in the admin area[[lang:it]]Nuova pagina di benvenuto nell area admin', $currentLang); ?></li>
              </ul>
            </div>

            <div class="resume-item mb-4 p-4 text-bg-secondary">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2024 - Weitere Optimierungen[[lang:en]]2024 - Further Optimizations[[lang:it]]2024 - Ulteriori ottimizzazioni', $currentLang); ?>
              </h4>
              <ul>
                <li><?php echo getMultiLangText('[[lang:de]]Modul-Uebersicht zur besseren Plugin-Verwaltung[[lang:en]]Module overview for better plugin management[[lang:it]]Panoramica dei moduli per una migliore gestione dei plugin', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Language Editor zur einfachen Uebersetzung von Inhalten[[lang:en]]Language editor for easy content translation[[lang:it]]Editor linguistico per una facile traduzione dei contenuti', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Sticky Navigation Optionen fuer Admin-Navigation[[lang:en]]Sticky navigation options for admin navigation[[lang:it]]Opzioni di navigazione sticky per la navigazione admin', $currentLang); ?></li>
              </ul>
            </div>

            <div class="resume-item mb-4 p-4">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2025 - Neuentwicklung als nexpell[[lang:en]]2025 - Redevelopment as nexpell[[lang:it]]2025 - Riadattamento come nexpell', $currentLang); ?>
              </h4>
              <p>
                <?php echo getMultiLangText('[[lang:de]]Im Jahr 2025 wurde das System unter dem neuen Namen <strong>nexpell</strong> komplett neu entwickelt mit einem modernen, responsiven Adminbereich.[[lang:en]]In 2025, the system was completely redeveloped under the new name <strong>nexpell</strong> with a modern, responsive admin area.[[lang:it]]Nel 2025 il sistema e stato completamente riadattato con il nuovo nome <strong>nexpell</strong> con un area admin moderna e responsive.', $currentLang); ?>
              </p>
              <ul>
                <li><?php echo getMultiLangText('[[lang:de]]Integration und Umstieg auf Bootstrap 5.3 als Design- und Komponentenbasis[[lang:en]]Integration and switch to Bootstrap 5.3 as design and component basis[[lang:it]]Integrazione e passaggio a Bootstrap 5.3 come base per design e componenti', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Einfuehrung eines modularen Adminsystems mit separaten Bereichen fuer Pricing, About, Gallery, Resume u.v.m.[[lang:en]]Introduction of a modular admin system with separate areas for Pricing, About, Gallery, Resume, and more[[lang:it]]Introduzione di un sistema admin modulare con aree separate per Pricing, About, Gallery, Resume e altro', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Verbesserte Drag-&-Drop-Sortierung mit AJAX, inklusive Seitenvorschau fuer Galerien und Inhalte[[lang:en]]Improved drag & drop sorting with AJAX, including page preview for galleries and content[[lang:it]]Ordinamento drag & drop migliorato con AJAX, inclusa l anteprima della pagina per gallerie e contenuti', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Neuer Theme- und Template-Wechsler mit Live-Vorschau und komfortabler Speicherung[[lang:en]]New theme and template switcher with live preview and comfortable saving[[lang:it]]Nuovo switcher per temi e template con anteprima live e salvataggio comodo', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Vollstaendig neu strukturierte Plugin-Architektur mit eigenstaendigen Admin-Panels und Datenbanksteuerung[[lang:en]]Completely restructured plugin architecture with independent admin panels and database control[[lang:it]]Architettura plugin completamente ristrutturata con pannelli admin indipendenti e controllo del database', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Systematischer Ausbau von Hilfetexten, Tooltips und modaler Vorschau zur Verbesserung der Benutzerfreundlichkeit (UX)[[lang:en]]Systematic expansion of help texts, tooltips and modal preview to improve usability (UX)[[lang:it]]Espansione sistematica di testi di aiuto, tooltip e anteprime modali per migliorare l usabilita (UX)', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Zahlreiche Kernfunktionen aus Webspell-RM 2.1.6 wurden ueberarbeitet, optimiert oder komplett neu geschrieben[[lang:en]]Numerous core functions from Webspell-RM 2.1.6 were revised, optimized or completely rewritten[[lang:it]]Numerose funzioni core di Webspell-RM 2.1.6 sono state riviste, ottimizzate o riscritte completamente', $currentLang); ?></li>
                <li>
                  <strong><?php echo getMultiLangText('[[lang:de]]Deutliche Verbesserungen bei der Sicherheit:[[lang:en]]Significant improvements in security:[[lang:it]]Significativi miglioramenti nella sicurezza:', $currentLang); ?></strong>
                  <ul>
                    <li><?php echo getMultiLangText('[[lang:de]]Einfuehrung von CSRF-Schutz in Formularen[[lang:en]]Introduction of CSRF protection in forms[[lang:it]]Introduzione della protezione CSRF nei form', $currentLang); ?></li>
                    <li><?php echo getMultiLangText('[[lang:de]]Prepared Statements fuer alle Datenbankzugriffe zur Vermeidung von SQL-Injections[[lang:en]]Prepared statements for all database access to avoid SQL injections[[lang:it]]Prepared statements per tutti gli accessi al database per evitare SQL injection', $currentLang); ?></li>
                    <li><?php echo getMultiLangText('[[lang:de]]Verbesserte Benutzer- und Rechteverwaltung[[lang:en]]Improved user and rights management[[lang:it]]Gestione utenti e permessi migliorata', $currentLang); ?></li>
                    <li><?php echo getMultiLangText('[[lang:de]]Sicherere Passwort-Hashing-Methoden[[lang:en]]More secure password hashing methods[[lang:it]]Metodi di hashing delle password piu sicuri', $currentLang); ?></li>
                  </ul>
                </li>
                <li><?php echo getMultiLangText('[[lang:de]]Verbesserte Performance durch moderne PHP-Standards und optimierten Code[[lang:en]]Improved performance through modern PHP standards and optimized code[[lang:it]]Prestazioni migliorate grazie a standard PHP moderni e codice ottimizzato', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Integration neuer Features wie SEO-optimierte URL-Strukturen und bessere Mehrsprachigkeit[[lang:en]]Integration of new features such as SEO-optimized URL structures and improved multilingualism[[lang:it]]Integrazione di nuove funzionalita come URL ottimizzate per SEO e migliore multilingua', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Vereinfachte Erweiterbarkeit und Wartbarkeit durch konsequente Trennung von Logik und Darstellung[[lang:en]]Simplified extensibility and maintainability through consistent separation of logic and presentation[[lang:it]]Estensibilita e manutenibilita semplificate grazie a una chiara separazione tra logica e presentazione', $currentLang); ?></li>
              </ul>
            </div>

            <div class="resume-item mb-4 p-4 text-bg-secondary">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]2026 - Weiterentwicklung von nexpell[[lang:en]]2026 - Further Development of nexpell[[lang:it]]2026 - Ulteriore sviluppo di nexpell', $currentLang); ?>
              </h4>
              <p>
                <?php echo getMultiLangText('[[lang:de]]Im Jahr 2026 wurde <strong>nexpell</strong> konsequent weiterentwickelt und in vielen Kernbereichen technisch, strukturell und gestalterisch deutlich verbessert.[[lang:en]]In 2026, <strong>nexpell</strong> was consistently further developed and significantly improved in many core areas technically, structurally and visually.[[lang:it]]Nel 2026 <strong>nexpell</strong> e stato ulteriormente sviluppato in modo coerente e migliorato sensibilmente in molte aree chiave dal punto di vista tecnico, strutturale e visivo.', $currentLang); ?>
              </p>
              <ul>
                <li><?php echo getMultiLangText('[[lang:de]]Weiterer Ausbau der Core-Struktur mit Fokus auf Stabilität, Wartbarkeit und klare Systemarchitektur[[lang:en]]Further expansion of the core structure with a focus on stability, maintainability and clear system architecture[[lang:it]]Ulteriore ampliamento della struttura core con particolare attenzione a stabilita, manutenibilita e architettura di sistema chiara', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Neues Admincenter-Design mit modernerer Oberfläche, klarerer Struktur und verbesserter Bedienbarkeit[[lang:en]]New admin center design with a more modern interface, clearer structure and improved usability[[lang:it]]Nuovo design dell admincenter con interfaccia piu moderna, struttura piu chiara e migliore usabilita', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Überarbeitung des Backends mit besser abgestimmten Karten-, Tabellen- und Formularlayouts für eine konsistentere Benutzerführung[[lang:en]]Revision of the backend with better coordinated card, table and form layouts for more consistent user guidance[[lang:it]]Revisione del backend con layout di card, tabelle e moduli meglio coordinati per una guida utente piu coerente', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Entwicklung eines eigenen Editors und Entfernung von CKEditor aus Core und Plugins zugunsten einer schlankeren und wartbareren Systemstruktur[[lang:en]]Development of a custom editor and removal of CKEditor from core and plugins in favor of a leaner and more maintainable system structure[[lang:it]]Sviluppo di un editor personalizzato e rimozione di CKEditor dal core e dai plugin a favore di una struttura di sistema piu snella e manutenibile', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Vereinheitlichung, Optimierung und weitere Verfeinerung der SEO-Logik mit kanonischen URLs, Redirect-Systemen, Pagination-Regeln, konsistenteren Canonicals und verbesserter Sitemap-Ausgabe[[lang:en]]Standardization, optimization and further refinement of SEO logic with canonical URLs, redirect systems, pagination rules, more consistent canonicals and improved sitemap output[[lang:it]]Uniformazione, ottimizzazione e ulteriore affinamento della logica SEO con URL canonici, sistemi di redirect, regole di paginazione, canonical piu coerenti e output sitemap migliorato', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Überarbeitung zahlreicher Core- und Plugin-Dateien zur besseren Kompatibilität mit modernen PHP-Versionen[[lang:en]]Revision of numerous core and plugin files for better compatibility with modern PHP versions[[lang:it]]Revisione di numerosi file core e plugin per una migliore compatibilita con le moderne versioni di PHP', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Ausbau der Mehrsprachigkeit mit konsistenteren Sprachschlüsseln, sprachabhängigen Inhalten und verbesserter Verwaltung im Adminbereich[[lang:en]]Expansion of multilingual support with more consistent language keys, language-dependent content and improved management in the admin area[[lang:it]]Espansione del supporto multilingua con chiavi lingua piu coerenti, contenuti dipendenti dalla lingua e gestione migliorata nell area admin', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Technische Bereinigung und Modernisierung bestehender Module wie News, Articles, Forum, Wiki, Downloads, Rules, Static Pages und Navigation[[lang:en]]Technical cleanup and modernization of existing modules such as News, Articles, Forum, Wiki, Downloads, Rules, Static Pages and Navigation[[lang:it]]Pulizia tecnica e modernizzazione dei moduli esistenti come News, Articles, Forum, Wiki, Downloads, Rules, Static Pages e Navigation', $currentLang); ?></li>
                <li>
                  <strong><?php echo getMultiLangText('[[lang:de]]Weitere Verbesserung der Sicherheit:[[lang:en]]Further improvement of security:[[lang:it]]Ulteriore miglioramento della sicurezza:', $currentLang); ?></strong>
                  <ul>
                    <li><?php echo getMultiLangText('[[lang:de]]Absicherung kritischer Admin-Prozesse[[lang:en]]Securing critical admin processes[[lang:it]]Protezione dei processi amministrativi critici', $currentLang); ?></li>
                    <li><?php echo getMultiLangText('[[lang:de]]Zusätzliche Validierung und Härtung von Eingaben[[lang:en]]Additional validation and hardening of inputs[[lang:it]]Validazione aggiuntiva e rafforzamento degli input', $currentLang); ?></li>
                    <li><?php echo getMultiLangText('[[lang:de]]Verbesserte Behandlung verdächtiger Zugriffe und Protokollierung sicherheitsrelevanter Ereignisse[[lang:en]]Improved handling of suspicious requests and logging of security-relevant events[[lang:it]]Migliore gestione degli accessi sospetti e registrazione degli eventi rilevanti per la sicurezza', $currentLang); ?></li>
                    <li><?php echo getMultiLangText('[[lang:de]]Reduzierung veralteter, unsicherer oder fehleranfälliger Altlogik[[lang:en]]Reduction of outdated, insecure or error-prone legacy logic[[lang:it]]Riduzione della logica legacy obsoleta, insicura o soggetta a errori', $currentLang); ?></li>
                  </ul>
                </li>
                <li><?php echo getMultiLangText('[[lang:de]]Optimierung der Datenbankzugriffe und schrittweise Umstellung problematischer Altbereiche auf robustere, modernere Abfragen[[lang:en]]Optimization of database access and gradual migration of problematic legacy areas to more robust, modern queries[[lang:it]]Ottimizzazione degli accessi al database e migrazione graduale delle aree legacy problematiche verso query piu robuste e moderne', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Ausbau des Adminbereichs mit besserer Pflege von Navigation, Inhalten, Modulen und Systemeinstellungen[[lang:en]]Expansion of the admin area with improved management of navigation, content, modules and system settings[[lang:it]]Espansione dell area admin con una migliore gestione di navigazione, contenuti, moduli e impostazioni di sistema', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Verbesserung der Benutzerfreundlichkeit im Backend durch konsistentere Formulare, klarere Abläufe und weniger technische Fehlerquellen[[lang:en]]Improved backend usability through more consistent forms, clearer workflows and fewer technical sources of error[[lang:it]]Miglioramento dell usabilita del backend grazie a moduli piu coerenti, flussi piu chiari e meno fonti di errore tecnico', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Weiterentwicklung der Plugin-Integration, damit Erweiterungen sauberer an den Core angebunden werden können[[lang:en]]Further development of plugin integration so that extensions can be connected more cleanly to the core[[lang:it]]Ulteriore sviluppo dell integrazione plugin, cosi che le estensioni possano essere collegate al core in modo piu pulito', $currentLang); ?></li>
                <li><?php echo getMultiLangText('[[lang:de]]Vorbereitung von nexpell auf einen stabilen, eigenständigen Produktstand mit klarer Abgrenzung zur ursprünglichen Webspell-RM-Basis[[lang:en]]Preparation of nexpell for a stable, independent product state with clear differentiation from the original Webspell-RM base[[lang:it]]Preparazione di nexpell per uno stato di prodotto stabile e indipendente con una chiara distinzione dalla base originaria di Webspell-RM', $currentLang); ?></li>
              </ul>
            </div>

            <div class="resume-item mb-4 p-4 text-bg-secondary">
              <h4>
                <?php echo getMultiLangText('[[lang:de]]Weitere Informationen[[lang:en]]Further Information[[lang:it]]Ulteriori informazioni', $currentLang); ?>
              </h4>
              <ul>
                <li><a href="https://www.nexpell.de" target="_blank"><?php echo getMultiLangText('[[lang:de]]Offizielle Website[[lang:en]]Official Website[[lang:it]]Sito ufficiale', $currentLang); ?></a></li>
                <li><a href="https://github.com/nexpell" target="_blank"><?php echo getMultiLangText('[[lang:de]]GitHub-Repository (Basis)[[lang:en]]GitHub Repository (Base)[[lang:it]]Repository GitHub (Base)', $currentLang); ?></a></li>
                <li><a href="https://www.nexpell.de/de/forum" target="_blank"><?php echo getMultiLangText('[[lang:de]]Forum[[lang:en]]Forum[[lang:it]]Forum', $currentLang); ?></a></li>
                <li><a href="https://www.nexpell.de/de/wiki" target="_blank"><?php echo getMultiLangText('[[lang:de]]Wiki[[lang:en]]Wiki[[lang:it]]Wiki', $currentLang); ?></a></li>
              </ul>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>
</div>
