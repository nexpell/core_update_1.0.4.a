<?php
// Gemeinsame Definition und Rendering von "Core"-Widgets (nicht pluginbasiert)
declare(strict_types=1);

if (!function_exists('nxb_core_widgets_list')) {
    /**
     * Liefert die im System verfügbaren Core-Widgets für die Palette.
     */
    function nxb_core_widgets_list(): array
    {
        return [
            [
                'widget_key' => 'core_heading',
                'title'      => 'Überschrift',
                'category'   => 'Typografie',
            ],
            [
                'widget_key' => 'core_text',
                'title'      => 'Textblock',
                'category'   => 'Typografie',
            ],
            [
                'widget_key' => 'core_button',
                'title'      => 'Button',
                'category'   => 'Buttons & Links',
            ],
            [
                'widget_key' => 'core_image',
                'title'      => 'Bild',
                'category'   => 'Medien',
            ],
            [
                'widget_key' => 'core_hero',
                'title'      => 'Hero Section',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_hero_split',
                'title'      => 'Hero Split',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_feature_grid',
                'title'      => 'Feature-Grid',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_section_full',
                'title'      => 'Full-Width Section',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_section_two_col',
                'title'      => '2-Column Section',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_section_three_col',
                'title'      => '3-Column Section',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_container',
                'title'      => 'Container',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_row',
                'title'      => 'Row',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_col',
                'title'      => 'Col',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_spacer',
                'title'      => 'Abstand',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_quote',
                'title'      => 'Zitat',
                'category'   => 'Typografie',
            ],
            [
                'widget_key' => 'core_gallery',
                'title'      => 'Galerie',
                'category'   => 'Medien',
            ],
            [
                'widget_key' => 'core_tabs',
                'title'      => 'Tabs',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_divider',
                'title'      => 'Trennlinie',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_list',
                'title'      => 'Liste',
                'category'   => 'Typografie',
            ],
            [
                'widget_key' => 'core_collapse',
                'title'      => 'Collapse',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_list_group',
                'title'      => 'List-Group',
                'category'   => 'Typografie',
            ],
            [
                'widget_key' => 'core_link',
                'title'      => 'Link',
                'category'   => 'Buttons & Links',
            ],
            [
                'widget_key' => 'core_faq',
                'title'      => 'FAQ',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_testimonials',
                'title'      => 'Testimonials',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_timeline',
                'title'      => 'Timeline',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_table',
                'title'      => 'Tabelle',
                'category'   => 'Daten & Status',
            ],
            [
                'widget_key' => 'core_alert',
                'title'      => 'Hinweisbox',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_badge',
                'title'      => 'Badge / Label',
                'category'   => 'Daten & Status',
            ],
            [
                'widget_key' => 'core_accordion',
                'title'      => 'Accordion',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_video',
                'title'      => 'Video einbetten',
                'category'   => 'Medien',
            ],
            [
                'widget_key' => 'core_html',
                'title'      => 'HTML / Code',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_card',
                'title'      => 'Card',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_button_group',
                'title'      => 'Button-Gruppe',
                'category'   => 'Buttons & Links',
            ],
            [
                'widget_key' => 'core_breadcrumb',
                'title'      => 'Breadcrumb',
                'category'   => 'Buttons & Links',
            ],
            [
                'widget_key' => 'core_columns',
                'title'      => 'Spalten (2–6)',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_counter',
                'title'      => 'Zähler / Stat',
                'category'   => 'Daten & Status',
            ],
            [
                'widget_key' => 'core_progress',
                'title'      => 'Fortschrittsbalken',
                'category'   => 'Daten & Status',
            ],
            [
                'widget_key' => 'core_logo_row',
                'title'      => 'Logo-Leiste',
                'category'   => 'Medien',
            ],
            [
                'widget_key' => 'core_social_links',
                'title'      => 'Social-Links',
                'category'   => 'Buttons & Links',
            ],
            [
                'widget_key' => 'core_slider',
                'title'      => 'Carousel',
                'category'   => 'Medien',
            ],
            [
                'widget_key' => 'core_pricing',
                'title'      => 'Pricing',
                'category'   => 'Content',
            ],
            [
                'widget_key' => 'core_nav_demo',
                'title'      => 'Navigation (Demo)',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_footer_simple',
                'title'      => 'Footer (Links, kompakt)',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_footer_3col',
                'title'      => 'Footer (About & Hilfe)',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_footer_2col',
                'title'      => 'Footer (4 Spalten)',
                'category'   => 'Layout',
            ],
            [
                'widget_key' => 'core_footer_centered',
                'title'      => 'Footer (zentriert)',
                'category'   => 'Layout',
            ],
        ];
    }

    /**
     * Hilfsfunktion zum sicheren Escapen.
     */
    function nxb_core_h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Erlaubte responsive Sichtbarkeits-Klassen (Bootstrap) */
    function nxb_visibility_class(array $settings): string
    {
        $whitelist = [
            '' => true,
            'd-none d-md-block' => true,   // Versteckt auf Mobil
            'd-block d-md-none' => true,  // Nur auf Mobil
            'd-none d-lg-block' => true,  // Nur auf Desktop
            'd-block d-lg-none' => true,  // Versteckt auf Desktop
        ];
        $v = trim((string)($settings['visibility'] ?? ''));
        return isset($whitelist[$v]) ? $v : '';
    }

    /**
     * Optionales max-width-Style fuer innere Content-Wrapper.
     */
    function nxb_core_inline_max_width_style(array $settings): string
    {
        $value = trim((string)($settings['content_width'] ?? ''));
        if ($value === '') {
            return '';
        }
        if (!preg_match('/^(none|\\d+(?:\\.\\d+)?(?:px|rem|em|%|vw|vh))$/i', $value)) {
            return '';
        }
        return ' style="max-width:' . nxb_core_h($value) . ';margin-left:auto;margin-right:auto;"';
    }

    function nxb_core_widget_item_style(array $settings): string
    {
        $width = trim((string)($settings['item_width'] ?? ''));
        if ($width === '') {
            return '';
        }
        if (!preg_match('/^(none|\\d+(?:\\.\\d+)?(?:px|rem|em|%|vw|vh))$/i', $width)) {
            return '';
        }
        $align = trim((string)($settings['item_align'] ?? 'start'));
        $marginLeft = '0';
        $marginRight = 'auto';
        if ($align === 'center') {
            $marginLeft = 'auto';
            $marginRight = 'auto';
        } elseif ($align === 'end' || $align === 'right') {
            $marginLeft = 'auto';
            $marginRight = '0';
        }
        return 'max-width:' . nxb_core_h($width) . ';width:100%;margin-left:' . $marginLeft . ';margin-right:' . $marginRight . ';';
    }

    function nxb_wrap_widget_output(string $html, array $settings): string
    {
        $style = nxb_core_widget_item_style($settings);
        if ($style === '') {
            return $html;
        }
        return '<div class="nx-widget-width-wrap" style="' . $style . '">' . $html . '</div>';
    }

    function nxb_footer_link_href(array $settings, string $field): string
    {
        $value = trim((string)($settings[$field . '_url'] ?? ''));
        return $value !== '' ? $value : '#';
    }

    function nxb_render_frontend_widget_html(string $widgetKey, string $instanceId, array $settings, string $title): string
    {
        if (strpos($widgetKey, 'core_') === 0) {
            $html = nxb_render_core_widget_html($widgetKey, $settings, $title);
        } else {
            global $pluginManager;
            $html = '';
            if ($pluginManager instanceof \nexpell\PluginManager) {
                $html = $pluginManager->renderWidget($widgetKey, [
                    'instanceId' => $instanceId,
                    'settings' => $settings,
                    'title' => $title,
                    'ctx' => ['builder' => false, 'widget_key' => $widgetKey, 'instance_id' => $instanceId, 'title' => $title],
                ]);
            }
        }
        return nxb_wrap_widget_output($html, $settings);
    }

    /**
     * Liest Anzeigenamen aus navigation_website_lang für die aktuelle Sprache.
     * Schema: content_key, language, content, modulname.
     * Lookup: content_key (z. B. "main_1", "sub_5" oder modulname) → content.
     */
    function nxb_get_nav_lang_labels(\mysqli $_database, string $currentLang): array
    {
        $byKey = [];
        $byModulname = [];
        $table = 'navigation_website_lang';
        $res = $_database->query("SHOW TABLES LIKE '" . $_database->real_escape_string($table) . "'");
        if (!$res || $res->num_rows === 0) {
            return ['by_key' => $byKey, 'by_modulname' => $byModulname];
        }
        $lang = $_database->real_escape_string($currentLang);
        $res = $_database->query("SELECT content_key, content, modulname FROM `" . $table . "` WHERE language = '" . $lang . "'");
        if (!$res) {
            return ['by_key' => $byKey, 'by_modulname' => $byModulname];
        }
        while ($row = $res->fetch_assoc()) {
            $content = isset($row['content']) ? trim((string)$row['content']) : '';
            if ($content === '') {
                continue;
            }
            $key = isset($row['content_key']) ? trim((string)$row['content_key']) : '';
            $mod = isset($row['modulname']) ? trim((string)$row['modulname']) : '';
            if ($key !== '') {
                $byKey[$key] = $content;
            }
            if ($mod !== '') {
                $byModulname[$mod] = $content;
            }
        }
        $res->free();
        return ['by_key' => $byKey, 'by_modulname' => $byModulname];
    }

    /**
     * Baut den Language-Selector für die Navbar (wie in widget_navigation.php).
     * Liefert HTML: <li class="nav-item dropdown"> mit Flag-Toggle und dropdown-menu (Sprachen mit Flag + name_native).
     * @param string $linkPaddingStyle Optional: gleiches Padding wie die anderen Nav-Links (z. B. style="padding-top:26px;...") für gleiche Höhe.
     */
    function nxb_nav_language_selector_html(string $linkPaddingStyle = ''): string
    {
        global $languageService;
        $languageService = $languageService ?? $GLOBALS['languageService'] ?? null;
        if (!$languageService || !method_exists($languageService, 'getActiveLanguages')) {
            return '';
        }
        $languages = $languageService->getActiveLanguages();
        if (empty($languages)) {
            return '';
        }
        $currentLang = $languageService->currentLanguage ?? 'de';
        $currentFlag = '';
        $itemsHtml = '';
        // Wie Plugin: lang in URL. Im Builder-Kontext Basis-Query aus $GLOBALS, sonst $_GET.
        $currentQuery = (isset($GLOBALS['nxb_nav_base_query']) && is_array($GLOBALS['nxb_nav_base_query']))
            ? $GLOBALS['nxb_nav_base_query']
            : $_GET;
        // Live-Builder: Seite mit ?builder=1 im Iframe ODER Render aus plugin_widgets_render → Sprach-Link mit target="_top", damit Session im Hauptfenster gesetzt wird
        $isBuilderContext = (isset($GLOBALS['nxb_nav_base_query']) && is_array($GLOBALS['nxb_nav_base_query']))
            || (!empty($_GET['builder']) && $_GET['builder'] === '1');

        foreach ($languages as $l) {
            $flag = isset($l['flag']) ? trim((string)$l['flag']) : '';
            $iso  = isset($l['iso_639_1']) ? trim((string)$l['iso_639_1']) : '';
            $name = isset($l['name_native']) ? trim((string)$l['name_native']) : $iso;
            if ($iso === '') {
                continue;
            }
            if ($iso === $currentLang) {
                $currentFlag = $flag !== '' ? $flag : '';
            }
            $query = $currentQuery;
            $query['lang'] = $iso;
            $url = function_exists('\\nexpell\\SeoUrlHandler::convertToSeoUrl')
                ? \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?' . http_build_query($query))
                : 'index.php?' . http_build_query($query);
            $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $active = ($iso === $currentLang);
            $activeClass = $active ? ' active-language' : '';
            $checkIcon = $active ? ' <i class="bi bi-check2 ms-auto text-success"></i>' : '';
            $flagImg = $flag !== '' ? '<img src="' . htmlspecialchars($flag, ENT_QUOTES, 'UTF-8') . '" class="me-2" style="width:20px;height:20px;border-radius:4px;">' : '';
            // data-nx-lang-link: Builder-JS lässt den Klick durch (kein preventDefault), damit Sprachumschaltung funktioniert
            $targetAttr = $isBuilderContext ? ' target="_top"' : '';
            $itemsHtml .= '<li><a class="dropdown-item d-flex align-items-center' . $activeClass . '" href="' . $url . '"' . $targetAttr . ' data-nx-lang-link="1">' . $flagImg . '<span>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</span>' . $checkIcon . '</a></li>';
        }

        if ($itemsHtml === '') {
            return '';
        }
        $toggleImg = $currentFlag !== '' ? '<img src="' . htmlspecialchars($currentFlag, ENT_QUOTES, 'UTF-8') . '" style="width:22px;height:22px;border-radius:4px;">' : '<i class="bi bi-globe"></i>';
        $padAttr = $linkPaddingStyle !== '' ? ' ' . trim($linkPaddingStyle) : '';
        return '<li class="nav-item dropdown">'
            . '<a class="nav-link d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown" aria-expanded="false"' . $padAttr . '>' . $toggleImg . '</a>'
            . '<ul class="dropdown-menu dropdown-menu-end">' . $itemsHtml . '</ul></li>';
    }

    /**
     * Liefert die gespeicherte Menüstruktur aus navigation_website_main + _sub
     * im Format [{label, url, children: [{label, url}]}]. Nutzt navigation_website_lang
     * für übersetzte Anzeigenamen (aktuelle Sprache).
     */
    function nxb_get_plugin_navigation_menu(): array
    {
        global $_database, $languageService;
        if (!isset($_database)) {
            return [];
        }
        $languageService = $languageService ?? $GLOBALS['languageService'] ?? null;
        $currentLang = ($languageService && isset($languageService->currentLanguage)) ? $languageService->currentLanguage : 'de';
        $langLabels = nxb_get_nav_lang_labels($_database, $currentLang);

        $navLang = static function (string $txt) use ($languageService): string {
            if ($languageService && strpos($txt, '[[lang:') !== false) {
                return $languageService->parseMultilang($txt);
            }
            return $txt;
        };
        $getLabel = static function (array $row, string $type, int $id) use ($languageService, $navLang, $langLabels): string {
            $byKey = $langLabels['by_key'] ?? [];
            $byModulname = $langLabels['by_modulname'] ?? [];
            $mod = isset($row['modulname']) ? trim((string)$row['modulname']) : '';
            if ($type === 'main') {
                $t = $byKey['nav_main_' . $id] ?? $byKey['main_' . $id] ?? $byKey['mnav_' . $id] ?? $byKey[(string)$id] ?? null;
                if ($t !== null && $t !== '') {
                    return $t;
                }
                if ($mod !== '' && isset($byModulname[$mod]) && $byModulname[$mod] !== '') {
                    return $byModulname[$mod];
                }
            }
            if ($type === 'sub') {
                $t = $byKey['nav_sub_' . $id] ?? $byKey['sub_' . $id] ?? $byKey['snav_' . $id] ?? $byKey[(string)$id] ?? null;
                if ($t !== null && $t !== '') {
                    return $t;
                }
                if ($mod !== '' && isset($byModulname[$mod]) && $byModulname[$mod] !== '') {
                    return $byModulname[$mod];
                }
            }
            if (isset($row['name']) && trim((string)$row['name']) !== '') {
                return $navLang(trim((string)$row['name']));
            }
            if (isset($row['title']) && trim((string)$row['title']) !== '') {
                return $navLang(trim((string)$row['title']));
            }
            if (isset($row['label']) && trim((string)$row['label']) !== '') {
                return $navLang(trim((string)$row['label']));
            }
            if ($languageService && method_exists($languageService, 'readModule')) {
                $languageService->readModule('navigation', false);
                if ($mod !== '' && method_exists($languageService, 'get')) {
                    $t = $languageService->get($mod);
                    if ($t !== $mod && $t !== '') {
                        return $t;
                    }
                }
            }
            return $mod !== '' ? $mod : '';
        };
        $urlKey = static function (array $row): string {
            $u = $row['url'] ?? $row['link'] ?? '#';
            return trim((string)$u);
        };

        $out = [];
        $res = $_database->query("SELECT * FROM navigation_website_main ORDER BY sort ASC");
        if (!$res) {
            return [];
        }
        while ($m = $res->fetch_assoc()) {
            $mnavID = (int)($m['mnavID'] ?? 0);
            $label  = $getLabel($m, 'main', $mnavID);
            $url    = $urlKey($m);
            if (function_exists('\\nexpell\\SeoUrlHandler::convertToSeoUrl')) {
                $url = \nexpell\SeoUrlHandler::convertToSeoUrl($url);
            }
            $children = [];
            $isDropdown = (int)($m['isdropdown'] ?? $m['dropdown'] ?? 0) === 1;
            if ($isDropdown) {
                $sub = $_database->query("SELECT * FROM navigation_website_sub WHERE mnavID = " . $mnavID . " ORDER BY sort ASC");
                if ($sub) {
                    while ($s = $sub->fetch_assoc()) {
                        $snavID = (int)($s['snavID'] ?? 0);
                        $sLabel = $getLabel($s, 'sub', $snavID);
                        $sUrl   = $urlKey($s);
                        if (function_exists('\\nexpell\\SeoUrlHandler::convertToSeoUrl')) {
                            $sUrl = \nexpell\SeoUrlHandler::convertToSeoUrl($sUrl);
                        }
                        $children[] = ['label' => $sLabel, 'url' => $sUrl];
                    }
                    $sub->free();
                }
            }
            $out[] = ['label' => $label, 'url' => $url, 'children' => $children];
        }
        $res->free();
        return $out;
    }

    /**
     * Rendert ein Core-Widget als HTML (wird vom Builder initial und via AJAX verwendet).
     */
    function nxb_render_core_widget_html(string $widget_key, array $settings, string $title): string
    {
        // Zugriff auf alle Widgets je Zone, die der Builder vorab geladen hat
        $allRows = $GLOBALS['__NX_ALL_WIDGET_ROWS'] ?? [];

        switch ($widget_key) {
            case 'core_heading':
                $level = (int)($settings['level'] ?? 2);
                if ($level < 1 || $level > 6) {
                    $level = 2;
                }
                $text  = (string)($settings['text'] ?? $title ?: 'Überschrift');
                $align = (string)($settings['align'] ?? '');
                $class = trim((string)($settings['class'] ?? ''));

                $alignClass = '';
                if ($align === 'center') {
                    $alignClass = 'text-center';
                } elseif ($align === 'end' || $align === 'right') {
                    $alignClass = 'text-end';
                } elseif ($align === 'start' || $align === 'left') {
                    $alignClass = 'text-start';
                }

                $vis = nxb_visibility_class($settings);
                $classes = trim('mb-3 ' . $alignClass . ' ' . $class . ($vis !== '' ? ' ' . $vis : ''));

                return sprintf(
                    '<h%d class="%s" data-nx-inline="text" title="Doppelklick zum Bearbeiten">%s</h%d>',
                    $level,
                    nxb_core_h($classes),
                    nxb_core_h($text),
                    $level
                );

            case 'core_header':
                // Sektions-Header mit optionalem Untertitel und optionalem Bild (Texte als Overlay auf dem Bild)
                $headerTitle   = trim((string)($settings['title'] ?? $title ?: 'Header'));
                $headerSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $headerImage   = trim((string)($settings['image'] ?? ''));
                $level = (int)($settings['level'] ?? 2);
                if ($level < 1 || $level > 6) {
                    $level = 2;
                }
                $displaySize = (string)($settings['display'] ?? '');
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'nx-header ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $headingClass = trim($displaySize !== '' ? $displaySize : '');
                $imageHeight  = trim((string)($settings['imageHeight'] ?? ''));
                $imageHeightUnit = (string)($settings['imageHeightUnit'] ?? 'px');
                if (!in_array($imageHeightUnit, ['px', 'rem', 'vh'], true)) {
                    $imageHeightUnit = 'px';
                }
                $vignetteSize = isset($settings['vignetteSize']) ? (float)$settings['vignetteSize'] : 40;
                $vignetteSize = max(0, min(100, $vignetteSize));
                $vignetteOpacity = isset($settings['vignetteOpacity']) ? (float)$settings['vignetteOpacity'] : 50;
                $vignetteOpacity = max(0, min(100, $vignetteOpacity));
                $vignetteOpacityDec = $vignetteOpacity / 100;
                $html = '<header class="' . nxb_core_h($wrapClass) . '">';

                $isBuilder = function_exists('nxb_is_builder') && nxb_is_builder();
                // forceOverlay wird z. B. von Presets gesetzt und soll auch im Frontend gelten
                $forceOverlay = !empty($settings['forceOverlay']);
                $useOverlay = ($headerImage !== '') || $forceOverlay;

                if ($useOverlay) {
                    $heightStyle = '';
                    if ($imageHeight !== '' && is_numeric($imageHeight)) {
                        $heightStyle = ' height:' . (float)$imageHeight . $imageHeightUnit . ';';
                    } else {
                        $heightStyle = ' height:35vh;';
                    }
                    $wrapperStyle = trim($heightStyle);
                    // Overlay ohne echtes Bild: dezenter Verlauf als Hintergrund (sowohl im Builder als auch im Frontend)
                    if ($headerImage === '') {
                        $wrapperStyle .= ' background:radial-gradient(circle at top,#1e293b,#020617);';
                    }
                    $html .= '<div class="nx-header-image position-relative overflow-hidden" style="' . nxb_core_h($wrapperStyle) . '">';
                    if ($headerImage !== '') {
                        $html .= '<img src="' . nxb_core_h($headerImage) . '" alt="" class="w-100 h-100 nx-header-bg-img" style="position:absolute;inset:0;object-fit:cover;display:block;z-index:0;" data-nx-inline="image" title="Klick: Bild ändern">';
                    } elseif ($isBuilder) {
                        // Platzhalter, der per Doppelklick durch ein echtes Bild ersetzt werden kann
                        $html .= '<div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center text-white-50 small" data-nx-inline="image" title="Klick: Bild hinzufügen" style="z-index:0;cursor:pointer;">Bild – Klick zum Hinzufügen</div>';
                    }
                    if ($vignetteOpacity > 0) {
                        $vignetteGradient = 'radial-gradient(circle at center, transparent 0%, transparent ' . (int)$vignetteSize . '%, rgba(0,0,0,' . nxb_core_h((string)$vignetteOpacityDec) . ') 100%)';
                        $html .= '<div class="position-absolute top-0 start-0 end-0 bottom-0 nx-header-vignette" style="background:' . nxb_core_h($vignetteGradient) . ';z-index:1;pointer-events:none;" aria-hidden="true"></div>';
                    }
                    $html .= '<div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex flex-column ' . nxb_core_h($alignClass) . ' justify-content-center p-4" style="background:rgba(0,0,0,.4);z-index:2;">';
                    $html .= '<div class="text-white">';
                    $html .= sprintf('<h%d class="%s mb-2 text-white" data-nx-inline="title" title="Doppelklick zum Bearbeiten">%s</h%d>', $level, nxb_core_h($headingClass), nxb_core_h($headerTitle), $level);
                    if ($headerSubtitle !== '' || $isBuilder) {
                        $subtitleClass = $headerSubtitle !== '' ? 'text-uppercase fw-semibold mb-0 small opacity-90' : 'mb-0 small opacity-70 nx-inline-placeholder';
                        $html .= '<p class="' . nxb_core_h($subtitleClass) . '" data-nx-inline="subtitle" title="' . ($headerSubtitle !== '' ? 'Doppelklick zum Bearbeiten' : 'Doppelklick zum Hinzufügen') . '">' . ($headerSubtitle !== '' ? nxb_core_h($headerSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen') . '</p>';
                    }
                    $html .= '</div></div></div>';
                } else {
                    $html .= sprintf('<h%d class="%s mb-2" data-nx-inline="title" title="Doppelklick zum Bearbeiten">%s</h%d>', $level, nxb_core_h($headingClass), nxb_core_h($headerTitle), $level);
                    if ($headerSubtitle !== '' || $isBuilder) {
                        $subtitleClass = $headerSubtitle !== '' ? 'text-uppercase fw-semibold mb-0 small text-primary' : 'mb-0 small text-muted nx-inline-placeholder';
                        $html .= '<p class="' . nxb_core_h($subtitleClass) . '" data-nx-inline="subtitle" title="' . ($headerSubtitle !== '' ? 'Doppelklick zum Bearbeiten' : 'Doppelklick zum Hinzufügen') . '">' . ($headerSubtitle !== '' ? nxb_core_h($headerSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen') . '</p>';
                    }
                    if ($headerImage === '' && $isBuilder) {
                        $html .= '<div class="mt-2 py-2 px-3 small text-muted" data-nx-inline="image" title="Klick: Bild hinzufügen" style="cursor:pointer;">Bild – Klick zum Hinzufügen</div>';
                    }
                }
                $html .= '</header>';
                return $html;

            case 'core_text':
                // Erlaubt HTML-Inhalt im Admin (Builder). Wenn nur "text" gesetzt ist, wird dieser escaped ausgegeben.
                $rawHtml = $settings['html'] ?? null;
                if ($rawHtml === null && isset($settings['text'])) {
                    $rawHtml = '<p>' . nxb_core_h((string)$settings['text']) . '</p>';
                }
                if ($rawHtml === null || $rawHtml === '') {
                    $rawHtml = '<p class="text-muted">Beispieltext – bitte Inhalt im Builder anpassen.</p>';
                }
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                return '<div class="' . nxb_core_h($wrapClass) . '" data-nx-inline="html" title="Doppelklick zum Bearbeiten">' . (string)$rawHtml . '</div>';

            case 'core_image':
                $src      = trim((string)($settings['src'] ?? ''));
                $alt      = (string)($settings['alt'] ?? '');
                $caption  = (string)($settings['caption'] ?? '');
                $align    = (string)($settings['align'] ?? 'start'); // start|center|end
                $rounded  = !empty($settings['rounded']);
                $shadow   = !empty($settings['shadow']);
                $linkHref = trim((string)($settings['href'] ?? ''));
                $ratio    = (string)($settings['ratio'] ?? '');

                if ($src === '') {
                    if (function_exists('nxb_is_builder') && nxb_is_builder()) {
                        $vis = nxb_visibility_class($settings);
                        $wrapClass = 'mb-3 ' . ($align === 'center' ? 'text-center' : ($align === 'end' || $align === 'right' ? 'text-end' : 'text-start')) . ($vis !== '' ? ' ' . $vis : '');
                        $fig = '<figure class="' . nxb_core_h($wrapClass) . '">
  <div class="py-4 px-3 border border-dashed rounded small text-muted" data-nx-inline="src" title="Klick: Bild hinzufügen" style="cursor:pointer;">Bild – Klick zum Hinzufügen</div>
  <figcaption class="mt-2 small text-muted nx-inline-placeholder" data-nx-inline="caption" title="Doppelklick zum Hinzufügen">Bildunterschrift – Doppelklick zum Hinzufügen</figcaption>
</figure>';
                        return $fig;
                    }
                    return '<div class="mb-3 alert alert-warning small">Kein Bild gewählt – bitte URL im Builder hinterlegen.</div>';
                }

                $imgClasses = ['img-fluid'];
                if ($rounded) {
                    $imgClasses[] = 'rounded-3';
                }
                if ($shadow) {
                    $imgClasses[] = 'shadow-sm';
                }

                $wrapperClasses = ['mb-3'];
                if ($align === 'center') {
                    $wrapperClasses[] = 'text-center';
                } elseif ($align === 'end' || $align === 'right') {
                    $wrapperClasses[] = 'text-end';
                } else {
                    $wrapperClasses[] = 'text-start';
                }
                $vis = nxb_visibility_class($settings);
                if ($vis !== '') {
                    $wrapperClasses[] = $vis;
                }

                $imgTag = '<img src="' . nxb_core_h($src) . '" alt="' . nxb_core_h($alt) . '" class="' . nxb_core_h(implode(' ', $imgClasses)) . '" data-nx-inline="src" title="Klick: Bild ändern">';

                if ($linkHref !== '') {
                    $imgTag = '<a href="' . nxb_core_h($linkHref) . '">' . $imgTag . '</a>';
                }

                // Optional: Ratio-Wrapper für konsistente Höhen
                if ($ratio !== '' && preg_match('~^\d+:\d+$~', $ratio)) {
                    [$w, $h] = array_map('intval', explode(':', $ratio, 2));
                    if ($w > 0 && $h > 0) {
                        $padding = ($h / $w) * 100;
                        $imgTag = '<div class="position-relative w-100 overflow-hidden" style="padding-top:' . nxb_core_h((string)$padding) . '%">
  <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
    ' . $imgTag . '
  </div>
</div>';
                    }
                }

                $html = '<figure class="' . nxb_core_h(implode(' ', $wrapperClasses)) . '">
  ' . $imgTag;

                if ($caption !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $capClass = $caption !== '' ? 'mt-2 small text-muted' : 'mt-2 small text-muted nx-inline-placeholder';
                    $capTitle = $caption !== '' ? 'Doppelklick zum Bearbeiten' : 'Doppelklick zum Hinzufügen';
                    $capText  = $caption !== '' ? nxb_core_h($caption) : 'Bildunterschrift – Doppelklick zum Hinzufügen';
                    $html .= '
  <figcaption class="' . nxb_core_h($capClass) . '" data-nx-inline="caption" title="' . nxb_core_h($capTitle) . '">' . $capText . '</figcaption>';
                }

                $html .= '
</figure>';

                return $html;

            case 'core_button':
                $label = (string)($settings['label'] ?? $title ?: 'Button');
                // Aliase zulassen, damit JSON-Settings "intuitiv" bleiben
                $url   = (string)($settings['url'] ?? ($settings['href'] ?? ($settings['link'] ?? '#')));
                $style = (string)($settings['style'] ?? ($settings['variant'] ?? 'primary'));
                $size  = (string)($settings['size'] ?? 'md');
                $block = !empty($settings['block']);
                $extraClass = trim((string)($settings['class'] ?? ($settings['classes'] ?? '')));
                $target = trim((string)($settings['target'] ?? ''));
                $rel    = trim((string)($settings['rel'] ?? ''));
                $ariaLabel = trim((string)($settings['ariaLabel'] ?? ($settings['aria_label'] ?? '')));

                $allowedStyles = [
                    'primary','secondary','success','danger','warning','info','light','dark',
                    'outline-primary','outline-secondary','outline-success','outline-danger',
                    'outline-warning','outline-info','outline-light','outline-dark'
                ];
                // Normalisieren: "btn-primary" / "btn-outline-primary" erlauben
                if (strpos($style, 'btn-') === 0) {
                    $style = substr($style, 4);
                }
                if (!in_array($style, $allowedStyles, true)) {
                    $style = 'primary';
                }

                $sizeClass = '';
                if ($size === 'sm') {
                    $sizeClass = 'btn-sm';
                } elseif ($size === 'lg') {
                    $sizeClass = 'btn-lg';
                }

                $classes = ['btn', 'btn-'.$style];
                if ($sizeClass) {
                    $classes[] = $sizeClass;
                }
                if ($block) {
                    $classes[] = 'w-100';
                }
                if ($extraClass !== '') {
                    $classes[] = $extraClass;
                }

                $classAttr = implode(' ', $classes);

                $attrs = [
                    'href="' . nxb_core_h($url) . '"',
                    'class="' . nxb_core_h($classAttr) . '"',
                ];
                if ($target !== '') {
                    $attrs[] = 'target="' . nxb_core_h($target) . '"';
                }
                if ($rel !== '') {
                    $attrs[] = 'rel="' . nxb_core_h($rel) . '"';
                } elseif ($target === '_blank') {
                    // sinnvolles Default-Rel, falls _blank verwendet wird
                    $attrs[] = 'rel="noopener noreferrer"';
                }
                if ($ariaLabel !== '') {
                    $attrs[] = 'aria-label="' . nxb_core_h($ariaLabel) . '"';
                }

                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                return '<div class="' . nxb_core_h($wrapClass) . '"><a ' . implode(' ', $attrs) . ' data-nx-inline="label" title="Doppelklick zum Bearbeiten">' . nxb_core_h($label) . '</a></div>';

            case 'core_feature_grid':
                // Einfaches Feature-Grid (max. 4 Karten), konfiguriert über Settings
                $cols = (int)($settings['columns'] ?? 3);
                if ($cols < 2 || $cols > 4) {
                    $cols = 3;
                }

                $items = [];
                // Entweder strukturierte items verwenden ...
                if (!empty($settings['items']) && is_array($settings['items'])) {
                    $i = 0;
                    foreach ($settings['items'] as $it) {
                        $i++;
                        if (empty($it['title']) && empty($it['text'])) {
                            continue;
                        }
                        $items[] = [
                            'index' => $i,
                            'title' => (string)($it['title'] ?? ''),
                            'text'  => (string)($it['text'] ?? ''),
                            'icon'  => (string)($it['icon'] ?? ''),
                        ];
                    }
                } else {
                    // ... oder flache Felder item1_title, item1_text, item1_icon, ...
                    for ($i = 1; $i <= 4; $i++) {
                        $t = (string)($settings["item{$i}_title"] ?? '');
                        $tx = (string)($settings["item{$i}_text"] ?? '');
                        $ic = (string)($settings["item{$i}_icon"] ?? '');
                        if ($t === '' && $tx === '') {
                            continue;
                        }
                        $items[] = ['index' => $i, 'title' => $t, 'text' => $tx, 'icon' => $ic];
                    }
                }

                if (empty($items)) {
                    // Fallback-Dummy-Inhalte
                    $items = [
                        ['index' => 1, 'title' => 'Feature 1', 'text' => 'Kurze Beschreibung des Features.', 'icon' => 'bi-lightning-charge'],
                        ['index' => 2, 'title' => 'Feature 2', 'text' => 'Hervorhebung eines Vorteils.', 'icon' => 'bi-star'],
                        ['index' => 3, 'title' => 'Feature 3', 'text' => 'Ein weiterer Nutzenpunkt.', 'icon' => 'bi-shield-check'],
                    ];
                }

                $colClass = 'col-md-4';
                if ($cols === 2) {
                    $colClass = 'col-md-6';
                } elseif ($cols === 4) {
                    $colClass = 'col-md-3';
                }

                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $sectionClass = 'nx-feature-grid py-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $html = '<section class="' . nxb_core_h($sectionClass) . '">
  <div class="container">
    <div class="row g-4">';

                foreach ($items as $item) {
                    $iconHtml = '';
                    if (!empty($item['icon'])) {
                        $iconHtml = '<div class="mb-2 text-primary"><i class="bi ' . nxb_core_h($item['icon']) . ' fs-3"></i></div>';
                    }

                    $idx = (int)($item['index'] ?? 0);
                    $html .= '
      <div class="' . nxb_core_h('col-12 ' . $colClass) . '">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body">
            ' . $iconHtml . '
            <h3 class="h5 mb-2" data-nx-inline="' . nxb_core_h('item' . $idx . '_title') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($item['title']) . '</h3>
            <p class="mb-0 small text-muted" data-nx-inline="' . nxb_core_h('item' . $idx . '_text') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($item['text']) . '</p>
          </div>
        </div>
      </div>';
                }

                $html .= '
    </div>
  </div>
</section>';

                return $html;

            case 'core_hero':
                // Hero-Sektion (ohne innere Drop-Zonen, rein konfigurierbar)
                $titleText    = (string)($settings['title'] ?? $title ?? 'Hero-Titel');
                $subtitleText = (string)($settings['subtitle'] ?? '');
                $bodyText     = (string)($settings['text'] ?? '');

                $bg       = trim((string)($settings['bg'] ?? 'bg-dark'));
                $pad      = trim((string)($settings['padding'] ?? 'py-5'));
                $textMode = trim((string)($settings['mode'] ?? 'light')); // light | dark
                $bgImage  = trim((string)($settings['bgImage'] ?? ''));

                $align = (string)($settings['align'] ?? 'start'); // start, center, end
                $alignClass = 'text-start';
                if ($align === 'center') {
                    $alignClass = 'text-center';
                } elseif ($align === 'end' || $align === 'right') {
                    $alignClass = 'text-end';
                }

                $primaryLabel = (string)($settings['primaryLabel'] ?? 'Call to Action');
                $primaryUrl   = trim((string)($settings['primaryUrl'] ?? ''));
                $secondaryLabel = (string)($settings['secondaryLabel'] ?? '');
                $secondaryUrl   = trim((string)($settings['secondaryUrl'] ?? ''));

                $heightMode = trim((string)($settings['heightMode'] ?? ''));

                $outerClasses = trim('nx-hero ' . $bg . ' ' . $pad);
                if ($heightMode === 'vh-40') {
                    $outerClasses .= ' nx-hero-h-40';
                } elseif ($heightMode === 'vh-50') {
                    $outerClasses .= ' nx-hero-h-50';
                } elseif ($heightMode === 'vh-60') {
                    $outerClasses .= ' nx-hero-h-60';
                } elseif ($heightMode === 'vh-80') {
                    $outerClasses .= ' nx-hero-h-80';
                } elseif ($heightMode === 'vh-100') {
                    $outerClasses .= ' nx-hero-h-100';
                }
                $textClass    = ($textMode === 'dark') ? 'text-dark' : 'text-white';

                $sectionStyle = '';
                $sectionClass = $outerClasses;
                if ($bgImage !== '') {
                    $sectionClass .= ' position-relative';
                    $sectionStyle = ' style="background-image:url(' . nxb_core_h($bgImage) . '); background-size:cover; background-position:center;"';
                }
                $vis = nxb_visibility_class($settings);
                if ($vis !== '') {
                    $sectionClass .= ' ' . $vis;
                }

                $html = '<section class="' . nxb_core_h($sectionClass) . '"' . $sectionStyle . '>';
                if ($bgImage !== '') {
                    $html .= '<div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50" aria-hidden="true"></div>';
                }
                $showSubtitle = $subtitleText !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());
                $showBody = $bodyText !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());
                $showBtns = $primaryLabel !== '' || $secondaryLabel !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());

                $html .= '
  <div class="container position-relative">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-8">
        <div class="' . nxb_core_h($textClass . ' ' . $alignClass) . '">';

                $html .= '<h1 class="display-5 fw-bold mb-2" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($titleText) . '</h1>';

                if ($showSubtitle) {
                    $subCls = $subtitleText !== '' ? 'text-uppercase fw-semibold mb-3 small-1 text-primary' : 'mb-3 small opacity-75 nx-inline-placeholder';
                    $subVal = $subtitleText !== '' ? nxb_core_h($subtitleText) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $html .= '<p class="' . nxb_core_h($subCls) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subVal . '</p>';
                }

                if ($showBody) {
                    $bodyCls = $bodyText !== '' ? 'lead mb-4' : 'lead mb-4 text-muted nx-inline-placeholder';
                    $bodyVal = $bodyText !== '' ? nxb_core_h($bodyText) : 'Text – Doppelklick zum Hinzufügen';
                    $html .= '<p class="' . nxb_core_h($bodyCls) . '" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . $bodyVal . '</p>';
                }

                $btnGroup = '';
                if ($showBtns) {
                    $btnGroup .= '<div class="d-flex flex-wrap gap-2 justify-content-' . ($align === 'center' ? 'center' : ($align === 'end' || $align === 'right' ? 'end' : 'start')) . '">';
                    $primVal = $primaryLabel !== '' ? nxb_core_h($primaryLabel) : 'Button – Doppelklick';
                    $hrefPrimary = $primaryUrl !== '' ? nxb_core_h($primaryUrl) : '#';
                    $btnGroup .= '<a href="' . $hrefPrimary . '" class="btn btn-primary btn-lg px-4" data-nx-inline="primaryLabel" title="Doppelklick zum Bearbeiten">' . $primVal . '</a>';
                    if ($secondaryLabel !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                        $secVal = $secondaryLabel !== '' ? nxb_core_h($secondaryLabel) : 'Zweiter Button – Doppelklick';
                        $hrefSecondary = $secondaryUrl !== '' ? nxb_core_h($secondaryUrl) : '#';
                        $btnGroup .= '<a href="' . $hrefSecondary . '" class="btn btn-outline-light btn-lg px-4" data-nx-inline="secondaryLabel" title="Doppelklick zum Bearbeiten">' . $secVal . '</a>';
                    }
                    $btnGroup .= '</div>';
                }

                if ($btnGroup !== '') {
                    $html .= '
          ' . $btnGroup;
                }

                $html .= '
        </div>
      </div>
    </div>
  </div>
</section>';

                return $html;

            case 'core_hero_split':
                // Hero-Sektion mit zweispaltigem Layout (Text links, Bild rechts)
                $titleText    = (string)($settings['title'] ?? $title ?? 'Hero-Titel');
                $subtitleText = (string)($settings['subtitle'] ?? '');
                $bodyText     = (string)($settings['text'] ?? '');

                $bg       = trim((string)($settings['bg'] ?? ''));
                $pad      = trim((string)($settings['padding'] ?? 'py-0'));
                $textMode = trim((string)($settings['mode'] ?? 'dark')); // light | dark
                $bgImage  = trim((string)($settings['bgImage'] ?? ''));

                $align = (string)($settings['align'] ?? 'start'); // start, center, end
                $alignClass = 'text-start';
                if ($align === 'center') {
                    $alignClass = 'text-center';
                } elseif ($align === 'end' || $align === 'right') {
                    $alignClass = 'text-end';
                }

                $primaryLabel = (string)($settings['primaryLabel'] ?? 'Call to Action');
                $primaryUrl   = trim((string)($settings['primaryUrl'] ?? ''));

                $heightMode = trim((string)($settings['heightMode'] ?? ''));

                $outerClasses = trim('nx-hero-split ' . $bg . ' ' . $pad);
                if ($heightMode === 'vh-40') {
                    $outerClasses .= ' nx-hero-h-40';
                } elseif ($heightMode === 'vh-50') {
                    $outerClasses .= ' nx-hero-h-50';
                } elseif ($heightMode === 'vh-60') {
                    $outerClasses .= ' nx-hero-h-60';
                } elseif ($heightMode === 'vh-80') {
                    $outerClasses .= ' nx-hero-h-80';
                } elseif ($heightMode === 'vh-100') {
                    $outerClasses .= ' nx-hero-h-100';
                }
                $textClass    = ($textMode === 'dark') ? 'text-dark' : 'text-white';

                $vis = nxb_visibility_class($settings);
                if ($vis !== '') {
                    $outerClasses .= ' ' . $vis;
                }

                $showSubtitle = $subtitleText !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());
                $showBody = $bodyText !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());
                $showBtn = $primaryLabel !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());

                $html = '<section class="' . nxb_core_h($outerClasses) . ' w-100">
  <div class="d-sm-flex align-items-center justify-content-between w-100">
      <div class="nx-hero-split-text col-12 col-md-4 mx-auto mb-4 mb-sm-0 ' . nxb_core_h($textClass . ' ' . $alignClass) . '">';

                if ($showSubtitle) {
                    $subText = $subtitleText !== '' ? nxb_core_h($subtitleText) : 'SUBHEADLINE';
                    $subCls  = $subtitleText !== '' ? 'text-secondary text-uppercase small mb-2' : 'text-secondary text-uppercase small mb-2 nx-inline-placeholder';
                    $html .= '<span class="' . nxb_core_h($subCls) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</span>';
                }

                $html .= '<h1 class="display-4 fw-bold my-4" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($titleText) . '</h1>';

                if ($showBody) {
                    $bodyCls = $bodyText !== '' ? 'mb-4' : 'mb-4 text-muted nx-inline-placeholder';
                    $bodyVal = $bodyText !== '' ? nxb_core_h($bodyText) : 'Text – Doppelklick zum Hinzufügen';
                    $html .= '<p class="' . nxb_core_h($bodyCls) . '" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . $bodyVal . '</p>';
                }

                if ($showBtn) {
                    $btnVal = $primaryLabel !== '' ? nxb_core_h($primaryLabel) : 'Button – Doppelklick';
                    $hrefPrimary = $primaryUrl !== '' ? nxb_core_h($primaryUrl) : '#';
                    $html .= '<a href="' . $hrefPrimary . '" class="btn btn-primary px-5 py-3 mt-3 mt-sm-0" data-nx-inline="primaryLabel" title="Doppelklick zum Bearbeiten">' . $btnVal . '</a>';
                }

                $html .= '
      </div>
      <div class="nx-hero-split-image col-12 col-md-8">';
                if ($bgImage !== '') {
                    $html .= '<img src="' . nxb_core_h($bgImage) . '" alt="" class="nx-hero-split-img">';
                } elseif (function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $html .= '<div class="nx-hero-split-placeholder d-flex align-items-center justify-content-center text-muted small">Hintergrundbild im Hero-Split in den Einstellungen setzen.</div>';
                }
                $html .= '</div>
    </div>
  </section>';

                return $html;

            case 'core_spacer':
                $size = trim((string)($settings['size'] ?? 'py-4'));
                $validSizes = ['py-1', 'py-2', 'py-3', 'py-4', 'py-5', 'py-6'];
                if (!in_array($size, $validSizes, true)) {
                    $size = 'py-4';
                }
                $vis = nxb_visibility_class($settings);
                $cls = 'nx-spacer ' . nxb_core_h($size) . ($vis !== '' ? ' ' . $vis : '');
                return '<div class="' . $cls . '" aria-hidden="true"></div>';

            case 'core_quote':
                $quoteText = trim((string)($settings['text'] ?? ''));
                $author    = trim((string)($settings['author'] ?? ''));
                $source    = trim((string)($settings['source'] ?? ''));
                if ($quoteText === '') {
                    $quoteText = 'Zitat hier einfügen.';
                }
                $showQuoteMeta = $author !== '' || $source !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());
                $html = '<figure class="nx-quote mb-0">';
                $html .= '<blockquote class="blockquote">';
                $html .= '<p class="mb-0" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . nl2br(nxb_core_h($quoteText)) . '</p>';
                $html .= '</blockquote>';
                if ($showQuoteMeta) {
                    $html .= '<figcaption class="blockquote-footer mt-2">';
                    $authorCls = $author !== '' ? '' : 'nx-inline-placeholder';
                    $authorVal = $author !== '' ? nxb_core_h($author) : 'Autor – Doppelklick';
                    $html .= '<span class="' . nxb_core_h($authorCls) . '" data-nx-inline="author" title="Doppelklick zum Bearbeiten">' . $authorVal . '</span>';
                    if ($source !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                        $sourceVal = $source !== '' ? nxb_core_h($source) : 'Quelle – Doppelklick';
                        $html .= ' <cite class="' . ($source === '' ? 'nx-inline-placeholder' : '') . '" data-nx-inline="source" title="Doppelklick zum Bearbeiten">' . $sourceVal . '</cite>';
                    }
                    $html .= '</figcaption>';
                }
                $html .= '</figure>';
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $html . '</div>';

            case 'core_gallery':
                $columns = (int)($settings['columns'] ?? 3);
                if ($columns < 2 || $columns > 4) {
                    $columns = 3;
                }
                $colClass = $columns === 2 ? 'col-12 col-md-6' : ($columns === 4 ? 'col-6 col-md-3' : 'col-6 col-md-4');
                $images = [];
                for ($i = 1; $i <= 6; $i++) {
                    $src = trim((string)($settings["item{$i}_src"] ?? ''));
                    if ($src === '') {
                        continue;
                    }
                    $alt = trim((string)($settings["item{$i}_alt"] ?? ''));
                    $cap = trim((string)($settings["item{$i}_caption"] ?? ''));
                    $images[] = ['src' => $src, 'alt' => $alt, 'caption' => $cap];
                }
                if (empty($images)) {
                    return '<div class="alert alert-secondary small mb-3">Galerie – bitte Bilder in den Einstellungen angeben.</div>';
                }
                $align = (string)($settings['align'] ?? 'start');
                $justify = ($align === 'center') ? 'justify-content-center' : (($align === 'end' || $align === 'right') ? 'justify-content-end' : '');
                $vis = nxb_visibility_class($settings);
                $rowClass = 'row g-3 nx-gallery' . ($justify !== '' ? ' ' . $justify : '');
                $html = '<div class="' . nxb_core_h($rowClass) . '">';
                foreach ($images as $img) {
                    $html .= '<div class="' . $colClass . '">';
                    $html .= '<figure class="mb-0">';
                    $html .= '<img src="' . nxb_core_h($img['src']) . '" alt="' . nxb_core_h($img['alt']) . '" class="img-fluid rounded" loading="lazy" />';
                    if ($img['caption'] !== '') {
                        $html .= '<figcaption class="small text-muted mt-1">' . nxb_core_h($img['caption']) . '</figcaption>';
                    }
                    $html .= '</figure></div>';
                }
                $html .= '</div>';
                $outerClass = 'mb-3' . ($vis !== '' ? ' ' . $vis : '');
                return '<div class="' . nxb_core_h($outerClass) . '">' . $html . '</div>';

            case 'core_tabs':
                $tabsId = 'nx-tabs-' . substr(md5(json_encode($settings)), 0, 8);
                $tabTitles = [];
                $tabContents = [];
                for ($i = 1; $i <= 5; $i++) {
                    $title = trim((string)($settings["tab{$i}_title"] ?? ''));
                    $content = trim((string)($settings["tab{$i}_content"] ?? ''));
                    if ($title === '' && $content === '') {
                        continue;
                    }
                    if ($title === '') {
                        $title = "Tab {$i}";
                    }
                    $tabId = $tabsId . '-tab' . $i;
                    $tabTitles[] = ['id' => $tabId, 'title' => $title];
                    $tabContents[] = ['id' => $tabId, 'content' => $content];
                }
                if (empty($tabTitles)) {
                    return '<div class="alert alert-secondary small mb-3">Tabs – bitte Reiter in den Einstellungen anlegen.</div>';
                }
                $align = (string)($settings['align'] ?? 'start');
                $navJustify = ($align === 'center') ? 'justify-content-center' : (($align === 'end' || $align === 'right') ? 'justify-content-end' : '');
                $vis = nxb_visibility_class($settings);
                $tabsClass = 'nx-tabs mb-3' . ($vis !== '' ? ' ' . $vis : '');
                $html = '<div class="' . nxb_core_h($tabsClass) . '">';
                $navClass = 'nav nav-tabs' . ($navJustify !== '' ? ' ' . $navJustify : '');
                $html .= '<ul class="' . nxb_core_h($navClass) . '" id="' . nxb_core_h($tabsId) . '" role="tablist">';
                foreach ($tabTitles as $idx => $t) {
                    $active = $idx === 0 ? ' active' : '';
                    $html .= '<li class="nav-item" role="presentation">';
                    $html .= '<button class="nav-link' . $active . '" id="' . nxb_core_h($t['id']) . '-btn" data-bs-toggle="tab" data-bs-target="#' . nxb_core_h($t['id']) . '" type="button" role="tab">' . nxb_core_h($t['title']) . '</button>';
                    $html .= '</li>';
                }
                $html .= '</ul><div class="tab-content p-3 border border-top-0 rounded-bottom">';
                foreach ($tabContents as $idx => $c) {
                    $active = $idx === 0 ? ' show active' : '';
                    $html .= '<div class="tab-pane fade' . $active . '" id="' . nxb_core_h($c['id']) . '" role="tabpanel">';
                    $html .= $c['content'] !== '' ? '<div class="nx-tab-body">' . nl2br(nxb_core_h($c['content'])) . '</div>' : '<p class="text-muted small mb-0">Inhalt für diesen Tab eingeben.</p>';
                    $html .= '</div>';
                }
                $html .= '</div></div>';
                return $html;

            case 'core_table':
                // Konfigurierbare Tabelle mit Inline-Bearbeitung der Zellen
                $columns = (int)($settings['columns'] ?? 3);
                if ($columns < 1) {
                    $columns = 1;
                } elseif ($columns > 6) {
                    $columns = 6;
                }
                $rows = (int)($settings['rows'] ?? 3);
                if ($rows < 1) {
                    $rows = 1;
                } elseif ($rows > 10) {
                    $rows = 10;
                }
                $hasHeader  = !empty($settings['hasHeader']);
                $striped    = !empty($settings['striped']);
                $bordered   = !empty($settings['bordered']);
                $hover      = !empty($settings['hover']);
                $small      = !empty($settings['small']);
                $responsive = !empty($settings['responsive']);

                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $tableClasses = ['table'];
                if ($striped) {
                    $tableClasses[] = 'table-striped';
                }
                if ($bordered) {
                    $tableClasses[] = 'table-bordered';
                }
                if ($hover) {
                    $tableClasses[] = 'table-hover';
                }
                if ($small) {
                    $tableClasses[] = 'table-sm';
                }
                $tableClasses[] = 'mb-0';

                $caption = trim((string)($settings['caption'] ?? ''));
                $showCaption = $caption !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());

                $html = '<div class="' . nxb_core_h($wrapClass) . '">';
                if ($responsive) {
                    $html .= '<div class="table-responsive">';
                }
                $html .= '<table class="' . nxb_core_h(implode(' ', $tableClasses)) . '">';

                if ($showCaption) {
                    $capText = $caption !== '' ? nxb_core_h($caption) : 'Tabellenbeschreibung – Doppelklick zum Hinzufügen';
                    $capClass = $caption !== '' ? 'caption-top text-muted small' : 'caption-top text-muted small nx-inline-placeholder';
                    $html .= '<caption class="' . nxb_core_h($capClass) . '" data-nx-inline="caption" title="Doppelklick zum Bearbeiten">' . $capText . '</caption>';
                }

                if ($hasHeader) {
                    $html .= '<thead><tr>';
                    for ($c = 1; $c <= $columns; $c++) {
                        $key = 'col' . $c . '_header';
                        $val = trim((string)($settings[$key] ?? ''));
                        $text = $val;
                        $cls = '';
                        if ($text === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                            $text = 'Spalte ' . $c;
                            $cls = 'nx-inline-placeholder';
                        }
                        $html .= '<th scope="col" class="' . nxb_core_h($cls) . '" data-nx-inline="' . nxb_core_h($key) . '" title="Doppelklick zum Bearbeiten">' . ($text !== '' ? nxb_core_h($text) : '') . '</th>';
                    }
                    $html .= '</tr></thead>';
                }

                $html .= '<tbody>';
                for ($r = 1; $r <= $rows; $r++) {
                    $html .= '<tr>';
                    for ($c = 1; $c <= $columns; $c++) {
                        $key = 'cell' . $r . '_' . $c;
                        $val = trim((string)($settings[$key] ?? ''));
                        $text = $val;
                        $cls = '';
                        if ($text === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                            $text = 'Zelle ' . $r . '×' . $c;
                            $cls = 'nx-inline-placeholder';
                        }
                        $html .= '<td class="' . nxb_core_h($cls) . '" data-nx-inline="' . nxb_core_h($key) . '" title="Doppelklick zum Bearbeiten">' . ($text !== '' ? nxb_core_h($text) : '') . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
                if ($responsive) {
                    $html .= '</div>';
                }
                $html .= '</div>';
                return $html;

            case 'core_container':
                // Reiner Breiten-Container (container oder container-fluid) mit einer inneren Zone
                $sectionId = (string)($settings['id'] ?? uniqid('sec_', false));
                $bg = trim((string)($settings['bg'] ?? ''));
                $pad = trim((string)($settings['padding'] ?? 'py-4'));
                $container = (string)($settings['container'] ?? 'container');
                $contentWidthStyle = nxb_core_inline_max_width_style($settings);
                if ($container !== 'container-fluid') {
                    $container = 'container';
                }
                $vis = nxb_visibility_class($settings);
                $zoneName = 'sec_' . $sectionId . '_c1';

                $inner = '';
                if (!empty($allRows[$zoneName]) && is_array($allRows[$zoneName])) {
                    foreach ($allRows[$zoneName] as $w) {
                        $widgetKey  = (string)$w['widget_key'];
                        $instanceId = (string)$w['instance_id'];
                        $wSettings  = is_array($w['settings'] ?? null) ? $w['settings'] : [];
                        $wTitle     = (string)$w['title'];
                        if (function_exists('nxb_live_wrap') && function_exists('nxb_render_widget_content')) {
                            $inner .= nxb_live_wrap($w, nxb_render_widget_content($widgetKey, $instanceId, $wSettings, $wTitle));
                        } else {
                            $inner .= nxb_render_frontend_widget_html($widgetKey, $instanceId, $wSettings, $wTitle);
                        }
                    }
                }
                if ($inner === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $inner = '<div class="nx-drop-hint">Hier Blöcke ablegen – Reihenfolge frei wählbar</div>';
                }

                $zoneClasses = 'nx-zone' . (function_exists('nxb_is_builder') && nxb_is_builder() ? ' nx-live-zone' : '');
                $outerClasses = trim('nx-container ' . $pad . ' ' . $bg . ($vis !== '' ? ' ' . $vis : ''));

                return '<div class="' . nxb_core_h($outerClasses) . '">
  <div class="' . nxb_core_h($container) . '"' . $contentWidthStyle . '>
    <div class="' . nxb_core_h($zoneClasses) . '" data-nx-zone="' . nxb_core_h($zoneName) . '">
      ' . $inner . '
    </div>
  </div>
</div>';

            case 'core_row':
                // Eine Zeile mit einer Zone, in die nur Cols gezogen werden (Builder prüft das)
                $rowId = (string)($settings['id'] ?? uniqid('row_', false));
                $bg = trim((string)($settings['bg'] ?? ''));
                $pad = trim((string)($settings['padding'] ?? 'py-4'));
                $container = (string)($settings['container'] ?? 'container');
                $contentWidthStyle = nxb_core_inline_max_width_style($settings);
                if ($container !== 'container-fluid') {
                    $container = 'container';
                }
                $vis = nxb_visibility_class($settings);
                $zoneName = 'row_' . $rowId;

                $inner = '';
                if (!empty($allRows[$zoneName]) && is_array($allRows[$zoneName])) {
                    foreach ($allRows[$zoneName] as $w) {
                        $widgetKey  = (string)$w['widget_key'];
                        $instanceId = (string)$w['instance_id'];
                        $wSettings  = is_array($w['settings'] ?? null) ? $w['settings'] : [];
                        $wTitle     = (string)$w['title'];
                        if (function_exists('nxb_live_wrap') && function_exists('nxb_render_widget_content')) {
                            $content = nxb_render_widget_content($widgetKey, $instanceId, $wSettings, $wTitle);
                            // Im Builder: Col-Klassen + data-nx-col-span am Wrapper für Grid (nebeneinander)
                            $wrapClasses = '';
                            $wrapData = [];
                            if ($widgetKey === 'core_col' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                                $span = (int)($wSettings['span'] ?? 6);
                                if ($span < 1 || $span > 12) {
                                    $span = 6;
                                }
                                $wrapClasses = 'col-12 col-md-' . $span;
                                $wrapData['data-nx-col-span'] = (string)$span;
                            }
                            $inner .= nxb_live_wrap($w, $content, $wrapClasses, $wrapData);
                        } else {
                            $inner .= nxb_render_frontend_widget_html($widgetKey, $instanceId, $wSettings, $wTitle);
                        }
                    }
                }
                if ($inner === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $inner = '<div class="nx-drop-hint">Nur Cols hier ablegen – Reihenfolge frei wählbar</div>';
                }

                $zoneClasses = 'row g-4 nx-row-cols nx-zone' . (function_exists('nxb_is_builder') && nxb_is_builder() ? ' nx-live-zone' : '');
                $outerClasses = trim('nx-row ' . $pad . ' ' . $bg . ($vis !== '' ? ' ' . $vis : ''));

                return '<section class="' . nxb_core_h($outerClasses) . '">
  <div class="' . nxb_core_h($container) . '"' . $contentWidthStyle . '>
    <div class="' . nxb_core_h($zoneClasses) . '" data-nx-zone="' . nxb_core_h($zoneName) . '">
      ' . $inner . '
    </div>
  </div>
</section>';

            case 'core_col':
                // Eine Spalte mit Breiten-Einstellung und einer inneren Zone für beliebigen Inhalt
                $colId = (string)($settings['id'] ?? uniqid('col_', false));
                $span = (int)($settings['span'] ?? 6);
                if ($span < 1 || $span > 12) {
                    $span = 6;
                }
                $vis = nxb_visibility_class($settings);
                $zoneName = 'col_' . $colId;
                $colClass = 'col-12 col-md-' . $span;

                $inner = '';
                if (!empty($allRows[$zoneName]) && is_array($allRows[$zoneName])) {
                    foreach ($allRows[$zoneName] as $w) {
                        $widgetKey  = (string)$w['widget_key'];
                        $instanceId = (string)$w['instance_id'];
                        $wSettings  = is_array($w['settings'] ?? null) ? $w['settings'] : [];
                        $wTitle     = (string)$w['title'];
                        if (function_exists('nxb_live_wrap') && function_exists('nxb_render_widget_content')) {
                            $inner .= nxb_live_wrap($w, nxb_render_widget_content($widgetKey, $instanceId, $wSettings, $wTitle));
                        } else {
                            $inner .= nxb_render_frontend_widget_html($widgetKey, $instanceId, $wSettings, $wTitle);
                        }
                    }
                }
                if ($inner === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $inner = '<div class="nx-drop-hint">Hier Blöcke ablegen</div>';
                }

                $zoneClasses = trim($colClass . ' nx-zone' . (function_exists('nxb_is_builder') && nxb_is_builder() ? ' nx-live-zone' : '') . ($vis !== '' ? ' ' . $vis : ''));

                return '<div class="' . nxb_core_h($zoneClasses) . '" data-nx-zone="' . nxb_core_h($zoneName) . '">
  ' . $inner . '
</div>';

            case 'core_section_full':
            case 'core_section_two_col':
            case 'core_section_three_col':
                // Einfache, bootstrap-basierte Layout-Sektionen mit internen Zonen
                // Widgets in diesen Spalten werden aus $__NX_ALL_WIDGET_ROWS gerendert
                $sectionId = (string)($settings['id'] ?? uniqid('sec_', false));
                $bg = trim((string)($settings['bg'] ?? ''));
                $pad = trim((string)($settings['padding'] ?? 'py-5'));
                $container = (string)($settings['container'] ?? 'container'); // container oder container-fluid
                $contentWidthStyle = nxb_core_inline_max_width_style($settings);

                $vis = nxb_visibility_class($settings);
                $outerClasses = trim('nx-section ' . $pad . ' ' . $bg . ($vis !== '' ? ' ' . $vis : ''));

                $cols = [];
                if ($widget_key === 'core_section_full') {
                    $cols = [
                        ['id' => $sectionId . '_c1', 'class' => 'col-12'],
                    ];
                } elseif ($widget_key === 'core_section_two_col') {
                    $cols = [
                        ['id' => $sectionId . '_c1', 'class' => 'col-12 col-md-6 mb-3 mb-md-0'],
                        ['id' => $sectionId . '_c2', 'class' => 'col-12 col-md-6'],
                    ];
                } else {
                    $cols = [
                        ['id' => $sectionId . '_c1', 'class' => 'col-12 col-md-4 mb-3 mb-md-0'],
                        ['id' => $sectionId . '_c2', 'class' => 'col-12 col-md-4 mb-3 mb-md-0'],
                        ['id' => $sectionId . '_c3', 'class' => 'col-12 col-md-4'],
                    ];
                }

                $html = '<section class="' . nxb_core_h($outerClasses) . ' nx-section">
  <div class="' . nxb_core_h($container) . '"' . $contentWidthStyle . '>
    <div class="row g-4 nx-section-cols">';

                foreach ($cols as $col) {
                    $zoneName = 'sec_' . $sectionId . '_' . $col['id'];
                    $inner = '';

                    // Widgets aus dieser Zonen-Position rendern (falls vorhanden)
                    if (!empty($allRows[$zoneName]) && is_array($allRows[$zoneName])) {
                        foreach ($allRows[$zoneName] as $w) {
                            $widgetKey  = (string)$w['widget_key'];
                            $instanceId = (string)$w['instance_id'];
                            $wSettings  = is_array($w['settings'] ?? null) ? $w['settings'] : [];
                            $wTitle     = (string)$w['title'];

                            // Im Builder: nxb_live_wrap + nxb_render_widget_content verwenden
                            if (function_exists('nxb_live_wrap') && function_exists('nxb_render_widget_content')) {
                                $inner .= nxb_live_wrap(
                                    $w,
                                    nxb_render_widget_content(
                                        $widgetKey,
                                        $instanceId,
                                        $wSettings,
                                        $wTitle
                                    )
                                );
                            } else {
                                // Im öffentlichen Frontend: Core-Widgets direkt rendern,
                                // Plugin-Widgets über PluginManager
                                $inner .= nxb_render_frontend_widget_html($widgetKey, $instanceId, $wSettings, $wTitle);
                            }
                        }
                    }

                    // Fallback-Hinweis, wenn noch leer (nur im Builder sinnvoll)
                    if ($inner === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                        $inner = '<div class="nx-drop-hint">Hier Blöcke ablegen – Reihenfolge frei wählbar</div>';
                    }

                    $zoneClasses = 'nx-zone';
                    if (function_exists('nxb_is_builder') && nxb_is_builder()) {
                        $zoneClasses .= ' nx-live-zone';
                    }

                    $html .= '
      <div class="' . nxb_core_h($col['class'] . ' ' . $zoneClasses) . '" data-nx-zone="' . nxb_core_h($zoneName) . '">
        ' . $inner . '
      </div>';
                }

                $html .= '
    </div>
  </div>
</section>';

                return $html;

            case 'core_divider':
                $style = trim((string)($settings['style'] ?? ''));
                $valid = ['', 'opacity-25', 'opacity-50', 'opacity-100', 'my-4', 'my-5'];
                if (!in_array($style, $valid, true)) {
                    $style = '';
                }
                $vis = nxb_visibility_class($settings);
                $cls = 'nx-divider ' . $style . ($vis !== '' ? ' ' . $vis : '');
                return '<hr class="' . nxb_core_h(trim($cls)) . '" />';

            case 'core_list':
                $items = [];
                for ($i = 1; $i <= 10; $i++) {
                    $t = trim((string)($settings["item{$i}"] ?? ''));
                    if ($t !== '') {
                        $items[] = $t;
                    }
                }
                $type = (string)($settings['type'] ?? 'ul');
                $tag = ($type === 'ol') ? 'ol' : 'ul';
                $icon = trim((string)($settings['icon'] ?? ''));
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                if (empty($items)) {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><' . $tag . ' class="list-unstyled"><li class="text-muted">Liste in Einstellungen füllen.</li></' . $tag . '></div>';
                }
                $inner = '';
                foreach ($items as $item) {
                    if ($icon !== '') {
                        $inner .= '<li class="d-flex align-items-center gap-2"><i class="bi ' . nxb_core_h($icon) . ' text-primary"></i>' . nxb_core_h($item) . '</li>';
                    } else {
                        $inner .= '<li>' . nxb_core_h($item) . '</li>';
                    }
                }
                $listClass = $icon !== '' ? 'list-unstyled' : '';
                return '<div class="' . nxb_core_h($wrapClass) . '"><' . $tag . ' class="' . nxb_core_h($listClass) . '">' . $inner . '</' . $tag . '></div>';

            case 'core_alert':
                $variant = (string)($settings['variant'] ?? 'info');
                $allowed = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
                if (!in_array($variant, $allowed, true)) {
                    $variant = 'info';
                }
                $title = trim((string)($settings['title'] ?? ''));
                $text = trim((string)($settings['text'] ?? 'Hinweis hier eingeben.'));
                $dismiss = !empty($settings['dismissible']);
                $vis = nxb_visibility_class($settings);
                $cls = 'alert alert-' . $variant . ($dismiss ? ' alert-dismissible fade show' : '') . ($vis !== '' ? ' ' . $vis : '');
                $html = '<div class="' . nxb_core_h($cls) . '" role="alert">';
                if ($dismiss) {
                    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>';
                }
                $showAlertTitle = $title !== '' || (function_exists('nxb_is_builder') && nxb_is_builder());
                if ($showAlertTitle) {
                    $titVal = $title !== '' ? nxb_core_h($title) : 'Überschrift – Doppelklick';
                    $titCls = $title !== '' ? 'alert-heading' : 'alert-heading text-muted nx-inline-placeholder';
                    $html .= '<h4 class="' . nxb_core_h($titCls) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titVal . '</h4>';
                }
                $html .= '<p class="mb-0" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . nl2br(nxb_core_h($text)) . '</p></div>';
                return '<div class="mb-3">' . $html . '</div>';

            case 'core_badge':
                $text = trim((string)($settings['text'] ?? 'Badge'));
                $variant = (string)($settings['variant'] ?? 'primary');
                $allowed = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
                if (!in_array($variant, $allowed, true)) {
                    $variant = 'primary';
                }
                $rounded = !empty($settings['rounded']);
                $url = trim((string)($settings['url'] ?? ''));
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $cls = 'badge text-bg-' . $variant . ($rounded ? ' rounded-pill' : '');
                $badge = '<span class="' . nxb_core_h($cls) . '" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . nxb_core_h($text) . '</span>';
                if ($url !== '') {
                    $badge = '<a href="' . nxb_core_h($url) . '" class="text-decoration-none">' . $badge . '</a>';
                }
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $badge . '</div>';

            case 'core_faq':
                // FAQ-Sektion mit Titel/Untertitel und mehreren Accordion-Einträgen
                $sectionTitle    = trim((string)($settings['title'] ?? 'FAQ'));
                $sectionSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $sectionClass = 'nx-faq mb-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $items = [];
                for ($i = 1; $i <= 6; $i++) {
                    $q = trim((string)($settings["item{$i}_title"] ?? ''));
                    $a = trim((string)($settings["item{$i}_content"] ?? ''));
                    if ($q === '' && $a === '') {
                        continue;
                    }
                    if ($q === '') {
                        $q = "Frage {$i}";
                    }
                    $items[] = ['title' => $q, 'content' => $a];
                }

                $accId = 'nx-faq-' . substr(md5(json_encode($settings)), 0, 8);

                $html = '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="row justify-content-center"><div class="col-lg-10">';

                if ($sectionTitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $titleText = $sectionTitle !== '' ? nxb_core_h($sectionTitle) : 'FAQ – Doppelklick zum Bearbeiten';
                    $titleClass = $sectionTitle !== '' ? 'h2 mb-2' : 'h2 mb-2 text-muted nx-inline-placeholder';
                    $html .= '<h2 class="' . nxb_core_h($titleClass) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titleText . '</h2>';
                }
                if ($sectionSubtitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $subText = $sectionSubtitle !== '' ? nxb_core_h($sectionSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $subClass = $sectionSubtitle !== '' ? 'mb-3 text-muted' : 'mb-3 text-muted nx-inline-placeholder';
                    $html .= '<p class="' . nxb_core_h($subClass) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</p>';
                }

                if (empty($items)) {
                    if (function_exists('nxb_is_builder') && nxb_is_builder()) {
                        $html .= '<div class="alert alert-secondary small mb-0">FAQ – Einträge in den Einstellungen anlegen.</div>';
                    }
                    $html .= '</div></div></div></section>';
                    return $html;
                }

                $html .= '<div class="accordion" id="' . nxb_core_h($accId) . '">';
                foreach ($items as $idx => $it) {
                    $itemIndex = $idx + 1;
                    $id = $accId . '-item' . $itemIndex;
                    $show = $idx === 0 ? ' show' : '';
                    $collapsed = $idx !== 0 ? ' collapsed' : '';
                    $questionField = 'item' . $itemIndex . '_title';
                    $answerField = 'item' . $itemIndex . '_content';
                    $questionText = $it['title'] !== '' ? nxb_core_h($it['title']) : 'Frage ' . $itemIndex;
                    $answerHasContent = $it['content'] !== '';
                    $answerText = $answerHasContent ? nl2br(nxb_core_h($it['content'])) : 'Antwort – Doppelklick zum Hinzufügen';
                    $answerClass = $answerHasContent ? 'mb-0' : 'mb-0 text-muted nx-inline-placeholder';
                    $html .= '<div class="accordion-item">';
                    $html .= '<h2 class="accordion-header"><button class="accordion-button' . $collapsed . '" type="button" data-bs-toggle="collapse" data-bs-target="#' . nxb_core_h($id) . '" aria-expanded="' . ($idx === 0 ? 'true' : 'false') . '" data-nx-inline="' . nxb_core_h($questionField) . '" title="Doppelklick zum Bearbeiten">' . $questionText . '</button></h2>';
                    $html .= '<div id="' . nxb_core_h($id) . '" class="accordion-collapse collapse' . $show . '" data-bs-parent="#' . nxb_core_h($accId) . '"><div class="accordion-body"><p class="' . nxb_core_h($answerClass) . '" data-nx-inline="' . nxb_core_h($answerField) . '" title="Doppelklick zum Bearbeiten">' . $answerText . '</p></div></div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div></div></div></section>';
                return $html;

            case 'core_testimonials':
                // Testimonials / Referenzen als Karten-Grid
                // Titel bevorzugt aus den Settings, fallback auf Widget-Titel
                $sectionTitle    = trim((string)($settings['title'] ?? $title ?? 'Testimonials'));
                $sectionSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $columns = (int)($settings['columns'] ?? 3);
                if ($columns < 1) {
                    $columns = 1;
                } elseif ($columns > 3) {
                    $columns = 3;
                }
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $sectionClass = 'nx-testimonials mb-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $items = [];
                for ($i = 1; $i <= 6; $i++) {
                    $quote   = trim((string)($settings["item{$i}_quote"] ?? ''));
                    $name    = trim((string)($settings["item{$i}_name"] ?? ''));
                    $role    = trim((string)($settings["item{$i}_role"] ?? ''));
                    $company = trim((string)($settings["item{$i}_company"] ?? ''));
                    $image   = trim((string)($settings["item{$i}_image"] ?? ''));
                    if ($quote === '' && $name === '' && $role === '' && $company === '' && $image === '') {
                        continue;
                    }
                    if ($quote === '') {
                        $quote = 'Referenztext – Doppelklick zum Bearbeiten.';
                    }
                    $items[] = [
                        'index'   => $i,
                        'quote'   => $quote,
                        'name'    => $name,
                        'role'    => $role,
                        'company' => $company,
                        'image'   => $image,
                    ];
                }

                if (empty($items) && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    // Platzhalter mit drei Demo-Items
                    $items = [
                        [
                            'index' => 1,
                            'quote' => '„Tolles Projekt – hat unsere Prozesse deutlich vereinfacht.“',
                            'name' => 'Max Mustermann',
                            'role' => 'Teamleiter',
                            'company' => 'Beispiel GmbH',
                            'image' => '',
                        ],
                        [
                            'index' => 2,
                            'quote' => '„Sehr zuverlässiger Service und großartige Community.“',
                            'name' => 'Erika Muster',
                            'role' => 'Community Managerin',
                            'company' => '',
                            'image' => '',
                        ],
                        [
                            'index' => 3,
                            'quote' => '„In wenigen Tagen produktiv – intuitive Oberfläche und starke Features.“',
                            'name' => '',
                            'role' => '',
                            'company' => '',
                            'image' => '',
                        ],
                    ];
                }

                if (empty($items)) {
                    return '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="alert alert-secondary small mb-0">Testimonials – Einträge in den Einstellungen anlegen.</div></div></section>';
                }

                $colClass = 'col-12';
                if ($columns === 2) {
                    $colClass = 'col-12 col-md-6';
                } elseif ($columns === 3) {
                    $colClass = 'col-12 col-md-4';
                }

                $html = '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="row justify-content-center"><div class="col-lg-11">';
                if ($sectionTitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $titleText = $sectionTitle !== '' ? nxb_core_h($sectionTitle) : 'Testimonials – Doppelklick zum Bearbeiten';
                    $titleClass = $sectionTitle !== '' ? 'h2 mb-2' : 'h2 mb-2 text-muted nx-inline-placeholder';
                    $html .= '<h2 class="' . nxb_core_h($titleClass) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titleText . '</h2>';
                }
                if ($sectionSubtitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $subText = $sectionSubtitle !== '' ? nxb_core_h($sectionSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $subClass = $sectionSubtitle !== '' ? 'mb-4 text-muted' : 'mb-4 text-muted nx-inline-placeholder';
                    $html .= '<p class="' . nxb_core_h($subClass) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</p>';
                }

                $html .= '<div class="row g-4">';
                foreach ($items as $it) {
                    $idx = (int)($it['index'] ?? 0);
                    $html .= '<div class="' . nxb_core_h($colClass) . '"><div class="card h-100 border-0 shadow-sm">';
                    $html .= '<div class="card-body">';
                    $quoteText = nxb_core_h($it['quote']);
                    $html .= '<p class="mb-3 fst-italic" data-nx-inline="' . nxb_core_h('item' . $idx . '_quote') . '" title="Doppelklick zum Bearbeiten">' . $quoteText . '</p>';
                    if ($it['name'] !== '' || $it['role'] !== '' || $it['company'] !== '' || $it['image'] !== '') {
                        $html .= '<div class="d-flex align-items-center gap-3">';
                        if ($it['image'] !== '') {
                            $html .= '<img src="' . nxb_core_h($it['image']) . '" alt="" class="rounded-circle flex-shrink-0" style="width:56px;height:56px;object-fit:cover;" loading="lazy" />';
                        }
                        $html .= '<div class="small">';
                        if ($it['name'] !== '') {
                            $html .= '<div class="fw-semibold" data-nx-inline="' . nxb_core_h('item' . $idx . '_name') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['name']) . '</div>';
                        }
                        if ($it['role'] !== '' || $it['company'] !== '') {
                            $html .= '<div class="text-muted">';
                            if ($it['role'] !== '') {
                                $html .= '<span data-nx-inline="' . nxb_core_h('item' . $idx . '_role') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['role']) . '</span>';
                            }
                            if ($it['role'] !== '' && $it['company'] !== '') {
                                $html .= ' · ';
                            }
                            if ($it['company'] !== '') {
                                $html .= '<span data-nx-inline="' . nxb_core_h('item' . $idx . '_company') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['company']) . '</span>';
                            }
                            $html .= '</div>';
                        }
                        $html .= '</div></div>';
                    }
                    $html .= '</div></div></div>';
                }
                $html .= '</div></div></div></section>';
                return $html;

            case 'core_timeline':
                // Vertikale Timeline mit Schritten/Etappen
                $sectionTitle    = trim((string)($settings['title'] ?? ''));
                $sectionSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $sectionClass = 'nx-timeline mb-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $items = [];
                for ($i = 1; $i <= 8; $i++) {
                    $title = trim((string)($settings["item{$i}_title"] ?? ''));
                    $meta  = trim((string)($settings["item{$i}_meta"] ?? ''));
                    $text  = trim((string)($settings["item{$i}_text"] ?? ''));
                    $status = trim((string)($settings["item{$i}_status"] ?? ''));
                    if ($title === '' && $meta === '' && $text === '' && $status === '') {
                        continue;
                    }
                    if ($title === '') {
                        $title = "Schritt {$i}";
                    }
                    $items[] = [
                        'index'  => $i,
                        'title'  => $title,
                        'meta'   => $meta,
                        'text'   => $text,
                        'status' => $status,
                    ];
                }

                if (empty($items) && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $items = [
                        ['index' => 1, 'title' => 'Kickoff', 'meta' => 'Phase 1', 'text' => 'Projektstart und Zieldefinition.', 'status' => 'done'],
                        ['index' => 2, 'title' => 'Umsetzung', 'meta' => 'Phase 2', 'text' => 'Entwicklung der wichtigsten Features.', 'status' => 'active'],
                        ['index' => 3, 'title' => 'Rollout', 'meta' => 'Phase 3', 'text' => 'Launch und kontinuierliche Optimierung.', 'status' => 'upcoming'],
                    ];
                }

                if (empty($items)) {
                    return '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="alert alert-secondary small mb-0">Timeline – Einträge in den Einstellungen anlegen.</div></div></section>';
                }

                $html = '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="row justify-content-center"><div class="col-lg-10 col-xl-8">';
                if ($sectionTitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $titleText = $sectionTitle !== '' ? nxb_core_h($sectionTitle) : 'Timeline – Doppelklick zum Bearbeiten';
                    $titleClass = $sectionTitle !== '' ? 'h2 mb-2' : 'h2 mb-2 text-muted nx-inline-placeholder';
                    $html .= '<h2 class="' . nxb_core_h($titleClass) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titleText . '</h2>';
                }
                if ($sectionSubtitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $subText = $sectionSubtitle !== '' ? nxb_core_h($sectionSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $subClass = $sectionSubtitle !== '' ? 'mb-4 text-muted' : 'mb-4 text-muted nx-inline-placeholder';
                    $html .= '<p class="' . nxb_core_h($subClass) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</p>';
                }

                $html .= '<div class="nx-timeline-list position-relative ps-4">';
                $html .= '<div class="position-absolute top-0 bottom-0 start-1 translate-middle-x bg-light border-start" style="width:2px;"></div>';

                foreach ($items as $idx => $it) {
                    $itemIndex = (int)($it['index'] ?? ($idx + 1));
                    $status = $it['status'];
                    $dotClass = 'bg-secondary';
                    if ($status === 'done') {
                        $dotClass = 'bg-success';
                    } elseif ($status === 'active') {
                        $dotClass = 'bg-primary';
                    } elseif ($status === 'upcoming') {
                        $dotClass = 'bg-warning';
                    }
                    $html .= '<div class="d-flex gap-3 mb-4">';
                    $html .= '<div class="flex-shrink-0 position-relative" style="width:1.5rem;">';
                    $html .= '<div class="rounded-circle ' . nxb_core_h($dotClass) . '" style="width:12px;height:12px;margin-top:.4rem;"></div>';
                    $html .= '</div>';
                    $html .= '<div class="flex-grow-1">';
                    if ($it['meta'] !== '') {
                        $html .= '<div class="small text-muted mb-1" data-nx-inline="' . nxb_core_h('item' . $itemIndex . '_meta') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['meta']) . '</div>';
                    }
                    $html .= '<h3 class="h6 mb-1" data-nx-inline="' . nxb_core_h('item' . $itemIndex . '_title') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['title']) . '</h3>';
                    if ($it['text'] !== '') {
                        $html .= '<p class="mb-0 small text-body-secondary" data-nx-inline="' . nxb_core_h('item' . $itemIndex . '_text') . '" title="Doppelklick zum Bearbeiten">' . nl2br(nxb_core_h($it['text'])) . '</p>';
                    }
                    $html .= '</div></div>';
                }

                $html .= '</div></div></div></div></section>';
                return $html;

            case 'core_accordion':
                $accId = 'nx-acc-' . substr(md5(json_encode($settings)), 0, 8);
                $items = [];
                for ($i = 1; $i <= 6; $i++) {
                    $head = trim((string)($settings["item{$i}_title"] ?? ''));
                    $body = trim((string)($settings["item{$i}_content"] ?? ''));
                    if ($head === '' && $body === '') {
                        continue;
                    }
                    if ($head === '') {
                        $head = "Eintrag {$i}";
                    }
                    $items[] = ['title' => $head, 'content' => $body];
                }
                $vis = nxb_visibility_class($settings);
                $cls = 'accordion mb-3' . ($vis !== '' ? ' ' . $vis : '');
                if (empty($items)) {
                    return '<div class="' . nxb_core_h($cls) . '"><div class="alert alert-secondary small mb-0">Accordion – Einträge in Einstellungen anlegen.</div></div>';
                }
                $html = '<div class="' . nxb_core_h($cls) . '" id="' . nxb_core_h($accId) . '">';
                foreach ($items as $idx => $it) {
                    $id = $accId . '-item' . ($idx + 1);
                    $show = $idx === 0 ? ' show' : '';
                    $collapsed = $idx !== 0 ? ' collapsed' : '';
                    $html .= '<div class="accordion-item">';
                    $html .= '<h2 class="accordion-header"><button class="accordion-button' . $collapsed . '" type="button" data-bs-toggle="collapse" data-bs-target="#' . nxb_core_h($id) . '" aria-expanded="' . ($idx === 0 ? 'true' : 'false') . '">' . nxb_core_h($it['title']) . '</button></h2>';
                    $html .= '<div id="' . nxb_core_h($id) . '" class="accordion-collapse collapse' . $show . '" data-bs-parent="#' . nxb_core_h($accId) . '"><div class="accordion-body">' . ($it['content'] !== '' ? nl2br(nxb_core_h($it['content'])) : '<span class="text-muted">Inhalt eingeben.</span>') . '</div></div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                return $html;

            case 'core_video':
                $url = trim((string)($settings['url'] ?? ''));
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ratio ratio-16x9' . ($vis !== '' ? ' ' . $vis : '');
                if ($url === '') {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><div class="d-flex align-items-center justify-content-center bg-secondary text-white">Video-URL in Einstellungen angeben (YouTube/Vimeo).</div></div>';
                }
                $embed = '';
                if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]+)~', $url, $m)) {
                    $embed = 'https://www.youtube.com/embed/' . $m[1];
                } elseif (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
                    $embed = 'https://player.vimeo.com/video/' . $m[1];
                }
                if ($embed === '') {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><div class="d-flex align-items-center justify-content-center bg-secondary text-white">Ungültige Video-URL.</div></div>';
                }
                return '<div class="' . nxb_core_h($wrapClass) . '"><iframe src="' . nxb_core_h($embed) . '" allowfullscreen loading="lazy" title="Video"></iframe></div>';

            case 'core_html':
                $code = trim((string)($settings['html'] ?? $settings['code'] ?? ''));
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 nx-html-block' . ($vis !== '' ? ' ' . $vis : '');
                if ($code === '') {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><div class="alert alert-warning small mb-0">HTML/Code in Einstellungen einfügen.</div></div>';
                }
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $code . '</div>';

            case 'core_card':
                $title = trim((string)($settings['title'] ?? ''));
                $text = trim((string)($settings['text'] ?? ''));
                $img = trim((string)($settings['image'] ?? ''));
                $btnLabel = trim((string)($settings['buttonLabel'] ?? ''));
                $btnUrl = trim((string)($settings['buttonUrl'] ?? '#'));
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $width = (string)($settings['width'] ?? 'medium');
                $widths = ['small' => '18rem', 'medium' => '24rem', 'large' => '32rem', 'full' => ''];
                $maxWidth = $widths[$width] ?? $widths['medium'];
                $imgRatio = (string)($settings['imageRatio'] ?? '');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 nx-card-wrapper ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                if ($maxWidth !== '' && ($align === 'center' || $align === 'end' || $align === 'right')) {
                    $wrapClass .= $align === 'center' ? ' mx-auto' : ' ms-auto';
                }
                $wrapStyle = $maxWidth !== '' ? ' max-width:' . nxb_core_h($maxWidth) . ';' : '';
                $html = '<div class="card">';
                if ($img !== '') {
                    $ratioValid = in_array($imgRatio, ['16:9', '4:3', '1:1'], true);
                    if ($ratioValid) {
                        $html .= '<div class="card-img-top nx-card-img-ratio ratio ratio-' . str_replace(':', 'x', nxb_core_h($imgRatio)) . ' overflow-hidden bg-secondary">';
                        $html .= '<img src="' . nxb_core_h($img) . '" alt="" loading="lazy" class="w-100 h-100 object-fit-cover" />';
                        $html .= '</div>';
                    } else {
                        $html .= '<img src="' . nxb_core_h($img) . '" class="card-img-top" alt="" loading="lazy" style="object-fit:cover;max-height:12rem;" />';
                    }
                }
                $html .= '<div class="card-body ' . nxb_core_h($alignClass) . '">';
                if ($title !== '') {
                    $html .= '<h3 class="card-title h5" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($title) . '</h3>';
                }
                $html .= '<div class="card-text" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . ($text !== '' ? nl2br(nxb_core_h($text)) : '<span class="text-muted">Text eingeben.</span>') . '</div>';
                if ($btnLabel !== '') {
                    $html .= '<a href="' . nxb_core_h($btnUrl) . '" class="btn btn-primary mt-2" data-nx-inline="btnLabel" title="Doppelklick zum Bearbeiten">' . nxb_core_h($btnLabel) . '</a>';
                }
                $html .= '</div></div>';
                return '<div class="' . nxb_core_h($wrapClass) . '"' . ($wrapStyle !== '' ? ' style="' . trim($wrapStyle) . '"' : '') . '>' . $html . '</div>';

            case 'core_button_group':
                $buttons = [];
                for ($i = 1; $i <= 4; $i++) {
                    $label = trim((string)($settings["btn{$i}_label"] ?? ''));
                    $url = trim((string)($settings["btn{$i}_url"] ?? '#'));
                    $style = trim((string)($settings["btn{$i}_style"] ?? 'outline-secondary'));
                    if ($label === '') {
                        continue;
                    }
                    $buttons[] = ['label' => $label, 'url' => $url, 'style' => $style];
                }
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'justify-content-center' : (($align === 'end' || $align === 'right') ? 'justify-content-end' : 'justify-content-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 d-flex flex-wrap gap-2 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                if (empty($buttons)) {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><span class="text-muted small">Button-Gruppe – Labels in Einstellungen anlegen.</span></div>';
                }
                $inner = '';
                foreach ($buttons as $i => $b) {
                    $field = 'btn' . ($i + 1) . '_label';
                    $inner .= '<a href="' . nxb_core_h($b['url']) . '" class="btn btn-' . nxb_core_h($b['style']) . '" data-nx-inline="' . nxb_core_h($field) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($b['label']) . '</a>';
                }
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $inner . '</div>';

            case 'core_breadcrumb':
                $items = [];
                for ($i = 1; $i <= 5; $i++) {
                    $label = trim((string)($settings["item{$i}_label"] ?? ''));
                    $url = trim((string)($settings["item{$i}_url"] ?? ''));
                    if ($label === '') {
                        continue;
                    }
                    $items[] = ['label' => $label, 'url' => $url];
                }
                $vis = nxb_visibility_class($settings);
                $cls = 'mb-3' . ($vis !== '' ? ' ' . $vis : '');
                if (empty($items)) {
                    return '<nav class="' . nxb_core_h($cls) . '" aria-label="Breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item text-muted">Breadcrumb in Einstellungen anlegen.</li></ol></nav>';
                }
                $html = '<nav aria-label="Breadcrumb"><ol class="breadcrumb">';
                foreach ($items as $idx => $it) {
                    $last = $idx === count($items) - 1;
                    $html .= '<li class="breadcrumb-item' . ($last ? ' active" aria-current="page">' : '"><a href="' . nxb_core_h($it['url']) . '">') . nxb_core_h($it['label']) . ($last ? '' : '</a>') . '</li>';
                }
                $html .= '</ol></nav>';
                return '<div class="' . nxb_core_h($cls) . '">' . $html . '</div>';

            case 'core_columns':
                $cols = (int)($settings['columns'] ?? 2);
                if ($cols < 2 || $cols > 6) {
                    $cols = 2;
                }
                $sectionId = (string)($settings['id'] ?? uniqid('sec_', false));
                $bg = trim((string)($settings['bg'] ?? ''));
                $pad = trim((string)($settings['padding'] ?? 'py-4'));
                $vis = nxb_visibility_class($settings);
                $rowCols = 'row-cols-1 row-cols-md-' . $cols;
                $outerClasses = trim('nx-columns ' . $pad . ' ' . $bg . ($vis !== '' ? ' ' . $vis : ''));
                $html = '<div class="' . nxb_core_h($outerClasses) . '"><div class="container"><div class="row g-4 ' . nxb_core_h($rowCols) . '">';
                for ($i = 1; $i <= $cols; $i++) {
                    $zoneName = 'sec_' . $sectionId . '_c' . $i;
                    $inner = '';
                    if (!empty($allRows[$zoneName]) && is_array($allRows[$zoneName])) {
                        foreach ($allRows[$zoneName] as $w) {
                            $widgetKey = (string)$w['widget_key'];
                            $instanceId = (string)$w['instance_id'];
                            $wSettings = is_array($w['settings'] ?? null) ? $w['settings'] : [];
                            $wTitle = (string)$w['title'];
                            if (function_exists('nxb_live_wrap') && function_exists('nxb_render_widget_content')) {
                                $inner .= nxb_live_wrap($w, nxb_render_widget_content($widgetKey, $instanceId, $wSettings, $wTitle));
                            } else {
                                $inner .= nxb_render_frontend_widget_html($widgetKey, $instanceId, $wSettings, $wTitle);
                            }
                        }
                    }
                    if ($inner === '' && function_exists('nxb_is_builder') && nxb_is_builder()) {
                        $inner = '<div class="nx-drop-hint">Hier Blöcke ablegen – Reihenfolge frei wählbar</div>';
                    }
                    $zoneClasses = 'nx-zone' . (function_exists('nxb_is_builder') && nxb_is_builder() ? ' nx-live-zone' : '');
                    $html .= '<div class="col ' . nxb_core_h($zoneClasses) . '" data-nx-zone="' . nxb_core_h($zoneName) . '">' . $inner . '</div>';
                }
                $html .= '</div></div></div>';
                return $html;

            case 'core_counter':
                $value = trim((string)($settings['value'] ?? '0'));
                $suffix = trim((string)($settings['suffix'] ?? ''));
                $label = trim((string)($settings['label'] ?? ''));
                $align = (string)($settings['align'] ?? 'center');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $html = '<div class="nx-counter"><span class="display-5 fw-bold text-primary">' . nxb_core_h($value) . nxb_core_h($suffix) . '</span>';
                if ($label !== '') {
                    $html .= '<p class="mb-0 small text-muted">' . nxb_core_h($label) . '</p>';
                }
                $html .= '</div>';
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $html . '</div>';

            case 'core_progress':
                $value = (int)($settings['value'] ?? 75);
                $value = max(0, min(100, $value));
                $label = trim((string)($settings['label'] ?? ''));
                $variant = (string)($settings['variant'] ?? 'primary');
                $striped = !empty($settings['striped']);
                $animated = !empty($settings['animated']);
                $vis = nxb_visibility_class($settings);
                $cls = 'mb-3' . ($vis !== '' ? ' ' . $vis : '');
                $barClass = 'progress-bar' . ($striped ? ' progress-bar-striped' : '') . ($animated ? ' progress-bar-animated' : '');
                $html = '<div class="progress" role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100">';
                $html .= '<div class="' . nxb_core_h($barClass) . ' bg-' . nxb_core_h($variant) . '" style="width:' . $value . '%">' . ($label !== '' ? nxb_core_h($label) : '') . '</div></div>';
                return '<div class="' . nxb_core_h($cls) . '">' . $html . '</div>';

            case 'core_logo_row':
                $images = [];
                for ($i = 1; $i <= 6; $i++) {
                    $src = trim((string)($settings["item{$i}_src"] ?? ''));
                    if ($src === '') {
                        continue;
                    }
                    $alt = trim((string)($settings["item{$i}_alt"] ?? ''));
                    $url = trim((string)($settings["item{$i}_url"] ?? ''));
                    $images[] = ['src' => $src, 'alt' => $alt, 'url' => $url];
                }
                $align = (string)($settings['align'] ?? 'center');
                $alignClass = ($align === 'center') ? 'justify-content-center' : (($align === 'end' || $align === 'right') ? 'justify-content-end' : 'justify-content-start');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'd-flex flex-wrap align-items-center gap-4 mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                if (empty($images)) {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><span class="text-muted small">Logo-Leiste – Bilder in Einstellungen angeben.</span></div>';
                }
                $inner = '';
                foreach ($images as $img) {
                    $tag = '<img src="' . nxb_core_h($img['src']) . '" alt="' . nxb_core_h($img['alt']) . '" class="img-fluid" style="max-height:48px;width:auto;object-fit:contain;" loading="lazy" />';
                    $inner .= $img['url'] !== '' ? '<a href="' . nxb_core_h($img['url']) . '" class="text-decoration-none">' . $tag . '</a>' : $tag;
                }
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $inner . '</div>';

            case 'core_social_links':
                $links = [];
                $platforms = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'github', 'xing'];
                foreach ($platforms as $p) {
                    $url = trim((string)($settings[$p] ?? ''));
                    if ($url !== '') {
                        $links[] = ['platform' => $p, 'url' => $url];
                    }
                }
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'justify-content-center' : (($align === 'end' || $align === 'right') ? 'justify-content-end' : 'justify-content-start');
                $size = (string)($settings['size'] ?? 'fs-4');
                $vis = nxb_visibility_class($settings);
                $wrapClass = 'd-flex flex-wrap gap-2 mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $icons = ['facebook' => 'bi-facebook', 'twitter' => 'bi-twitter-x', 'instagram' => 'bi-instagram', 'youtube' => 'bi-youtube', 'linkedin' => 'bi-linkedin', 'github' => 'bi-github', 'xing' => 'bi-xing'];
                if (empty($links)) {
                    return '<div class="' . nxb_core_h($wrapClass) . '"><span class="text-muted small">Social-Links – URLs in Einstellungen angeben.</span></div>';
                }
                $inner = '';
                foreach ($links as $l) {
                    $icon = $icons[$l['platform']] ?? 'bi-link-45deg';
                    $inner .= '<a href="' . nxb_core_h($l['url']) . '" class="text-decoration-none ' . nxb_core_h($size) . '" target="_blank" rel="noopener noreferrer" aria-label="' . nxb_core_h($l['platform']) . '"><i class="bi ' . $icon . '"></i></a>';
                }
                return '<div class="' . nxb_core_h($wrapClass) . '">' . $inner . '</div>';

            case 'core_footer_simple':
                // Minimaler Footer - Brand + horizontale Links + Copyright
                $brand = trim((string)($settings['brand'] ?? 'Nexpell'));
                $year  = trim((string)($settings['year'] ?? date('Y')));
                $nav1  = trim((string)($settings['nav1'] ?? 'Product'));
                $nav2  = trim((string)($settings['nav2'] ?? 'Features'));
                $nav3  = trim((string)($settings['nav3'] ?? 'Pricing'));
                $nav4  = trim((string)($settings['nav4'] ?? 'Resources'));
                $nav5  = trim((string)($settings['nav5'] ?? 'Careers'));
                $nav6  = trim((string)($settings['nav6'] ?? 'Help'));
                $nav7  = trim((string)($settings['nav7'] ?? 'Privacy'));
                $vis   = nxb_visibility_class($settings);
                $outerClasses = 'nx-footer bg-light border-top py-4' . ($vis !== '' ? ' ' . $vis : '');
                // Im Builder immer zentriert wie im Frontend: Container-Layout mit fixer Breite
                $containerClass = 'container nx-keep-container';
                $html  = '<footer class="' . nxb_core_h($outerClasses) . '"><div class="' . nxb_core_h($containerClass) . '">';
                $html .= '<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 small">';
                $html .= '  <div class="d-flex align-items-center gap-2">';
                $html .= '    <span class="fw-semibold" data-nx-inline="brand" title="Doppelklick zum Bearbeiten">' . nxb_core_h($brand) . '</span>';
                $html .= '  </div>';
                $html .= '  <ul class="nav justify-content-center flex-wrap gap-2 mb-0">';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav1')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav1" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav1) . '</a></li>';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav2')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav2" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav2) . '</a></li>';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav3')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav3" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav3) . '</a></li>';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav4')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav4" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav4) . '</a></li>';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav5')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav5" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav5) . '</a></li>';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav6')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav6" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav6) . '</a></li>';
                $html .= '    <li class="nav-item"><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'nav7')) . '" class="nav-link px-2 nx-footer-link nx-footer-muted" data-nx-inline="nav7" title="Doppelklick zum Bearbeiten">' . nxb_core_h($nav7) . '</a></li>';
                $html .= '  </ul>';
                $html .= '  <div class="nx-footer-muted">';
                $html .= '    © <span data-nx-inline="year" title="Doppelklick zum Bearbeiten">' . nxb_core_h($year) . '</span>';
                $html .= '  </div>';
                $html .= '</div></div></footer>';
                return $html;

            case 'core_footer_3col':
                // Großer Multi-Column-Footer - About + Link-Spalten
                $brand   = trim((string)($settings['brand'] ?? 'Nexpell'));
                $about   = trim((string)($settings['about'] ?? 'Launch your own Software as a Service Application with Nexpell Solutions.'));
                $aboutTitle = trim((string)($settings['about_title'] ?? 'About Us'));
                $helpTitle  = trim((string)($settings['help_title'] ?? 'Help Center'));
                $about1 = trim((string)($settings['about1'] ?? 'Terms & Condition'));
                $about2 = trim((string)($settings['about2'] ?? 'Privacy Policy'));
                $about3 = trim((string)($settings['about3'] ?? 'Support'));
                $about4 = trim((string)($settings['about4'] ?? 'Press'));
                $help1  = trim((string)($settings['help1'] ?? 'General Questions'));
                $help2  = trim((string)($settings['help2'] ?? 'FAQs'));
                $help3  = trim((string)($settings['help3'] ?? 'Accounting'));
                $help4  = trim((string)($settings['help4'] ?? 'Billing'));
                $vis = nxb_visibility_class($settings);
                $outerClasses = 'nx-footer bg-light border-top py-5' . ($vis !== '' ? ' ' . $vis : '');
                $containerClass = 'container nx-keep-container';
                $html  = '<footer class="' . nxb_core_h($outerClasses) . '"><div class="' . nxb_core_h($containerClass) . '">';
                $html .= '<div class="row g-4 small">';
                // Brand + About
                $html .= '<div class="col-12 col-md-3">';
                $html .= '<div class="d-flex align-items-center gap-2 mb-2">';
                $html .= '<span class="fw-semibold" data-nx-inline="brand" title="Doppelklick zum Bearbeiten">' . nxb_core_h($brand) . '</span>';
                $html .= '</div>';
                $html .= '<p class="mb-0 nx-footer-muted" data-nx-inline="about" title="Doppelklick zum Bearbeiten">' . nxb_core_h($about) . '</p>';
                $html .= '</div>';
                // About Us links
                $html .= '<div class="col-6 col-md-3">';
                $html .= '<h6 class="fw-semibold mb-2" data-nx-inline="about_title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($aboutTitle) . '</h6>';
                $html .= '<ul class="list-unstyled mb-0">';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'about1')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="about1" title="Doppelklick zum Bearbeiten">' . nxb_core_h($about1) . '</a></li>';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'about2')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="about2" title="Doppelklick zum Bearbeiten">' . nxb_core_h($about2) . '</a></li>';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'about3')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="about3" title="Doppelklick zum Bearbeiten">' . nxb_core_h($about3) . '</a></li>';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'about4')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="about4" title="Doppelklick zum Bearbeiten">' . nxb_core_h($about4) . '</a></li>';
                $html .= '</ul>';
                $html .= '</div>';
                // Help Center links
                $html .= '<div class="col-6 col-md-3">';
                $html .= '<h6 class="fw-semibold mb-2" data-nx-inline="help_title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($helpTitle) . '</h6>';
                $html .= '<ul class="list-unstyled mb-0">';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'help1')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="help1" title="Doppelklick zum Bearbeiten">' . nxb_core_h($help1) . '</a></li>';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'help2')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="help2" title="Doppelklick zum Bearbeiten">' . nxb_core_h($help2) . '</a></li>';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'help3')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="help3" title="Doppelklick zum Bearbeiten">' . nxb_core_h($help3) . '</a></li>';
                $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, 'help4')) . '" class="text-decoration-none nx-footer-link nx-footer-muted d-inline-block mb-1" data-nx-inline="help4" title="Doppelklick zum Bearbeiten">' . nxb_core_h($help4) . '</a></li>';
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</div></div></footer>';
                return $html;

            case 'core_footer_2col':
                // Großer, heller Footer Brand + 4 Link-Spalten + Social + Copyright
                $brand   = trim((string)($settings['brand'] ?? 'Nexpell'));
                $about   = trim((string)($settings['about'] ?? 'Nexpell ist ein modularer Website-Builder mit modernen Bootstrap 5 Komponenten.'));
                $year    = trim((string)($settings['year'] ?? date('Y')));
                // Spaltentitel
                $platformTitle  = trim((string)($settings['platform_title'] ?? 'Platform'));
                $resourcesTitle = trim((string)($settings['resources_title'] ?? 'Resources'));
                $companyTitle   = trim((string)($settings['company_title'] ?? 'Company'));
                $supportTitle   = trim((string)($settings['support_title'] ?? 'Support'));
                // Platform-Links
                $platform1 = trim((string)($settings['platform1'] ?? 'Browse Templates'));
                $platform2 = trim((string)($settings['platform2'] ?? 'Live-Builder'));
                $platform3 = trim((string)($settings['platform3'] ?? 'Plugin Store'));
                $platform4 = trim((string)($settings['platform4'] ?? 'Changelog'));
                // Resources-Links
                $resources1 = trim((string)($settings['resources1'] ?? 'Docs'));
                $resources2 = trim((string)($settings['resources2'] ?? 'Tutorials'));
                $resources3 = trim((string)($settings['resources3'] ?? 'Blog'));
                $resources4 = trim((string)($settings['resources4'] ?? 'Templates'));
                $resources5 = trim((string)($settings['resources5'] ?? 'Community'));
                // Company-Links
                $company1 = trim((string)($settings['company1'] ?? 'About'));
                $company2 = trim((string)($settings['company2'] ?? 'Pricing'));
                $company3 = trim((string)($settings['company3'] ?? 'Careers'));
                $company4 = trim((string)($settings['company4'] ?? 'Contact'));
                // Support-Links
                $support1 = trim((string)($settings['support1'] ?? 'FAQ'));
                $support2 = trim((string)($settings['support2'] ?? 'Help Center'));
                $support3 = trim((string)($settings['support3'] ?? 'Status'));
                $support4 = trim((string)($settings['support4'] ?? 'Report an issue'));
                // Copyright/Policy
                $copyrightText = trim((string)($settings['copyright_text'] ?? 'Nexpell. All Rights Reserved.'));
                $policyPrivacy = trim((string)($settings['policy_privacy'] ?? 'Privacy Policy'));
                $policyCookies = trim((string)($settings['policy_cookies'] ?? 'Cookie Notice'));
                $policyTerms   = trim((string)($settings['policy_terms'] ?? 'Terms of Use'));

                $vis = nxb_visibility_class($settings);
                // Einheitlich helles Design für alle Footer-Widgets (bg-light)
                $outerClasses = 'nx-footer bg-light pt-5 pb-3' . ($vis !== '' ? ' ' . $vis : '');
                $containerClass = 'container nx-keep-container';
                $html  = '<footer class="' . nxb_core_h($outerClasses) . '"><div class="' . nxb_core_h($containerClass) . '">';
                $html .= '<div class="row gy-4 small">';
                // Brand + About + Social
                $html .= '<div class="col-lg-4 col-md-6 col-12">';
                $html .= '  <div class="d-flex flex-column gap-3">';
                $html .= '    <div class="fw-semibold fs-5" data-nx-inline="brand" title="Doppelklick zum Bearbeiten">' . nxb_core_h($brand) . '</div>';
                $html .= '    <p class="mb-0 nx-footer-muted" data-nx-inline="about" title="Doppelklick zum Bearbeiten">' . nxb_core_h($about) . '</p>';
                $html .= '    <div class="fs-4 d-flex flex-row gap-3">';
                $html .= nxb_render_core_widget_html('core_social_links', $settings, 'Social');
                $html .= '    </div>';
                $html .= '  </div>';
                $html .= '</div>';
                // Platform
                $html .= '<div class="col-lg-2 col-md-3 col-6">';
                $html .= '  <div class="d-flex flex-column gap-2">';
                $html .= '    <h6 class="fw-semibold text-uppercase mb-0" data-nx-inline="platform_title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($platformTitle) . '</h6>';
                $html .= '    <ul class="list-unstyled nav nav-footer flex-column nav-x-0">';
                foreach (['platform1', 'platform2', 'platform3', 'platform4'] as $field) {
                    $val = ${$field};
                    if ($val === '') {
                        continue;
                    }
                    $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, $field)) . '" class="nav-link px-0 nx-footer-link nx-footer-muted" data-nx-inline="' . nxb_core_h($field) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($val) . '</a></li>';
                }
                $html .= '    </ul>';
                $html .= '  </div>';
                $html .= '</div>';
                // Resources
                $html .= '<div class="col-lg-2 col-md-3 col-6">';
                $html .= '  <div class="d-flex flex-column gap-2">';
                $html .= '    <h6 class="fw-semibold text-uppercase mb-0" data-nx-inline="resources_title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($resourcesTitle) . '</h6>';
                $html .= '    <ul class="list-unstyled nav nav-footer flex-column nav-x-0">';
                foreach (['resources1', 'resources2', 'resources3', 'resources4', 'resources5'] as $field) {
                    $val = ${$field};
                    if ($val === '') {
                        continue;
                    }
                    $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, $field)) . '" class="nav-link px-0 nx-footer-link nx-footer-muted" data-nx-inline="' . nxb_core_h($field) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($val) . '</a></li>';
                }
                $html .= '    </ul>';
                $html .= '  </div>';
                $html .= '</div>';
                // Company
                $html .= '<div class="col-lg-2 col-md-3 col-6">';
                $html .= '  <div class="d-flex flex-column gap-2">';
                $html .= '    <h6 class="fw-semibold text-uppercase mb-0" data-nx-inline="company_title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($companyTitle) . '</h6>';
                $html .= '    <ul class="list-unstyled nav nav-footer flex-column nav-x-0">';
                foreach (['company1', 'company2', 'company3', 'company4'] as $field) {
                    $val = ${$field};
                    if ($val === '') {
                        continue;
                    }
                    $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, $field)) . '" class="nav-link px-0 nx-footer-link nx-footer-muted" data-nx-inline="' . nxb_core_h($field) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($val) . '</a></li>';
                }
                $html .= '    </ul>';
                $html .= '  </div>';
                $html .= '</div>';
                // Support
                $html .= '<div class="col-lg-2 col-md-3 col-6">';
                $html .= '  <div class="d-flex flex-column gap-2">';
                $html .= '    <h6 class="fw-semibold text-uppercase mb-0" data-nx-inline="support_title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($supportTitle) . '</h6>';
                $html .= '    <ul class="list-unstyled nav nav-footer flex-column nav-x-0">';
                foreach (['support1', 'support2', 'support3', 'support4'] as $field) {
                    $val = ${$field};
                    if ($val === '') {
                        continue;
                    }
                    $html .= '<li><a href="' . nxb_core_h(nxb_footer_link_href($settings, $field)) . '" class="nav-link px-0 nx-footer-link nx-footer-muted" data-nx-inline="' . nxb_core_h($field) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($val) . '</a></li>';
                }
                $html .= '    </ul>';
                $html .= '  </div>';
                $html .= '</div>';
                $html .= '</div>'; // row

                // Copyright-Zeile
                $html .= '<div class="row align-items-center g-0 border-top mt-5 pt-3 small">';
                $html .= '  <div class="col-md-6 col-12 text-center text-md-start mb-2 mb-md-0">';
                $html .= '    <span>© <span data-nx-inline="year" title="Doppelklick zum Bearbeiten">' . nxb_core_h($year) . '</span> <span data-nx-inline="copyright_text" title="Doppelklick zum Bearbeiten">' . nxb_core_h($copyrightText) . '</span></span>';
                $html .= '  </div>';
                $html .= '  <div class="col-md-6 col-12 d-flex justify-content-center justify-content-md-end">';
                $html .= '    <nav class="nav nav-footer">';
                $html .= '      <a href="' . nxb_core_h(nxb_footer_link_href($settings, 'policy_privacy')) . '" class="nav-link ps-0 nx-footer-link nx-footer-muted" data-nx-inline="policy_privacy" title="Doppelklick zum Bearbeiten">' . nxb_core_h($policyPrivacy) . '</a>';
                $html .= '      <a href="' . nxb_core_h(nxb_footer_link_href($settings, 'policy_cookies')) . '" class="nav-link px-2 px-md-3 nx-footer-link nx-footer-muted" data-nx-inline="policy_cookies" title="Doppelklick zum Bearbeiten">' . nxb_core_h($policyCookies) . '</a>';
                $html .= '      <a href="' . nxb_core_h(nxb_footer_link_href($settings, 'policy_terms')) . '" class="nav-link nx-footer-link nx-footer-muted" data-nx-inline="policy_terms" title="Doppelklick zum Bearbeiten">' . nxb_core_h($policyTerms) . '</a>';
                $html .= '    </nav>';
                $html .= '  </div>';
                $html .= '</div>'; // row copyright

                $html .= '</div></footer>';
                return $html;

            case 'core_footer_centered':
                // Zentrierter Footer – Brand + Beschreibung + zentrierte Links + Copyright/Policies
                $brand = trim((string)($settings['brand'] ?? 'Nexpell'));
                $description = trim((string)($settings['description'] ?? 'Nexpell ist ein moderner Website-Builder mit durchdachten Bootstrap 5 Komponenten.'));
                $year  = trim((string)($settings['year'] ?? date('Y')));
                $nav1  = trim((string)($settings['nav1'] ?? 'Über uns'));
                $nav2  = trim((string)($settings['nav2'] ?? 'Karriere'));
                $nav3  = trim((string)($settings['nav3'] ?? 'Kontakt'));
                $nav4  = trim((string)($settings['nav4'] ?? 'Preise'));
                $nav5  = trim((string)($settings['nav5'] ?? 'Blog'));
                $nav6  = trim((string)($settings['nav6'] ?? 'Partner'));
                $nav7  = trim((string)($settings['nav7'] ?? 'Hilfe'));
                $nav8  = trim((string)($settings['nav8'] ?? 'Investoren'));
                $policyPrivacy = trim((string)($settings['policy_privacy'] ?? 'Datenschutzerklärung'));
                $policyCookies = trim((string)($settings['policy_cookies'] ?? 'Cookie-Hinweis'));
                $policyTerms   = trim((string)($settings['policy_terms'] ?? 'Nutzungsbedingungen'));
                $copyrightText = trim((string)($settings['copyright_text'] ?? 'Nexpell. Alle Rechte vorbehalten.'));
                $vis = nxb_visibility_class($settings);
                // Einheitlich helles Design für alle Footer-Widgets (bg-light)
                $outerClasses = 'nx-footer bg-light pt-5 pb-3' . ($vis !== '' ? ' ' . $vis : '');
                $containerClass = 'container nx-keep-container';

                $html  = '<footer class="' . nxb_core_h($outerClasses) . '"><div class="' . nxb_core_h($containerClass) . '">';
                $html .= '<div class="row justify-content-center text-center align-items-center">';
                $html .= '  <div class="col-12 col-md-12 col-xxl-6 px-0">';
                $html .= '    <div class="mb-4">';
                $html .= '      <div class="mb-3 fw-semibold fs-4" data-nx-inline="brand" title="Doppelklick zum Bearbeiten">' . nxb_core_h($brand) . '</div>';
                $html .= '      <p class="lead mb-0 nx-footer-muted" data-nx-inline="description" title="Doppelklick zum Bearbeiten">' . nxb_core_h($description) . '</p>';
                $html .= '    </div>';
                $html .= '    <nav class="nav nav-footer justify-content-center flex-wrap">';
                $links = [
                    'nav1' => $nav1,
                    'nav2' => $nav2,
                    'nav3' => $nav3,
                    'nav4' => $nav4,
                    'nav5' => $nav5,
                    'nav6' => $nav6,
                    'nav7' => $nav7,
                    'nav8' => $nav8,
                ];
                $i = 0;
                foreach ($links as $field => $label) {
                    if ($label === '') {
                        continue;
                    }
                    if ($i > 0) {
                        $html .= '<span class="my-2 vr opacity-50"></span>';
                    }
                    $html .= '<a class="nav-link nx-footer-link" href="' . nxb_core_h(nxb_footer_link_href($settings, $field)) . '" data-nx-inline="' . nxb_core_h($field) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($label) . '</a>';
                    $i++;
                }
                $html .= '    </nav>';
                $html .= '  </div>';
                $html .= '</div>';
                $html .= '<hr class="mt-5 mb-3">';
                $html .= '<div class="row align-items-center small">';
                $html .= '  <div class="col-lg-3 col-md-6 col-12 text-center text-md-start mb-2 mb-md-0">';
                $html .= '    <span data-nx-inline="copyright_text" title="Doppelklick zum Bearbeiten">' . nxb_core_h($copyrightText) . '</span></span>';
                $html .= '  </div>';
                $html .= '  <div class="col-12 col-md-6 col-lg-7 d-lg-flex justify-content-center">';
                $html .= '    <nav class="nav nav-footer">';
                $html .= '      <a class="nav-link ps-0 nx-footer-link" href="' . nxb_core_h(nxb_footer_link_href($settings, 'policy_privacy')) . '" data-nx-inline="policy_privacy" title="Doppelklick zum Bearbeiten">' . nxb_core_h($policyPrivacy) . '</a>';
                $html .= '      <a class="nav-link px-2 px-md-0 nx-footer-link" href="' . nxb_core_h(nxb_footer_link_href($settings, 'policy_cookies')) . '" data-nx-inline="policy_cookies" title="Doppelklick zum Bearbeiten">' . nxb_core_h($policyCookies) . '</a>';
                $html .= '      <a class="nav-link nx-footer-link" href="' . nxb_core_h(nxb_footer_link_href($settings, 'policy_terms')) . '" data-nx-inline="policy_terms" title="Doppelklick zum Bearbeiten">' . nxb_core_h($policyTerms) . '</a>';
                $html .= '    </nav>';
                $html .= '  </div>';
                $html .= '</div>';
                $html .= '</div></footer>';
                return $html;

            case 'core_nav_demo':
                // Demo-Navigation für Template-Mode (Brand als Text oder Logo)
                $layout      = (string)($settings['layout'] ?? 'simple'); // simple|dropdown|centered
                $brandTitle  = trim((string)($settings['title'] ?? 'Nexpell'));
                if ($brandTitle === '') {
                    $brandTitle = 'Nexpell';
                }
                $brandImage  = trim((string)($settings['image'] ?? ''));
                $scheme      = (string)($settings['scheme'] ?? 'light'); // light|dark
                $shadow      = (string)($settings['shadow'] ?? 'shadow-sm'); // '', shadow-sm, shadow, shadow-lg
                $navVariant  = (string)($settings['navVariant'] ?? 'standard'); // standard|sticky|agency
                if (!in_array($navVariant, ['standard', 'sticky', 'agency'], true)) {
                    $navVariant = 'standard';
                }
                $navBgColor  = trim((string)($settings['navBgColor'] ?? ''));
                $navTextColor = trim((string)($settings['navTextColor'] ?? ''));
                $navHoverColor = trim((string)($settings['navHoverColor'] ?? ''));
                $navFillBgColor = trim((string)($settings['navFillBgColor'] ?? ''));
                $navFillTextColor = trim((string)($settings['navFillTextColor'] ?? ''));
                $overlayMode = !empty($settings['overlayMode']);
                $overlayTextMode = (string)($settings['overlayTextMode'] ?? 'light'); // light|dark
                $scrollFill  = !empty($settings['scrollFill']);
                $scrollFillOffset = isset($settings['scrollFillOffset']) ? (int)$settings['scrollFillOffset'] : 80;
                if ($scrollFillOffset < 0) $scrollFillOffset = 0;
                $filledShadow = (string)($settings['filledShadow'] ?? $shadow);
                if ($navVariant === 'sticky') {
                    $overlayMode = false;
                    $scrollFill = false;
                } elseif ($navVariant === 'agency') {
                    $overlayMode = true;
                    $scrollFill = true;
                    if ($filledShadow === '') {
                        $filledShadow = 'shadow-sm';
                    }
                }
                $container   = (string)($settings['container'] ?? 'fluid'); // fluid|fixed
                $paddingY    = trim((string)($settings['paddingY'] ?? ''));
                $paddingX    = trim((string)($settings['paddingX'] ?? ''));
                $hoverEffect = (string)($settings['hoverEffect'] ?? 'none'); // none|default|center|swipe

                $navClasses = ['navbar', 'navbar-expand-lg'];
                if ($overlayMode) {
                    $navClasses[] = ($overlayTextMode === 'dark') ? 'navbar-light bg-transparent' : 'navbar-dark bg-transparent';
                } else {
                    if ($scheme === 'dark') {
                        $navClasses[] = 'navbar-dark bg-dark';
                    } else {
                        // Keine zusätzliche Border-Bottom-Linie – die Hover/Underline-Effekte übernehmen die Betonung
                        $navClasses[] = 'navbar-light bg-white';
                    }
                    if ($shadow !== '') {
                        $navClasses[] = $shadow;
                    }
                }
                // Marker-Klasse für Builder-CSS: fixed vs. fluid Container
                $navClasses[] = $container === 'fixed' ? 'nx-nav-fixed' : 'nx-nav-fluid';
                $navClasses[] = 'nx-nav-variant-' . $navVariant;
                if ($overlayMode) {
                    $navClasses[] = 'nx-nav-overlay';
                } elseif ($navVariant === 'sticky') {
                    $navClasses[] = 'sticky-top';
                }
                // Standardmäßig kein vertikales Padding auf der Navbar selbst (Padding kommt von den Nav-Items)
                $navClasses[] = 'py-0';
                $vis = nxb_visibility_class($settings);
                if ($vis !== '') {
                    $navClasses[] = $vis;
                }
                $navClass = implode(' ', $navClasses);
                $noShadow = ($shadow === '');
                $navStyleParts = [];
                if ($noShadow) {
                    $navStyleParts[] = 'box-shadow:none;';
                }
                if ($navBgColor !== '') {
                    $navStyleParts[] = '--nx-demo-nav-bg:' . nxb_core_h($navBgColor) . ';';
                }
                if ($navTextColor !== '') {
                    $navStyleParts[] = '--nx-demo-nav-text:' . nxb_core_h($navTextColor) . ';';
                }
                if ($navHoverColor !== '') {
                    $navStyleParts[] = '--nx-demo-nav-hover:' . nxb_core_h($navHoverColor) . ';';
                }
                if (($navFillBgColor !== '' ? $navFillBgColor : $navBgColor) !== '') {
                    $navStyleParts[] = '--nx-demo-nav-fill-bg:' . nxb_core_h($navFillBgColor !== '' ? $navFillBgColor : $navBgColor) . ';';
                }
                if (($navFillTextColor !== '' ? $navFillTextColor : $navTextColor) !== '') {
                    $navStyleParts[] = '--nx-demo-nav-fill-text:' . nxb_core_h($navFillTextColor !== '' ? $navFillTextColor : $navTextColor) . ';';
                }
                $navStyle = empty($navStyleParts) ? '' : ' style="' . implode('', $navStyleParts) . '"';
                $containerClass = $container === 'fixed' ? 'container nx-keep-container' : 'container-fluid nx-keep-container';

                $isBuilder = (function_exists('nxb_is_builder') && nxb_is_builder()) || !empty($GLOBALS['nxb_ajax_builder']);
                $navId = 'nx-nav-demo-' . substr(md5(json_encode($settings)), 0, 8);

                // Neues Menü-Modell: settings.menu = [{label,url,children:[{label,url}]}]
                // Wenn noch kein eigenes Menü im Widget gepflegt ist, nutze die bestehende Website-Navigation.
                $menuSourceIsPlugin = false;
                $menu = $settings['menu'] ?? null;
                if (!is_array($menu)) {
                    $menu = null;
                }
                if (($menu === null || empty($menu)) && function_exists('nxb_get_plugin_navigation_menu')) {
                    $fallbackMenu = nxb_get_plugin_navigation_menu();
                    if (!empty($fallbackMenu)) {
                        $menu = $fallbackMenu;
                        $menuSourceIsPlugin = true;
                    }
                }
                // Brand: entweder Text (inline-editierbar) oder Logo-Bild (inline austauschbar)
                if ($brandImage !== '') {
                    $brandHtml = '<a class="navbar-brand fw-semibold" href="#"><img src="' . nxb_core_h($brandImage) . '" alt="' . nxb_core_h($brandTitle) . '" style="max-height:calc(70px);height:70px;" class="d-inline-block align-text-bottom" data-nx-inline="image" title="Klick: Logo ändern"></a>';
                } else {
                    $brandHtml = '<a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="#">';
                    $brandHtml .= '<span data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($brandTitle) . '</span>';
                    if ($isBuilder) {
                        $brandHtml .= '<span class="text-muted small border rounded px-2 py-1" data-nx-inline="image" title="Klick: Logo hinzufügen" style="cursor:pointer;">Logo</span>';
                    }
                    $brandHtml .= '</a>';
                }

                // Padding: wird auf die Links (.nav-link) gelegt, nicht auf Navbar selbst
                $linkPaddingStyle = '';
                $padParts = [];
                if ($paddingY !== '') {
                    $padParts[] = 'padding-top:' . nxb_core_h($paddingY) . ';padding-bottom:' . nxb_core_h($paddingY) . ';';
                }
                if ($paddingX !== '') {
                    $padParts[] = 'padding-left:' . nxb_core_h($paddingX) . ';padding-right:' . nxb_core_h($paddingX) . ';';
                }
                if (!empty($padParts)) {
                    $linkPaddingStyle = ' style="' . implode('', $padParts) . '"';
                }

                // Hover-Effekt-Klasse für Links
                $hoverClass = '';
                if ($hoverEffect === 'default') {
                    $hoverClass = ' nx-nav-effect-default';
                } elseif ($hoverEffect === 'center') {
                    $hoverClass = ' nx-nav-effect-center';
                } elseif ($hoverEffect === 'swipe') {
                    $hoverClass = ' nx-nav-effect-swipe';
                } // none => keine zusätzliche Klasse

                // Immer einheitliche 2px-Unterstreichung + kein Fokus-Ring.
                // WICHTIG: Bootstrap nutzt ::after auf .dropdown-toggle für den Caret. Daher Unterstreichung hier über ::before.
                $navLinkStyle = '<style>'
                    . '.nx-nav-core-demo .navbar-nav .nav-link:focus,.nx-nav-core-demo .navbar-nav .nav-link.dropdown-toggle:focus{outline:none !important;box-shadow:none !important;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.dropdown-toggle::after{display:none !important;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link{position:relative;}'
                    . '.nx-nav-core-demo{background-color:var(--nx-demo-nav-bg, var(--bs-body-bg, #ffffff)) !important;color:var(--nx-demo-nav-text, var(--bs-body-color, #212529)) !important;}'
                    . '.nx-nav-core-demo .navbar-brand,.nx-nav-core-demo .navbar-nav .nav-link,.nx-nav-core-demo .navbar-toggler{color:var(--nx-demo-nav-text, var(--bs-body-color, #212529)) !important;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link:hover,.nx-nav-core-demo .navbar-nav .nav-link:focus,.nx-nav-core-demo .nav-item.dropdown.show > .nav-link,.nx-nav-core-demo .nav-item.dropdown:hover > .nav-link,.nx-nav-core-demo .navbar-brand:hover,.nx-nav-core-demo .navbar-brand:focus{color:var(--nx-demo-nav-hover, var(--bs-primary)) !important;}'
                    . '.nx-nav-core-demo .dropdown-menu{background:var(--nx-demo-nav-bg, var(--bs-body-bg, #ffffff)) !important;}'
                    . '.nx-nav-core-demo .dropdown-item{color:var(--nx-demo-nav-text, var(--bs-body-color, #212529)) !important;}'
                    . '.nx-nav-core-demo .dropdown-item:hover,.nx-nav-core-demo .dropdown-item:focus{color:var(--nx-demo-nav-hover, var(--bs-primary)) !important;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-default::before,'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-center::before,'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-swipe::before{content:"";position:absolute;left:0;right:0;bottom:0;height:2px !important;min-height:2px !important;max-height:2px !important;background-color:var(--bs-primary);transform:scaleX(0);transform-origin:left center;transition:transform .25s ease-out;pointer-events:none;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-default:hover::before,.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-default:focus::before,.nx-nav-core-demo .nav-item.dropdown.show > .nav-link.nx-nav-effect-default::before,.nx-nav-core-demo .nav-item.dropdown:hover > .nav-link.nx-nav-effect-default::before{transform:scaleX(1);}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-center::before{left:50%;right:auto;width:100%;transform:translateX(-50%) scaleX(0);transform-origin:center center;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-center:hover::before,.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-center:focus::before,.nx-nav-core-demo .nav-item.dropdown.show > .nav-link.nx-nav-effect-center::before,.nx-nav-core-demo .nav-item.dropdown:hover > .nav-link.nx-nav-effect-center::before{transform:translateX(-50%) scaleX(1);}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-swipe::before{transform:scaleX(0);transform-origin:left center;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-swipe:hover::before,.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-swipe:focus::before,.nx-nav-core-demo .nav-item.dropdown.show > .nav-link.nx-nav-effect-swipe::before,.nx-nav-core-demo .nav-item.dropdown:hover > .nav-link.nx-nav-effect-swipe::before{transform:scaleX(1);transform-origin:left center;}'
                    . '.nx-nav-core-demo .navbar-nav .nav-link.nx-nav-effect-swipe:not(:hover):not(:focus)::before{transform:scaleX(0);transform-origin:right center;}'
                    . '</style>';

                // Layout-Varianten
                $navDataAttrs = '';
                $navDataAttrs .= ' data-nx-nav-variant="' . nxb_core_h($navVariant) . '"';
                if ($overlayMode) $navDataAttrs .= ' data-nx-overlay="1" data-nx-overlay-text="' . nxb_core_h($overlayTextMode) . '" data-nx-fill-scheme="' . nxb_core_h($scheme) . '"';
                if ($scrollFill) $navDataAttrs .= ' data-nx-scrollfill="1" data-nx-scrollfill-offset="' . (int)$scrollFillOffset . '"';
                if ($filledShadow !== '') $navDataAttrs .= ' data-nx-filled-shadow="' . nxb_core_h($filledShadow) . '"';

                if ($layout === 'centered') {
                    $item1 = trim((string)($settings['item1_label'] ?? 'Produkt'));
                    $item2 = trim((string)($settings['item2_label'] ?? 'Features'));
                    $item3 = trim((string)($settings['item3_label'] ?? 'Preise'));
                    $item4 = trim((string)($settings['item4_label'] ?? 'Kontakt'));

                    // Zentrierte Variante: Nav-Hintergrund über volle Breite, Inhalt in Container + innerem Flex-Wrapper mittig
                    $innerClass = 'd-flex justify-content-center';

                    $html = $navLinkStyle . '<nav class="' . nxb_core_h($navClass . ' nx-nav-core-demo') . '"' . $navStyle . $navDataAttrs . '>';
                    $html .= '<div class="' . nxb_core_h($containerClass) . '">';
                    $html .= '<div class="' . nxb_core_h($innerClass) . '">';
                    $html .= str_replace('navbar-brand fw-semibold', 'navbar-brand fw-semibold me-4', $brandHtml);
                    $html .= '<ul class="navbar-nav flex-row gap-3 mb-0">';

                    if ($menu !== null && !empty($menu)) {
                        // Hover- und Klick-Dropdown im Frontend sichtbar (auch bei zentriertem Layout)
                        $html = str_replace('class="' . nxb_core_h($navClass . ' nx-nav-core-demo') . '"', 'class="' . nxb_core_h($navClass . ' nx-nav-core-demo nx-nav-hover') . '"', $html);
                        $html = '<style>.nx-nav-hover .dropdown:hover .dropdown-menu,.nx-nav-hover .dropdown.show .dropdown-menu{display:block !important;}</style>' . $html;
                        foreach ($menu as $i => $m) {
                            if (!is_array($m)) continue;
                            $label = trim((string)($m['label'] ?? 'Link'));
                            $url   = trim((string)($m['url'] ?? '#'));
                            $children = isset($m['children']) && is_array($m['children']) ? $m['children'] : [];

                            if (!empty($children)) {
                                $ddId = $navId . '-ddc-' . $i;
                                $html .= '<li class="nav-item dropdown">';
                                $html .= '<a class="nav-link' . $hoverClass . ' dropdown-toggle d-inline-flex align-items-center" href="' . nxb_core_h($url) . '" id="' . nxb_core_h($ddId) . '" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-nx-inline="menu:' . nxb_core_h((string)$i) . ':label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($label) . '<i class="bi bi-chevron-down ms-1"></i></a>';
                                if ($isBuilder) {
                                    $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="' . nxb_core_h((string)$i) . '" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                                }
                                $html .= '<ul class="dropdown-menu" aria-labelledby="' . nxb_core_h($ddId) . '">';
                                foreach ($children as $ci => $c) {
                                    if (!is_array($c)) continue;
                                    $cl = trim((string)($c['label'] ?? 'Unterlink'));
                                    $cu = trim((string)($c['url'] ?? '#'));
                                    $path = $i . '.children.' . $ci;
                                    $html .= '<li>';
                                    $html .= '<a class="dropdown-item" href="' . nxb_core_h($cu) . '" data-nx-inline="menu:' . nxb_core_h($path) . ':label" title="Doppelklick zum Bearbeiten">' . nxb_core_h($cl) . '</a>';
                                    if ($isBuilder) {
                                        $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-2 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="' . nxb_core_h($path) . '" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                                    }
                                    $html .= '</li>';
                                }
                                $html .= '</ul></li>';
                            } else {
                                $html .= '<li class="nav-item">';
                                $html .= '<a class="nav-link' . $hoverClass . '" href="' . nxb_core_h($url) . '" data-nx-inline="menu:' . nxb_core_h((string)$i) . ':label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($label) . '</a>';
                                if ($isBuilder) {
                                    $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="' . nxb_core_h((string)$i) . '" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                                }
                                $html .= '</li>';
                            }
                        }
                    } else {
                        // Fallback: alte 4 Links
                        $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item1_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item1) . '</a></li>';
                        $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item2_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item2) . '</a></li>';
                        $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item3_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item3) . '</a></li>';
                        $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item4_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item4) . '</a></li>';
                    }
                    $html .= function_exists('nxb_nav_language_selector_html') ? nxb_nav_language_selector_html($linkPaddingStyle) : '';
                    $html .= '</ul></div></div></nav>';
                    return $html;
                }

                // Default: klassische Top-Navigation (ohne Dropdown)
                $item1 = trim((string)($settings['item1_label'] ?? 'Produkt'));
                $item2      = trim((string)($settings['item2_label'] ?? 'Features'));
                $item3      = trim((string)($settings['item3_label'] ?? 'Preise'));
                $item4      = trim((string)($settings['item4_label'] ?? 'Ressourcen'));
                $loginLabel = trim((string)($settings['login_label'] ?? 'Login'));
                $loginUrl   = trim((string)($settings['login_url'] ?? ''));
                if ($loginUrl === '' || $loginUrl === '#') {
                    $loginUrl = \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?site=login');
                }
                $ctaLabel   = trim((string)($settings['cta_label'] ?? 'Jetzt starten'));

                $html = $navLinkStyle . '<nav class="' . nxb_core_h($navClass . ' nx-nav-core-demo') . '"' . $navStyle . $navDataAttrs . '>';
                $html .= '<div class="' . nxb_core_h($containerClass) . '">';
                $html .= $brandHtml;
                $html .= '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#' . nxb_core_h($navId) . '" aria-controls="' . nxb_core_h($navId) . '" aria-expanded="false" aria-label="Navigation umschalten">';
                $html .= '<span class="navbar-toggler-icon"></span></button>';
                $html .= '<div class="collapse navbar-collapse" id="' . nxb_core_h($navId) . '">';
                $html .= '<ul class="navbar-nav me-auto mb-2 mb-lg-0">';
                if ($menu !== null && !empty($menu)) {
                    // Hover- und Klick-Dropdown im Frontend sichtbar (Bootstrap versteckt .dropdown-menu standardmäßig)
                    $html = str_replace('class="' . nxb_core_h($navClass . ' nx-nav-core-demo') . '"', 'class="' . nxb_core_h($navClass . ' nx-nav-core-demo nx-nav-hover') . '"', $html);
                    $html = '<style>.nx-nav-hover .dropdown:hover .dropdown-menu,.nx-nav-hover .dropdown.show .dropdown-menu{display:block !important;}</style>' . $html;

                    foreach ($menu as $i => $m) {
                        if (!is_array($m)) continue;
                        $label = trim((string)($m['label'] ?? 'Link'));
                        $url   = trim((string)($m['url'] ?? '#'));
                        $children = isset($m['children']) && is_array($m['children']) ? $m['children'] : [];

                        if (!empty($children)) {
                            $ddId = $navId . '-dd-' . $i;
                            $html .= '<li class="nav-item dropdown">';
                            $html .= '<a class="nav-link' . $hoverClass . ' dropdown-toggle d-inline-flex align-items-center" href="' . nxb_core_h($url) . '" id="' . nxb_core_h($ddId) . '" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-nx-inline="menu:' . nxb_core_h((string)$i) . ':label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($label) . '<i class="bi bi-chevron-down ms-1"></i></a>';
                            if ($isBuilder) {
                                $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="' . nxb_core_h((string)$i) . '" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                            }
                            $html .= '<ul class="dropdown-menu" aria-labelledby="' . nxb_core_h($ddId) . '">';
                            foreach ($children as $ci => $c) {
                                if (!is_array($c)) continue;
                                $cl = trim((string)($c['label'] ?? 'Unterlink'));
                                $cu = trim((string)($c['url'] ?? '#'));
                                $path = $i . '.children.' . $ci;
                                $html .= '<li>';
                                $html .= '<a class="dropdown-item" href="' . nxb_core_h($cu) . '" data-nx-inline="menu:' . nxb_core_h($path) . ':label" title="Doppelklick zum Bearbeiten">' . nxb_core_h($cl) . '</a>';
                                if ($isBuilder) {
                                    $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-2 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="' . nxb_core_h($path) . '" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                                }
                                $html .= '</li>';
                            }
                            $html .= '</ul></li>';
                        } else {
                            $html .= '<li class="nav-item">';
                            $html .= '<a class="nav-link' . $hoverClass . '" href="' . nxb_core_h($url) . '" data-nx-inline="menu:' . nxb_core_h((string)$i) . ':label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($label) . '</a>';
                            if ($isBuilder) {
                                $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="' . nxb_core_h((string)$i) . '" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                            }
                            $html .= '</li>';
                        }
                    }
                } else {
                    // Fallback-Links – Padding auf den Links
                    $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . ' active" aria-current="page" href="#" data-nx-inline="item1_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item1) . '</a></li>';
                    $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item2_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item2) . '</a></li>';
                    $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item3_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item3) . '</a></li>';
                    $html .= '<li class="nav-item"><a class="nav-link' . $hoverClass . '" href="#" data-nx-inline="item4_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '>' . nxb_core_h($item4) . '</a></li>';
                }
                // Login-/User-UI kommt direkt aus dem Builder-Widget.
                if (session_status() === PHP_SESSION_NONE) session_start();
                global $_database;
                $uid = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;
                if ($uid <= 0) {
                    if ($loginLabel !== '' || $isBuilder) {
                        $loginText = $loginLabel !== '' ? $loginLabel : 'Login';
                        $loginHref = $loginUrl !== '' ? $loginUrl : \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?site=login');
                        $html .= '<li class="nav-item ms-lg-3">';
                        $html .= '<a class="nav-link' . $hoverClass . '" href="' . nxb_core_h($loginHref) . '" data-nx-inline="login_label" title="Doppelklick zum Bearbeiten"' . $linkPaddingStyle . '><i class="bi bi-box-arrow-in-right me-1"></i> ' . nxb_core_h($loginText) . '</a>';
                        if ($isBuilder) {
                            $html .= '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 nx-nav-gear" data-nx-nav-gear="1" data-nx-nav-path="login" title="Link-Einstellungen"><i class="bi bi-gear"></i></button>';
                        }
                        $html .= '</li>';
                    }
                } else {
                    $avatar = htmlspecialchars(getavatar($uid), ENT_QUOTES, 'UTF-8');
                    $username = htmlspecialchars(getusername($uid), ENT_QUOTES, 'UTF-8');
                    $canAdmin = class_exists(\nexpell\AccessControl::class) ? \nexpell\AccessControl::canAccessAdmin($_database, $uid) : false;
                    $messengerBadgeHtml = '';
                    $forumBadgeHtml = '';
                    $messengerUrl = \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?site=messenger');
                    $forumUrl      = \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?site=forum');

                    if (!$isBuilder) {
                        try {
                            if (class_exists(\nexpell\PluginManager::class) && \nexpell\PluginManager::isActive('messenger')) {
                                $check = $_database->query("SHOW TABLES LIKE 'plugins_messages'");
                                if ($check && $check->num_rows > 0) {
                                    $row = mysqli_fetch_assoc(safe_query("
                                        SELECT COUNT(*) AS unread
                                        FROM plugins_messages
                                        WHERE receiver_id = {$uid}
                                          AND is_read = 0
                                    "));
                                    $unread = (int)($row['unread'] ?? 0);
                                    if ($unread > 0) {
                                        $badge = ($unread > 99) ? '99+' : (string)$unread;
                                        $messengerBadgeHtml = "<span class='badge rounded-pill bg-danger'>{$badge}</span>";
                                    }
                                }
                            }
                        } catch (\Throwable $e) {}

                        try {
                            if (class_exists(\nexpell\PluginManager::class) && \nexpell\PluginManager::isActive('forum')) {
                                $check = $_database->query("SHOW TABLES LIKE 'plugins_forum_read'");
                                if ($check && $check->num_rows > 0) {
                                    $row2 = mysqli_fetch_assoc(safe_query("
                                        SELECT COUNT(*) AS new_posts
                                        FROM plugins_forum_posts p
                                        INNER JOIN plugins_forum_threads t
                                            ON t.threadID = p.threadID
                                           AND t.is_deleted = 0
                                        LEFT JOIN plugins_forum_read r
                                            ON r.userID = {$uid}
                                           AND r.threadID = p.threadID
                                        WHERE p.is_deleted = 0
                                          AND p.created_at > IFNULL(r.last_read_at, '1970-01-01')
                                    "));
                                    $count = (int)($row2['new_posts'] ?? 0);
                                    if ($count > 0) {
                                        $badge2 = ($count > 99) ? '99+' : (string)$count;
                                        $forumBadgeHtml = "<span class='badge rounded-pill bg-danger'>{$badge2}</span>";
                                    }
                                }
                            }
                        } catch (\Throwable $e) {}
                    }

                    $messengerHtml = '';
                    if (class_exists(\nexpell\PluginManager::class) && \nexpell\PluginManager::isActive('messenger')) {
                        $messengerHtml = "
                            <li class='nav-item'>
                                <a class='nav-link nav-icon-badge{$hoverClass} d-inline-flex align-items-center' href='{$messengerUrl}'{$linkPaddingStyle}>
                                    <span class='icon-wrapper'>
                                        <i class='bi bi-envelope fs-5'></i>
                                        {$messengerBadgeHtml}
                                    </span>
                                </a>
                            </li>";
                    }
                    $forumHtml = '';
                    if (class_exists(\nexpell\PluginManager::class) && \nexpell\PluginManager::isActive('forum')) {
                        $forumHtml = "
                            <li class='nav-item'>
                                <a class='nav-link nav-icon-badge{$hoverClass} d-inline-flex align-items-center' href='{$forumUrl}'{$linkPaddingStyle}>
                                    <span class='icon-wrapper'>
                                        <i class='bi bi-chat-dots fs-5'></i>
                                        {$forumBadgeHtml}
                                    </span>
                                </a>
                            </li>";
                    }

                    if ($messengerHtml !== '') $html .= $messengerHtml;
                    if ($forumHtml !== '') $html .= $forumHtml;
                    $profileHref = \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?site=profile&userID=' . $uid);
                    $logoutHref  = \nexpell\SeoUrlHandler::convertToSeoUrl('index.php?site=logout');
                    $adminHref   = '/admin/admincenter.php';

                    $html .= '<li class="nav-item dropdown ms-lg-3">';
                    $html .= '<a class="nav-link' . $hoverClass . ' d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown" aria-expanded="false"' . $linkPaddingStyle . '>';
                    $html .= '<img src="' . $avatar . '" class="navbar-avatar" style="width:22px;height:22px;border-radius:4px;" alt="' . $username . '">';
                    $html .= $username . '<i class="bi bi-chevron-down ms-1"></i></a>';
                    $html .= '<ul class="dropdown-menu dropdown-menu-end">';
                    $html .= '<li><a class="dropdown-item" href="' . nxb_core_h($profileHref) . '"><i class="bi bi-person me-2"></i> Profil</a></li>';
                    if ($canAdmin) {
                        $html .= '<li><a class="dropdown-item" href="' . nxb_core_h($adminHref) . '" target="_blank"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>';
                    }
                    $html .= '<li><hr class="dropdown-divider"></li>';
                    $html .= '<li><a class="dropdown-item" href="' . nxb_core_h($logoutHref) . '"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>';
                    $html .= '</ul></li>';
                }
                $html .= function_exists('nxb_nav_language_selector_html') ? nxb_nav_language_selector_html($linkPaddingStyle) : '';
                $html .= '</ul></div></div></nav>';
                return $html;

            case 'core_slider':
                // Bild-Carousel (Bootstrap Carousel-Markup)
                $sectionTitle    = trim((string)($settings['title'] ?? ''));
                $sectionSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $align = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $sectionClass = 'nx-slider mb-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $effect = (string)($settings['effect'] ?? 'slide'); // slide|fade
                $captionStyle = (string)($settings['captionStyle'] ?? 'dark'); // dark|light|none
                $showIndicators = !array_key_exists('showIndicators', $settings) || !empty($settings['showIndicators']);
                $showControls   = !array_key_exists('showControls', $settings) || !empty($settings['showControls']);
                $autoPlay       = !array_key_exists('autoPlay', $settings) || !empty($settings['autoPlay']);
                $interval       = isset($settings['interval']) ? (int)$settings['interval'] : 5000;
                if ($interval < 1000 || $interval > 20000) {
                    $interval = 5000;
                }

                $items = [];
                for ($i = 1; $i <= 6; $i++) {
                    $src = trim((string)($settings["item{$i}_src"] ?? ''));
                    if ($src === '') {
                        continue;
                    }
                    $alt = trim((string)($settings["item{$i}_alt"] ?? ''));
                    $cap = trim((string)($settings["item{$i}_caption"] ?? ''));
                    $items[] = ['index' => $i, 'src' => $src, 'alt' => $alt, 'caption' => $cap];
                }

                if (empty($items) && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $items = [
                        ['index' => 1, 'src' => 'https://via.placeholder.com/1200x600?text=Slide+1', 'alt' => 'Slide 1', 'caption' => 'Erster Slide – im Builder anpassen.'],
                        ['index' => 2, 'src' => 'https://via.placeholder.com/1200x600?text=Slide+2', 'alt' => 'Slide 2', 'caption' => 'Zweiter Slide – im Builder anpassen.'],
                        ['index' => 3, 'src' => 'https://via.placeholder.com/1200x600?text=Slide+3', 'alt' => 'Slide 3', 'caption' => 'Dritter Slide – im Builder anpassen.'],
                    ];
                }

                if (empty($items)) {
                    return '<section class="' . nxb_core_h($sectionClass) . '"><div class="container-fluid"><div class="alert alert-secondary small mb-0">Carousel – Bilder in den Einstellungen anlegen.</div></div></section>';
                }

                $sliderId = 'nx-carousel-' . substr(md5(json_encode($settings)), 0, 8);

                // Container für Text bewusst als container-fluid, damit das Carousel in Container-Fluid-Layouts voll greifen kann
                $html = '<section class="' . nxb_core_h($sectionClass) . '">';
                if ($sectionTitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $titleText = $sectionTitle !== '' ? nxb_core_h($sectionTitle) : 'Carousel – Doppelklick zum Bearbeiten';
                    $titleClass = $sectionTitle !== '' ? 'h2 mb-2' : 'h2 mb-2 text-muted nx-inline-placeholder';
                    $html .= '<h2 class="' . nxb_core_h($titleClass) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titleText . '</h2>';
                }
                if ($sectionSubtitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $subText = $sectionSubtitle !== '' ? nxb_core_h($sectionSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $subClass = $sectionSubtitle !== '' ? 'mb-3 text-muted' : 'mb-3 text-muted nx-inline-placeholder';
                    $html .= '<p class="' . nxb_core_h($subClass) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</p>';
                }

                $carouselClasses = 'carousel slide';
                if ($effect === 'fade') {
                    $carouselClasses .= ' carousel-fade';
                }
                $rideAttr = $autoPlay ? ' data-bs-ride="carousel"' : '';
                $intervalAttr = $autoPlay ? ' data-bs-interval="' . $interval . '"' : ' data-bs-interval="false"';

                $html .= '<div id="' . nxb_core_h($sliderId) . '" class="' . nxb_core_h($carouselClasses) . '"' . $rideAttr . $intervalAttr . '>';

                // Indikatoren
                if ($showIndicators && count($items) > 1) {
                    $html .= '<div class="carousel-indicators">';
                    foreach ($items as $idx => $_) {
                        $active = $idx === 0 ? ' class="active" aria-current="true"' : '';
                        $html .= '<button type="button" data-bs-target="#' . nxb_core_h($sliderId) . '" data-bs-slide-to="' . $idx . '"' . $active . ' aria-label="Slide ' . ($idx + 1) . '"></button>';
                    }
                    $html .= '</div>';
                }

                // Slides
                $html .= '<div class="carousel-inner">';
                foreach ($items as $idx => $it) {
                    $active = $idx === 0 ? ' active' : '';
                    $itemIndex = (int)($it['index'] ?? ($idx + 1));
                    $html .= '<div class="carousel-item' . $active . '">';
                    $html .= '<div class="ratio ratio-16x9 overflow-hidden bg-secondary">';
                    $html .= '<img src="' . nxb_core_h($it['src']) . '" alt="' . nxb_core_h($it['alt']) . '" class="w-100 h-100 object-fit-cover" loading="lazy" />';
                    $html .= '</div>';
                    if ($it['caption'] !== '') {
                        $html .= '<div class="carousel-caption d-none d-md-block">';
                        $captionClasses = 'small';
                        if ($captionStyle === 'dark') {
                            $captionClasses .= ' bg-dark bg-opacity-50 text-white rounded px-2 py-1 d-inline-block';
                        } elseif ($captionStyle === 'light') {
                            $captionClasses .= ' bg-white bg-opacity-75 text-dark rounded px-2 py-1 d-inline-block shadow-sm';
                        }
                        $html .= '<p class="' . nxb_core_h($captionClasses) . '" data-nx-inline="' . nxb_core_h('item' . $itemIndex . '_caption') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['caption']) . '</p>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';

                // Controls
                if ($showControls && count($items) > 1) {
                    $html .= '<button class="carousel-control-prev" type="button" data-bs-target="#' . nxb_core_h($sliderId) . '" data-bs-slide="prev">';
                    $html .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Vorheriger</span></button>';
                    $html .= '<button class="carousel-control-next" type="button" data-bs-target="#' . nxb_core_h($sliderId) . '" data-bs-slide="next">';
                    $html .= '<span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Nächster</span></button>';
                }

                $html .= '</div></section>';
                return $html;

            case 'core_pricing':
                // Pricing-Sektion mit 2–4 Plänen
                $sectionTitle    = trim((string)($settings['title'] ?? ''));
                $sectionSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $columns = (int)($settings['columns'] ?? 3);
                if ($columns < 2) {
                    $columns = 2;
                } elseif ($columns > 4) {
                    $columns = 4;
                }
                $align = (string)($settings['align'] ?? 'center');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis = nxb_visibility_class($settings);
                $sectionClass = 'nx-pricing py-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $plans = [];
                for ($i = 1; $i <= 4; $i++) {
                    $name   = trim((string)($settings["plan{$i}_name"] ?? ''));
                    $price  = trim((string)($settings["plan{$i}_price"] ?? ''));
                    $period = trim((string)($settings["plan{$i}_period"] ?? ''));
                    $featuresRaw = (string)($settings["plan{$i}_features"] ?? '');
                    $buttonLabel = trim((string)($settings["plan{$i}_buttonLabel"] ?? ''));
                    $buttonUrl   = trim((string)($settings["plan{$i}_buttonUrl"] ?? '#'));
                    $featured    = !empty($settings["plan{$i}_featured"]);
                    if ($name === '' && $price === '' && $featuresRaw === '' && $buttonLabel === '') {
                        continue;
                    }
                    $features = [];
                    foreach (preg_split('~\r\n|\r|\n~', $featuresRaw) as $line) {
                        $line = trim((string)$line);
                        if ($line !== '') {
                            $features[] = $line;
                        }
                    }
                    $plans[] = [
                        'index'    => $i,
                        'name'     => $name !== '' ? $name : "Plan {$i}",
                        'price'    => $price,
                        'period'   => $period,
                        'features' => $features,
                        'button'   => $buttonLabel,
                        'url'      => $buttonUrl,
                        'featured' => $featured,
                    ];
                }

                if (empty($plans) && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $plans = [
                        [
                            'index'    => 1,
                            'name'     => 'Starter',
                            'price'    => '9',
                            'period'   => 'mtl.',
                            'features' => ['1 Projekt', 'Basissupport'],
                            'button'   => 'Jetzt starten',
                            'url'      => '#',
                            'featured' => false,
                        ],
                        [
                            'index'    => 2,
                            'name'     => 'Pro',
                            'price'    => '19',
                            'period'   => 'mtl.',
                            'features' => ['Unbegrenzte Projekte', 'Priorisierter Support', 'Erweiterte Features'],
                            'button'   => 'Beliebter Plan',
                            'url'      => '#',
                            'featured' => true,
                        ],
                        [
                            'index'    => 3,
                            'name'     => 'Enterprise',
                            'price'    => 'Kontakt',
                            'period'   => '',
                            'features' => ['Individuelle SLAs', 'Persönlicher Ansprechpartner'],
                            'button'   => 'Kontakt aufnehmen',
                            'url'      => '#',
                            'featured' => false,
                        ],
                    ];
                }

                if (empty($plans)) {
                    return '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="alert alert-secondary small mb-0">Pricing – Pläne in den Einstellungen anlegen.</div></div></section>';
                }

                $colClass = 'col-12 col-md-4';
                if ($columns === 2) {
                    $colClass = 'col-12 col-md-6';
                } elseif ($columns === 3) {
                    $colClass = 'col-12 col-md-4';
                } elseif ($columns === 4) {
                    $colClass = 'col-12 col-md-3';
                }

                $html = '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="row justify-content-center"><div class="col-lg-11">';
                if ($sectionTitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $titleText = $sectionTitle !== '' ? nxb_core_h($sectionTitle) : 'Preise – Doppelklick zum Bearbeiten';
                    $titleClass = $sectionTitle !== '' ? 'h2 mb-2' : 'h2 mb-2 text-muted nx-inline-placeholder';
                    $html .= '<h2 class="' . nxb_core_h($titleClass) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titleText . '</h2>';
                }
                if ($sectionSubtitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $subText = $sectionSubtitle !== '' ? nxb_core_h($sectionSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $subClass = $sectionSubtitle !== '' ? 'mb-4 text-muted' : 'mb-4 text-muted nx-inline-placeholder';
                    $html .= '<p class="' . nxb_core_h($subClass) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</p>';
                }

                $html .= '<div class="row g-4 align-items-stretch justify-content-center">';
                foreach ($plans as $idx => $plan) {
                    $planIndex = (int)($plan['index'] ?? ($idx + 1));
                    $featured = $plan['featured'];
                    $cardClasses = 'card h-100 shadow-sm border-0';
                    if ($featured) {
                        $cardClasses .= ' border-primary border-2 shadow';
                    }
                    $badgeHtml = '';
                    if ($featured) {
                        $badgeHtml = '<div class="position-absolute top-0 end-0 m-2"><span class="badge text-bg-primary">Beliebt</span></div>';
                    }
                    $html .= '<div class="' . nxb_core_h($colClass) . '"><div class="position-relative ' . nxb_core_h($cardClasses) . '">';
                    $html .= $badgeHtml;
                    $html .= '<div class="card-body d-flex flex-column">';
                    $html .= '<h3 class="h5 mb-2" data-nx-inline="' . nxb_core_h('plan' . $planIndex . '_name') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($plan['name']) . '</h3>';
                    if ($plan['price'] !== '' || $plan['period'] !== '') {
                        $html .= '<div class="mb-3"><span class="display-6 fw-bold" data-nx-inline="' . nxb_core_h('plan' . $planIndex . '_price') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($plan['price']) . '</span>';
                        if ($plan['period'] !== '') {
                            $html .= '<span class="text-muted ms-1" data-nx-inline="' . nxb_core_h('plan' . $planIndex . '_period') . '" title="Doppelklick zum Bearbeiten">/' . nxb_core_h($plan['period']) . '</span>';
                        }
                        $html .= '</div>';
                    }
                    if (!empty($plan['features'])) {
                        // Features stammen aus planX_features (Textarea, zeilenweise). Für Inline-Editing bündeln wir alle Zeilen in einem Block.
                        $html .= '<div class="mb-3 small text-start" data-nx-inline="' . nxb_core_h('plan' . $planIndex . '_features') . '" title="Doppelklick zum Bearbeiten">';
                        foreach ($plan['features'] as $feat) {
                            $html .= '<div class="d-flex align-items-start gap-2 mb-1"><i class="bi bi-check-circle text-success mt-1"></i><span>' . nxb_core_h($feat) . '</span></div>';
                        }
                        $html .= '</div>';
                    }
                    if ($plan['button'] !== '') {
                        $btnClass = $featured ? 'btn btn-primary w-100' : 'btn btn-outline-primary w-100';
                        $html .= '<div class="mt-auto pt-2"><a href="' . nxb_core_h($plan['url']) . '" class="' . nxb_core_h($btnClass) . '" data-nx-inline="' . nxb_core_h('plan' . $planIndex . '_buttonLabel') . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($plan['button']) . '</a></div>';
                    }
                    $html .= '</div></div></div>';
                }
                $html .= '</div></div></div></section>';
                return $html;

            case 'core_collapse':
                // Einzelner Collapse-Block mit modernem Button und Body
                $title      = trim((string)($settings['title'] ?? 'Details anzeigen'));
                $body       = trim((string)($settings['text'] ?? 'Inhalt für den Collapse-Block eingeben.'));
                $align      = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis        = nxb_visibility_class($settings);
                $flush      = !empty($settings['flush']);
                $borderless = !empty($settings['borderless']);
                $startOpen  = !empty($settings['open']);

                $outerClass = 'nx-collapse mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $collapseId = 'nx-collapse-' . substr(md5(json_encode($settings)), 0, 8);

                $cardClasses = 'card';
                if ($flush) {
                    $cardClasses .= ' border-0';
                }
                if ($borderless) {
                    $cardClasses .= ' bg-transparent shadow-none';
                }

                $html = '<div class="' . nxb_core_h($outerClass) . '">';
                $html .= '<button class="btn btn-outline-primary d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#' . nxb_core_h($collapseId) . '" aria-expanded="' . ($startOpen ? 'true' : 'false') . '">';
                $html .= '<span data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . nxb_core_h($title) . '</span>';
                $html .= '<span class="collapse-indicator ms-1"><i class="bi bi-chevron-down"></i></span>';
                $html .= '</button>';
                $html .= '<div id="' . nxb_core_h($collapseId) . '" class="collapse' . ($startOpen ? ' show' : '') . ' mt-2">';
                $html .= '<div class="' . nxb_core_h($cardClasses) . '"><div class="card-body small text-body-secondary" data-nx-inline="text" title="Doppelklick zum Bearbeiten">' . nl2br(nxb_core_h($body)) . '</div></div>';
                $html .= '</div></div>';
                return $html;

            case 'core_list_group':
                // Bootstrap List-Group mit verschiedenen Darstellungsoptionen
                $sectionTitle    = trim((string)($settings['title'] ?? ''));
                $sectionSubtitle = trim((string)($settings['subtitle'] ?? ''));
                $align           = (string)($settings['align'] ?? 'start');
                $alignClass      = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis             = nxb_visibility_class($settings);
                $sectionClass    = 'nx-list-group mb-4 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');
                $flush           = !empty($settings['flush']);
                $numbered        = !empty($settings['numbered']);
                $interactive     = !empty($settings['interactive']);

                $items = [];
                for ($i = 1; $i <= 10; $i++) {
                    $text  = trim((string)($settings["item{$i}_text"] ?? ''));
                    $badge = trim((string)($settings["item{$i}_badge"] ?? ''));
                    if ($text === '' && $badge === '') {
                        continue;
                    }
                    $items[] = [
                        'index' => $i,
                        'text'  => $text !== '' ? $text : "Listeneintrag {$i}",
                        'badge' => $badge,
                    ];
                }

                if (empty($items) && function_exists('nxb_is_builder') && nxb_is_builder()) {
                    $items = [
                        ['index' => 1, 'text' => 'Erster Eintrag', 'badge' => 'Neu'],
                        ['index' => 2, 'text' => 'Zweiter Eintrag', 'badge' => '3'],
                        ['index' => 3, 'text' => 'Dritter Eintrag', 'badge' => ''],
                    ];
                }

                if (empty($items)) {
                    return '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="alert alert-secondary small mb-0">List-Group – Einträge in den Einstellungen anlegen.</div></div></section>';
                }

                $html = '<section class="' . nxb_core_h($sectionClass) . '"><div class="container"><div class="row justify-content-center"><div class="col-lg-8">';
                if ($sectionTitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $titleText = $sectionTitle !== '' ? nxb_core_h($sectionTitle) : 'List-Group – Doppelklick zum Bearbeiten';
                    $titleClass = $sectionTitle !== '' ? 'h3 mb-2' : 'h3 mb-2 text-muted nx-inline-placeholder';
                    $html .= '<h2 class="' . nxb_core_h($titleClass) . '" data-nx-inline="title" title="Doppelklick zum Bearbeiten">' . $titleText . '</h2>';
                }
                if ($sectionSubtitle !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                    $subText = $sectionSubtitle !== '' ? nxb_core_h($sectionSubtitle) : 'Untertitel – Doppelklick zum Hinzufügen';
                    $subClass = $sectionSubtitle !== '' ? 'mb-3 text-muted' : 'mb-3 text-muted nx-inline-placeholder';
                    $html .= '<p class="' . nxb_core_h($subClass) . '" data-nx-inline="subtitle" title="Doppelklick zum Bearbeiten">' . $subText . '</p>';
                }

                $listTag   = $numbered ? 'ol' : 'ul';
                $listClass = 'list-group';
                if ($flush) {
                    $listClass .= ' list-group-flush';
                }
                if ($numbered) {
                    $listClass .= ' list-group-numbered';
                }

                $html .= '<' . $listTag . ' class="' . nxb_core_h($listClass) . '">';
                foreach ($items as $it) {
                    $idx = (int)($it['index'] ?? 0);
                    $badge = $it['badge'];
                    $itemField = 'item' . $idx . '_text';
                    $badgeField = 'item' . $idx . '_badge';
                    $inner = '<div class="d-flex justify-content-between align-items-center gap-3">';
                    $inner .= '<span data-nx-inline="' . nxb_core_h($itemField) . '" title="Doppelklick zum Bearbeiten">' . nxb_core_h($it['text']) . '</span>';
                    if ($badge !== '' || (function_exists('nxb_is_builder') && nxb_is_builder())) {
                        $badgeText = $badge !== '' ? nxb_core_h($badge) : 'Badge';
                        $badgeClass = $badge !== '' ? 'badge rounded-pill text-bg-secondary' : 'badge rounded-pill text-bg-secondary nx-inline-placeholder';
                        $inner .= '<span class="' . nxb_core_h($badgeClass) . '" data-nx-inline="' . nxb_core_h($badgeField) . '" title="Doppelklick zum Bearbeiten">' . $badgeText . '</span>';
                    }
                    $inner .= '</div>';

                    if ($interactive) {
                        $html .= '<li class="list-group-item list-group-item-action">' . $inner . '</li>';
                    } else {
                        $html .= '<li class="list-group-item">' . $inner . '</li>';
                    }
                }
                $html .= '</' . $listTag . '>';
                $html .= '</div></div></div></section>';
                return $html;

            case 'core_link':
                // Einfacher Text-Link mit moderner Optik
                $label      = trim((string)($settings['label'] ?? 'Link-Text'));
                $href       = trim((string)($settings['href'] ?? '#'));
                $style      = (string)($settings['style'] ?? 'primary'); // primary|secondary|muted
                $underline  = !empty($settings['underline']);
                $targetBlank = !empty($settings['targetBlank']);
                $align      = (string)($settings['align'] ?? 'start');
                $alignClass = ($align === 'center') ? 'text-center' : (($align === 'end' || $align === 'right') ? 'text-end' : 'text-start');
                $vis        = nxb_visibility_class($settings);

                $wrapClass = 'mb-3 ' . $alignClass . ($vis !== '' ? ' ' . $vis : '');

                $linkClass = 'fw-semibold';
                if ($style === 'secondary') {
                    $linkClass .= ' link-secondary';
                } elseif ($style === 'muted') {
                    $linkClass .= ' link-secondary text-muted';
                } else {
                    $linkClass .= ' link-primary';
                }
                if ($underline) {
                    $linkClass .= ' text-decoration-underline';
                } else {
                    $linkClass .= ' text-decoration-none text-decoration-underline-hover';
                }

                $attrs = [];
                $attrs[] = 'href="' . nxb_core_h($href !== '' ? $href : '#') . '"';
                $attrs[] = 'class="' . nxb_core_h($linkClass) . '"';
                if ($targetBlank && $href !== '') {
                    $attrs[] = 'target="_blank" rel="noopener noreferrer"';
                }

                $html = '<div class="' . nxb_core_h($wrapClass) . '">';
                $html .= '<a ' . implode(' ', $attrs) . ' data-nx-inline="label" title="Doppelklick zum Bearbeiten">' . nxb_core_h($label) . '</a>';
                $html .= '</div>';
                return $html;

            default:
                return '<div class="alert alert-warning small mb-3">Unbekanntes Core-Widget: '
                    . nxb_core_h($widget_key) . '</div>';
        }
    }
}

