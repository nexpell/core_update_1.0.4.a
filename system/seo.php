<?php

use nexpell\LanguageService;
use nexpell\SeoUrlHandler;

function settitle($string)
{
    $base_title = isset($GLOBALS['hp_title']) ? $GLOBALS['hp_title'] : 'Website';
    return $base_title . ' - ' . $string;
}

function extractFirstElement($element)
{
    return is_array($element) && isset($element[0]) ? $element[0] : '';
}

function getPageTitle($url = null, $prefix = true)
{
    $data = parsenexpellURL($url);

    if (!is_array($data)) {
        return $prefix ? settitle('') : '';
    }

    // Metatags zusammenführen
    if (isset($data['metatags']) && is_array($data['metatags'])) {
        if (isset($GLOBALS['metatags']) && is_array($GLOBALS['metatags'])) {
            $GLOBALS['metatags'] = array_merge($GLOBALS['metatags'], $data['metatags']);
        } else {
            $GLOBALS['metatags'] = $data['metatags'];
        }
    }

    $canonical = nx_get_seo_canonical_url();
    if ($canonical !== '') {
        $GLOBALS['metatags']['canonical'] = $canonical;
    }

    $robots = nx_get_seo_robots_content();
    if ($robots !== null) {
        $GLOBALS['metatags']['robots'] = $robots;
    }

    // Seitentitel erzeugen
    $titles = array();
    if (isset($data['titles']) && is_array($data['titles'])) {
        $titles = array_map("extractFirstElement", $data['titles']);
    }

    $title = implode('&nbsp;&raquo;&nbsp;', array_filter($titles));
    return $prefix ? settitle($title) : $title;
}

function nx_seo_url(string $url): string
{
    return SeoUrlHandler::convertToSeoUrl($url);
}

function nx_get_seo_canonical_url(?array $parameters = null): string
{
    $parameters = $parameters ?? $_GET;

    if (!is_array($parameters) || empty($parameters['site'])) {
        return '';
    }

    $query = $parameters;
    unset($query['new_lang'], $query['setlang'], $query['profile_error']);

    $canonicalPath = SeoUrlHandler::convertToSeoUrl(
        'index.php?' . http_build_query($query)
    );
    $canonicalPath = (string)(parse_url($canonicalPath, PHP_URL_PATH) ?? '');
    if ($canonicalPath === '') {
        return '';
    }

    $baseUrl = '';

    if (function_exists('safe_query')) {
        $result = @safe_query("SELECT hpurl FROM settings LIMIT 1");
        if ($result && ($row = mysqli_fetch_assoc($result))) {
            $baseUrl = rtrim((string)($row['hpurl'] ?? ''), '/');
        }
    }

    if ($baseUrl === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        if ($host === '') {
            return $canonicalPath;
        }
        $baseUrl = $scheme . '://' . $host;
    }

    return $baseUrl . $canonicalPath;
}

function nx_get_seo_robots_content(?array $parameters = null): ?string
{
    $parameters = $parameters ?? $_GET;

    if (!is_array($parameters)) {
        return null;
    }

    $site = strtolower((string)($parameters['site'] ?? ''));
    $page = (int)($parameters['page'] ?? 0);

    if ($page > 1) {
        return 'noindex,follow';
    }

    if (in_array($site, ['search', 'login', 'register', 'lostpassword', 'shoutbox', 'live_visitor'], true)) {
        return 'noindex,follow';
    }

    return null;
}


function parsenexpellURL($parameters = null)
{ 

global $languageService;
global $_database;

if (!isset($languageService) || !$languageService instanceof LanguageService) {
    $languageService = new LanguageService($_database);
}

    if ($parameters === null) {
        $parameters = $_GET;
    }

    if (isset($parameters['action'])) {
        $action = $parameters['action'];
    } else {
        $action = '';
    }

    $returned_title = array();
    $metadata = array();
    if (isset($parameters['site'])) {
        switch ($parameters['site']) {

            case 'about':
                $aboutTitle = '';
                $aboutIntro = '';

                $hasModernAbout = false;
                $colCheck = safe_query("SHOW COLUMNS FROM plugins_about LIKE 'content_key'");
                if ($colCheck && mysqli_num_rows($colCheck) > 0) {
                    $hasModernAbout = true;
                }

                if ($hasModernAbout) {
                    $currentLang = strtolower((string)($languageService->detectLanguage() ?: ($_SESSION['language'] ?? 'en')));
                    $map = [];
                    $resAbout = safe_query("SELECT content_key, language, content FROM plugins_about WHERE content_key IN ('title','intro')");
                    while ($row = mysqli_fetch_assoc($resAbout)) {
                        $k = (string)$row['content_key'];
                        $l = strtolower((string)$row['language']);
                        $map[$k][$l] = (string)$row['content'];
                    }
                    foreach ([$currentLang, 'de', 'en', 'it'] as $iso) {
                        if ($aboutTitle === '' && !empty($map['title'][$iso])) {
                            $aboutTitle = (string)$map['title'][$iso];
                        }
                        if ($aboutIntro === '' && !empty($map['intro'][$iso])) {
                            $aboutIntro = (string)$map['intro'][$iso];
                        }
                    }
                } else {
                    $result = safe_query("SELECT title, intro FROM plugins_about ORDER BY id ASC LIMIT 1");
                    $about = mysqli_fetch_assoc($result);
                    $aboutTitle = (string)($about['title'] ?? '');
                    $aboutIntro = (string)($about['intro'] ?? '');
                }

                if ($aboutTitle !== '') {
                    $returned_title[] = [
                        $languageService->get('about'),
                        nx_seo_url('index.php?site=about')
                    ];
                    $returned_title[] = [$aboutTitle];

                    // Meta Description aus Intro (auf ~160 Zeichen beschränken)
                    $intro_excerpt = strip_tags($aboutIntro);
                    if (strlen($intro_excerpt) > 160) {
                        $intro_excerpt = substr($intro_excerpt, 0, 157) . '...';
                    }
                    $metadata['description'] = $intro_excerpt;

                } else {
                    $returned_title[] = [$languageService->get('about')];
                }
                break;


            case 'articles':
                $id = isset($parameters['id']) ? (int)$parameters['id'] : 0;
                $articleID = isset($parameters['articleID']) ? (int)$parameters['articleID'] : 0;

                // Kategorie holen (inkl. Beschreibung)
                $category = null;
                if ($id > 0) {
                    $result = safe_query("SELECT name, description FROM plugins_articles_categories WHERE id = $id");
                    if ($row = mysqli_fetch_assoc($result)) {
                        $category = $row;
                    }
                }

                // Artikel holen (inkl. Content)
                $article = null;
                if ($articleID > 0) {
                    $result2 = safe_query("SELECT title, content FROM plugins_articles WHERE id = $articleID");
                    if ($row2 = mysqli_fetch_assoc($result2)) {
                        $article = $row2;
                    }
                }

                if ($action === 'articlecat') {
                    $returned_title[] = [$languageService->get('articles'), nx_seo_url('index.php?site=articles')];

                    if ($category) {
                        $returned_title[] = [$category['name']];
                        // Meta-Beschreibung aus Kategorie-Beschreibung (max. 160 Zeichen, ohne HTML)
                        $metadata['description'] = mb_substr(strip_tags($category['description']), 0, 160);
                    }
                } elseif ($action === 'articles') {
                    $returned_title[] = [$languageService->get('articles'), nx_seo_url('index.php?site=articles')];

                    if ($category) {
                        $returned_title[] = [
                            $category['name'],
                            nx_seo_url('index.php?site=articles&action=articlecat&id=' . $id)
                        ];
                    }

                    if ($article) {
                        $returned_title[] = [$article['title']];
                        // Meta-Beschreibung aus Artikel-Content (max. 160 Zeichen, ohne HTML)
                        $metadata['description'] = mb_substr(strip_tags($article['content']), 0, 160);
                    }
                } else {
                    $returned_title[] = [$languageService->get('articles')];
                }
                break;


            case 'pricing':
                $planID = isset($parameters['planID']) ? (int)$parameters['planID'] : 0;

                // Alle Pläne holen (für Übersichtsseite)
                if ($planID === 0) {
                    $returned_title[] = [$languageService->get('pricing')];
                }
                // Einzelnen Plan mit Features holen (für Detailseite)
                else {
                    $plan = null;
                    $features = [];

                    // Plan holen
                    $resultPlan = safe_query("SELECT title FROM plugins_pricing_plans WHERE id = $planID");
                    if ($rowPlan = mysqli_fetch_assoc($resultPlan)) {
                        $plan = $rowPlan;
                    }

                    // Features holen
                    $resultFeatures = safe_query("SELECT feature_text FROM plugins_pricing_features WHERE plan_id = $planID AND available = 1 ORDER BY id ASC");
                    while ($rowFeature = mysqli_fetch_assoc($resultFeatures)) {
                        $features[] = $rowFeature['feature_text'];
                    }

                    // Titel setzen
                    if ($plan) {
                        $returned_title[] = [$languageService->get('pricing'), nx_seo_url('index.php?site=pricing')];
                        $returned_title[] = [$plan['title']];

                        // Meta-Description: Plan-Titel plus kurze Feature-Liste (max. 160 Zeichen)
                        $metaDescription = $plan['title'] . ': ' . implode(', ', $features);
                        $metadata['description'] = mb_substr($metaDescription, 0, 160);
                    } else {
                        // Fallback
                        $returned_title[] = [$languageService->get('pricing')];
                    }
                }
                break;
    
   

            case 'awards':
                if (isset($parameters['awardID'])) {
                    $awardID = (int)$parameters['awardID'];
                } else {
                    $awardID = '';
                }
                if ($action == "details") {
                    $get = mysqli_fetch_array(
                        safe_query("SELECT award FROM plugins_awards WHERE awardID=" . (int)$awardID)
                    );
                    $returned_title[] = array(
                        $languageService->get('awards'),
                        nx_seo_url('index.php?site=awards')
                    );
                    $returned_title[] = array($get['award']);
                } else {
                    $returned_title[] = array($languageService->get('awards'));
                }
                break;

            case 'calendar':
                $returned_title[] = array($languageService->get('calendar'));
                break;

            case 'cashbox':
                $returned_title[] = array($languageService->get('cash_box'));
                break;

            #case 'challenge':
            #    $returned_title[] = array($languageService->get('challenge']);
            #    break;

            case 'clanwars':
                if ($action == "stats") {
                    $returned_title[] = array(
                        $languageService->get('clanwars'),
                        nx_seo_url('index.php?site=clanwars')
                    );
                    $returned_title[] = array($languageService->get('stats'));
                } else {
                    $returned_title[] = array($languageService->get('clanwars'));
                }
                break;

            case 'clanwars_details':
                if (isset($parameters['cwID'])) {
                    $cwID = (int)$parameters['cwID'];
                } else {
                    $cwID = '';
                }
                $get = mysqli_fetch_array(
                    safe_query("SELECT opponent FROM plugins_clanwars WHERE cwID=" . (int)$cwID)
                );
                $returned_title[] = array(
                    $languageService->get('clanwars'),
                    nx_seo_url('index.php?site=clanwars')
                );
                $returned_title[] = array($languageService->get('clanwars_details'));
                $returned_title[] = array($get['opponent']);
                break;

            

            case 'counter_stats':
                $returned_title[] = array($languageService->get('stats'));
                break;

            case 'faq':
                if (isset($parameters['faqcatID'])) {
                    $faqcatID = (int)$parameters['faqcatID'];
                } else {
                    $faqcatID = 0;
                }
                if (isset($parameters['faqID'])) {
                    $faqID = (int)$parameters['faqID'];
                } else {
                    $faqID = '';
                }
                $get = mysqli_fetch_array(
                    safe_query(
                        "SELECT faqcatname FROM plugins_faq_categories WHERE faqcatID=" . (int)$faqcatID
                    )
                );
                $get2 = mysqli_fetch_array(
                    safe_query("SELECT question FROM plugins_faq WHERE faqID=" . (int)$faqID)
                );
                if ($action == "faqcat") {
                    $returned_title[] = array(
                        $languageService->get('faq'),
                        nx_seo_url('index.php?site=faq')
                    );
                    $returned_title[] = array($get['faqcatname']);
                } elseif ($action == "faq") {
                    $returned_title[] = array(
                        $languageService->get('faq'),
                        nx_seo_url('index.php?site=faq')
                    );
                    $returned_title[] = array(
                        $get['faqcatname'],
                        nx_seo_url('index.php?site=faq&action=faqcat&faqcatID=' . $faqcatID)
                    );
                    $returned_title[] = array($get2['question']);
                } else {
                    $returned_title[] = array($languageService->get('faq'));
                }
                break;

            case 'files':
                if (isset($parameters['cat'])) {
                    $cat = (int)$parameters['cat'];
                } else {
                    $cat = '';
                }
                if (isset($parameters['file'])) {
                    $file = (int)$parameters['file'];
                } else {
                    $file = '';
                }
                if (isset($parameters['cat'])) {
                    $cat = mysqli_fetch_array(
                        safe_query(
                            "SELECT
                                filecatID, name
                            FROM
                                plugins_files_categories
                            WHERE
                                filecatID='" . $cat . "'"
                        )
                    );
                    $returned_title[] = array(
                        $languageService->get('files'),
                        nx_seo_url('index.php?site=files')
                    );
                    $returned_title[] = array($cat['name']);
                } elseif (isset($parameters['file'])) {
                    $file = mysqli_fetch_array(
                        safe_query(
                            "SELECT
                                fileID, filecatID, filename
                            FROM
                                plugins_files
                            WHERE
                                fileID=" . (int)$file
                        )
                    );
                    $catname = mysqli_fetch_array(
                        safe_query(
                            "SELECT
                                name
                            FROM
                                plugins_files_categories
                            WHERE
                                filecatID=" . (int)$file['filecatID']
                        )
                    );
                    $returned_title[] = array(
                        $languageService->get('files'),
                        nx_seo_url('index.php?site=files')
                    );
                    $returned_title[] = array(
                        $catname['name'],
                        nx_seo_url('index.php?site=files&cat=' . $cat)
                    );
                    $returned_title[] = array($file['filename']);
                } else {
                    $returned_title[] = array($languageService->get('files'));
                }
                break;

            case 'forum':
                if (isset($parameters['board'])) {
                    $board = (int)$parameters['board'];
                } else {
                    $board = '';
                }
                if (isset($parameters['board'])) {
                    $board = mysqli_fetch_array(
                        safe_query(
                            "SELECT boardID, name FROM plugins_forum_boards WHERE boardID='" . $board . "'"
                        )
                    );
                    $returned_title[] = array(
                        $languageService->get('forum'),
                        nx_seo_url('index.php?site=forum')
                    );
                    $returned_title[] = array($board['name']);
                } else {
                    $returned_title[] = array($languageService->get('forum'));
                }
                break;

            case 'forum_topic':
                if (isset($parameters['topic'])) {
                    $topic = (int)$parameters['topic'];
                } else {
                    $topic = '';
                }
                if (isset($parameters['topic'])) {
                    $topic = mysqli_fetch_array(
                        safe_query(
                            "SELECT
                                topicID, boardID, topic
                            FROM
                                plugins_forum_topics
                            WHERE
                                topicID=" . (int)$topic
                        )
                    );
                    $boardname = mysqli_fetch_array(
                        safe_query(
                            "SELECT name FROM plugins_forum_boards WHERE boardID=" . (int)$topic['boardID']
                        )
                    );
                    $returned_title[] = array(
                        $languageService->get('forum'),
                        nx_seo_url('index.php?site=forum')
                    );
                    $returned_title[] = array(
                        $boardname['name'],
                        nx_seo_url('index.php?site=forum&board=' . $topic['boardID'])
                    );
                    $returned_title[] = array($topic['topic']);
                } else {
                    $returned_title[] = array($languageService->get('forum'));
                }
                break;

            case 'gallery':
                $picID = isset($parameters['id']) ? (int)$parameters['id'] : (isset($parameters['picID']) ? (int)$parameters['picID'] : 0);

                if ($picID > 0) {
                    $pic = mysqli_fetch_assoc(
                        safe_query("SELECT id, filename, title, caption, alt_text, upload_date FROM plugins_gallery WHERE id = $picID")
                    );

                    $returned_title[] = [
                        $languageService->get('gallery'),
                        nx_seo_url('index.php?site=gallery')
                    ];

                    if (!empty($pic['title'])) {
                        $returned_title[] = [$pic['title']];
                    } elseif (!empty($pic['alt_text'])) {
                        $returned_title[] = [$pic['alt_text']];
                    } elseif (!empty($pic['filename'])) {
                        $returned_title[] = [$pic['filename']];
                    }

                    if (!empty($pic['caption'])) {
                        $desc = strip_tags((string)$pic['caption']);
                        $desc = trim((string)preg_replace('/\s+/', ' ', $desc));
                        if (strlen($desc) > 160) {
                            $desc = substr($desc, 0, 157) . '...';
                        }
                        if ($desc !== '') {
                            $metadata['description'] = $desc;
                        }
                    } elseif (!empty($pic['alt_text'])) {
                        $metadata['description'] = trim((string)$pic['alt_text']);
                    }

                    if (!empty($pic['filename'])) {
                        $metadata['image'] = '/includes/plugins/gallery/images/upload/' . rawurlencode((string)$pic['filename']);
                    }

                    if (!empty($pic['upload_date'])) {
                        $ts = strtotime((string)$pic['upload_date']);
                        if ($ts !== false) {
                            $metadata['published_time'] = date(DATE_ATOM, $ts);
                            $metadata['modified_time'] = date(DATE_ATOM, $ts);
                        }
                    }
                } else {
                    $returned_title[] = [$languageService->get('gallery')];
                }
                break;

            case 'guestbook':
                $returned_title[] = array($languageService->get('guestbook'));
                break;

            case 'history':
                $returned_title[] = array($languageService->get('history'));
                break;

            case 'imprint':
                $returned_title[] = array($languageService->get('imprint'));
                break;

            case 'joinus':
                $returned_title[] = array($languageService->get('joinus'));
                break;

            case 'links':
                $category_id = isset($parameters['category_id']) ? (int)$parameters['category_id'] : 0;
                $link_id = isset($parameters['link_id']) ? (int)$parameters['link_id'] : 0;

                // Kategorie-Titel holen
                $category_title = '';
                if ($category_id > 0) {
                    $resCat = safe_query("SELECT title FROM plugins_links_categories WHERE id = $category_id");
                    if ($rowCat = mysqli_fetch_assoc($resCat)) {
                        $category_title = $rowCat['title'];
                    }
                }

                // Link-Daten holen (Titel, Beschreibung)
                $link_title = '';
                $link_description = '';
                if ($link_id > 0) {
                    $resLink = safe_query("SELECT title, description FROM plugins_links WHERE id = $link_id");
                    if ($rowLink = mysqli_fetch_assoc($resLink)) {
                        $link_title = $rowLink['title'];
                        $link_description = $rowLink['description'];
                    }
                }

                if ($action === "category") {
                    $returned_title[] = [
                        $languageService->get('links'),
                        nx_seo_url('index.php?site=links')
                    ];
                    if ($category_title !== '') {
                        $returned_title[] = [$category_title];
                    }
                } elseif ($action === "link") {
                    $returned_title[] = [
                        $languageService->get('links'),
                        nx_seo_url('index.php?site=links')
                    ];
                    if ($category_title !== '') {
                        $returned_title[] = [
                            $category_title,
                            nx_seo_url('index.php?site=links&action=category&category_id=' . $category_id)
                        ];
                    }
                    if ($link_title !== '') {
                        $returned_title[] = [$link_title];
                    }

                    // Meta Description aus Beschreibung, gekürzt auf 160 Zeichen
                    if ($link_description !== '') {
                        $desc_excerpt = strip_tags($link_description);
                        if (mb_strlen($desc_excerpt) > 160) {
                            $desc_excerpt = mb_substr($desc_excerpt, 0, 157) . '...';
                        }
                        $metadata['description'] = $desc_excerpt;
                    }

                } else {
                    $returned_title[] = [$languageService->get('links')];
                }
                break;





            case 'linkus':
                $returned_title[] = array($languageService->get('linkus'));
                break;

            case 'contact':
                $returned_title[] = array($languageService->get('contact'));
                break;

            case 'login':
                $returned_title[] = [$languageService->get('login')];
                break;

            case 'loginoverview':
                $returned_title[] = [$languageService->get('loginoverview')];
                break;

            case 'lostpassword':
                $returned_title[] = [$languageService->get('lostpassword')];
                break;

            case 'register':
                $returned_title[] = [$languageService->get('register')];
                break;


            case 'members':
                if (isset($parameters['squadID'])) {
                    $squadID = (int)$parameters['squadID'];
                } else {
                    $squadID = '';
                }
                if ($action == "show") {
                    $get = mysqli_fetch_array(
                        safe_query("SELECT name FROM plugins_squads WHERE squadID=" . (int)$squadID)
                    );
                    $returned_title[] = array(
                        $languageService->get('members'),
                        nx_seo_url('index.php?site=members')
                    );
                    $returned_title[] = array($get['name']);
                } else {
                    $returned_title[] = array($languageService->get('members'));
                }
                break;

            case 'messenger':
                $returned_title[] = array($languageService->get('messenger'));
                break;

            case 'myprofile':
                $returned_title[] = array($languageService->get('myprofile'));
                break;

            case 'news':
                if ($action == "archive") {
                    $returned_title[] = array(
                        $languageService->get('news'),
                        nx_seo_url('index.php?site=news')
                    );
                    $returned_title[] = array($languageService->get('archive'));
                } else {
                    $returned_title[] = array($languageService->get('news'));
                }
                break;

            case 'news_contents':
                if (isset($parameters['rubricID'])) {
                    $rubricID = (int)$parameters['rubricID'];
                } else {
                    $rubricID = 0;
                }
                if (isset($parameters['newsID'])) {
                    $newsID = (int)$parameters['newsID'];
                } else {
                    $newsID = '';
                }
                $get = mysqli_fetch_array(
                    safe_query(
                        "SELECT rubric FROM plugins_news_rubrics WHERE rubricID=" . (int)$rubricID)
                );
                $get2 = mysqli_fetch_array(
                    safe_query("SELECT headline FROM plugins_news WHERE newsID=" . (int)$newsID)
                );
                if ($action == "newscat") {
                    $returned_title[] = array(
                        $languageService->get('news'),
                        nx_seo_url('index.php?site=news')
                    );
                    $returned_title[] = array($get['rubric']);
                } elseif ($action == "news") {
                    $returned_title[] = array(
                        $languageService->get('news'),
                        nx_seo_url('index.php?site=news')
                    );

                    $returned_title[] = array(
                        $get['rubric'],
                        nx_seo_url('index.php?site=news_contents&action=newscat&rubricID=' . $rubricID)
                        
                    );
                    $returned_title[] = array($get2['headline']);
                } else {
                    $returned_title[] = array($languageService->get('news'));
                    $returned_title[] = array($get2['headline']);
                   
                }
                break;

            case 'newsletter':
                $returned_title[] = array($languageService->get('newsletter'));
                break;

            case 'partners':
                $partnerID = isset($parameters['partnerID']) ? (int)$parameters['partnerID'] : 0;

                if ($partnerID > 0) {
                    // Partner-Daten aus DB holen
                    $res = safe_query("SELECT name, description FROM plugins_partners WHERE id = " . $partnerID . " AND active = 1");
                    if ($partner = mysqli_fetch_assoc($res)) {
                        // Title für Breadcrumb / Navigation
                        $returned_title[] = [
                            $languageService->get('partners'),
                            nx_seo_url('index.php?site=partners')
                        ];
                        $returned_title[] = [$partner['name']];

                        // Meta Description (kurz, aus description)
                        if (!empty($partner['description'])) {
                            $desc = strip_tags($partner['description']);
                            if (strlen($desc) > 160) {
                                $desc = substr($desc, 0, 157) . '...';
                            }
                            $metadata['description'] = $desc;
                        }

                    } else {
                        // Partner nicht gefunden
                        $returned_title[] = [$languageService->get('partners')];
                    }
                } else {
                    // Startseite oder Übersicht Partners
                    $returned_title[] = [$languageService->get('partners')];
                }
                break;

            case 'startpage':
                $pageID = isset($parameters['pageID']) ? (int)$parameters['pageID'] : 0;

                $title = '';
                $startpage_text = '';

                if ($pageID > 0) {
                    $result = safe_query("SELECT title, startpage_text FROM settings_startpage WHERE pageID = $pageID");
                    if ($row = mysqli_fetch_assoc($result)) {
                        $title = $row['title'];
                        $startpage_text = $row['startpage_text'];
                    }
                }

                // Titel fürs Breadcrumb oder so
                if ($title) {
                    $returned_title[] = [$title];
                } else {
                    $returned_title[] = ['Startseite'];
                }

                // Meta Description aus Text (HTML-Tags entfernen, auf max 160 Zeichen kürzen)
                if ($startpage_text) {
                    $desc = strip_tags($startpage_text);
                    if (mb_strlen($desc) > 160) {
                        $desc = mb_substr($desc, 0, 157) . '...';
                    }
                    $metadata['description'] = $desc;
                }
                break;





            case 'polls':
                if (isset($parameters['vote'])) {
                    $vote = (int)$parameters['vote'];
                } else {
                    $vote = '';
                }
                if (isset($parameters['pollID'])) {
                    $pollID = (int)$parameters['pollID'];
                } else {
                    $pollID = '';
                }
                if (isset($parameters['vote'])) {
                    $vote = mysqli_fetch_array(
                        safe_query("SELECT titel FROM plugins_polls WHERE pollID=" . (int)$vote)
                    );
                    $returned_title[] = array(
                        $languageService->get('polls'),
                        nx_seo_url('index.php?site=polls')
                    );
                    $returned_title[] = array($vote['titel']);
                } elseif (isset($parameters['pollID'])) {
                    $pollID = mysqli_fetch_array(
                        safe_query("SELECT titel FROM plugins_polls WHERE pollID=" . (int)$pollID)
                    );
                    $returned_title[] = array(
                        $languageService->get('polls'),
                        nx_seo_url('index.php?site=polls')
                    );
                    $returned_title[] = array($pollID['titel']);
                } else {
                    $returned_title[] = array($languageService->get('polls'));
                }
                break;

            case 'profile':
                $id = isset($parameters['id']) ? (int)$parameters['id'] : 0;

                $returned_title[] = [$languageService->get('profile')];

                if ($id > 0) {
                    $username = getusername($id);
                    if ($username) {
                        $returned_title[] = [$username];
                    }
                }
                break;


            case 'userlist':
                $returned_title[] = [$languageService->get('userlist')];
                // Optional: Meta-Daten, falls vorhanden
                break;


            case 'search':
                $returned_title[] = array($languageService->get('search'));
                break;

            case 'servers':
                $returned_title[] = array($languageService->get('servers'));
                break;

            case 'shoutbox':
                $returned_title[] = array($languageService->get('shoutbox'));
                break;

            case 'sponsors':
                $returned_title[] = [$languageService->get('sponsors')];

                if (isset($parameters['sponsorID'])) {
                    $sponsorID = (int)$parameters['sponsorID'];

                    // Name und optionale Beschreibung holen
                    $sponsor = mysqli_fetch_assoc(
                        safe_query("SELECT name, description FROM plugins_sponsors WHERE id = $sponsorID AND active = 1")
                    );

                    if (!empty($sponsor['name'])) {
                        $returned_title[] = [$sponsor['name']];
                    }

                    // Meta Description aus Beschreibung, falls vorhanden
                    if (!empty($sponsor['description'])) {
                        $desc = strip_tags($sponsor['description']);
                        if (strlen($desc) > 160) {
                            $desc = substr($desc, 0, 157) . '...';
                        }
                        $metadata['description'] = $desc;
                    }

                }
                break;



            case 'planning':
                $returned_title[] = array($languageService->get('planning'));
                break;    

            case 'squads':
                if (isset($parameters['squadID'])) {
                    $squadID = (int)$parameters['squadID'];
                } else {
                    $squadID = '';
                }
                if ($action == "show") {
                    $get = mysqli_fetch_array(
                        safe_query("SELECT name FROM plugins_squads WHERE squadID=" . (int)$squadID)
                    );
                    $returned_title[] = array(
                        $languageService->get('squads'),
                        nx_seo_url('index.php?site=squads')
                    );
                    $returned_title[] = array($get['name']);
                } else {
                    $returned_title[] = array($languageService->get('squads'));
                }
                break;

            case 'static':
                $staticID = isset($parameters['staticID']) ? (int)$parameters['staticID'] : 0;
                $lang = $_SESSION['language'] ?? 'de';
                $safeLang = mysqli_real_escape_string($_database, (string)$lang);

                $titleRow = mysqli_fetch_assoc(safe_query("
                    SELECT content
                    FROM settings_content_lang
                    WHERE content_key = 'static_title_{$staticID}'
                      AND language IN ('{$safeLang}', 'de')
                    ORDER BY FIELD(language, '{$safeLang}', 'de')
                    LIMIT 1
                ")) ?: [];

                $contentRow = mysqli_fetch_assoc(safe_query("
                    SELECT content
                    FROM settings_content_lang
                    WHERE content_key = 'static_{$staticID}'
                      AND language IN ('{$safeLang}', 'de')
                    ORDER BY FIELD(language, '{$safeLang}', 'de')
                    LIMIT 1
                ")) ?: [];

                if (!empty($titleRow['content'])) {
                    $returned_title[] = [(string)$titleRow['content']];
                } else {
                    $returned_title[] = [$languageService->get('static')];
                }

                if (!empty($contentRow['content'])) {
                    $desc = strip_tags((string)$contentRow['content']);
                    $desc = trim(preg_replace('/\s+/', ' ', $desc));
                    if (strlen($desc) > 160) {
                        $desc = substr($desc, 0, 157) . '...';
                    }
                    $metadata['description'] = $desc;
                }

                break;


            case 'usergallery':
                $returned_title[] = array($languageService->get('usergallery'));
                break;
# neu Anfang
            case 'todo':
                $returned_title[] = array($languageService->get('todo'));
                break;

            case 'news_archive':
                $returned_title[] = array($languageService->get('news_archive'));
                break; 

            case 'privacy_policy':
                $privacyPolicyID = isset($parameters['privacy_policyID']) ? (int)$parameters['privacy_policyID'] : 0;

                $get = [];
                $privacyText = '';
                $currentLang = strtolower((string)$languageService->detectLanguage());
                if ($currentLang === '') {
                    $currentLang = 'de';
                }

                $hasSettingsContentLang = false;
                $resContentLang = $_database->query("SHOW TABLES LIKE 'settings_content_lang'");
                if ($resContentLang && $resContentLang->num_rows > 0) {
                    $hasSettingsContentLang = true;
                }

                if ($hasSettingsContentLang) {
                    $langOrder = array_values(array_unique([$currentLang, 'en', 'de', 'it']));
                    foreach ($langOrder as $langIso) {
                        $stmt = $_database->prepare("
                            SELECT content
                            FROM settings_content_lang
                            WHERE content_key = 'privacy_policy' AND language = ?
                            LIMIT 1
                        ");
                        if ($stmt) {
                            $stmt->bind_param('s', $langIso);
                            $stmt->execute();
                            $stmt->bind_result($privacyText);
                            $stmt->fetch();
                            $stmt->close();
                        }
                        $privacyText = trim((string)$privacyText);
                        if ($privacyText !== '') {
                            break;
                        }
                    }
                } else {
                    $hasLegacyPrivacyTable = false;
                    $resLegacy = $_database->query("SHOW TABLES LIKE 'settings_privacy_policy'");
                    if ($resLegacy && $resLegacy->num_rows > 0) {
                        $hasLegacyPrivacyTable = true;
                    }

                    if ($hasLegacyPrivacyTable) {
                        if ($privacyPolicyID > 0) {
                            $get = mysqli_fetch_assoc(
                                safe_query("SELECT privacy_policy_text FROM settings_privacy_policy WHERE privacy_policyID = " . $privacyPolicyID . " LIMIT 1")
                            ) ?: [];
                        }
                        if (empty($get['privacy_policy_text'])) {
                            $get = mysqli_fetch_assoc(
                                safe_query("SELECT privacy_policy_text FROM settings_privacy_policy ORDER BY privacy_policyID DESC LIMIT 1")
                            ) ?: [];
                        }
                        $privacyText = trim((string)($get['privacy_policy_text'] ?? ''));
                    }
                }

                $returned_title[] = [$languageService->get('privacy_policy')];

                if ($privacyText !== '') {
                    // Meta Description aus Datenschutztext (HTML entfernen, auf max. 160 Zeichen k�rzen)
                    $desc = strip_tags($privacyText);
                    $desc = trim(preg_replace('/\s+/', ' ', $desc));
                    if (strlen($desc) > 160) {
                        $desc = substr($desc, 0, 157) . '...';
                    }
                    $metadata['description'] = $desc;
                }

                // Optional: Keywords, wenn du welche hinterlegen willst (z.B. Tags)

                break;

            case 'candidature':
                $returned_title[] = array($languageService->get('candidature'));
                break; 

            case 'twitter':
                $returned_title[] = array($languageService->get('twitter'));
                break; 

            case 'discord':
                $returned_title[] = array($languageService->get('discord'));
                break;
                
            case 'portfolio':
                $returned_title[] = array($languageService->get('portfolio'));
                break;
                
            case 'streams':
                $returned_title[] = array($languageService->get('streams'));
                break;
                
            case 'server_rules':
                $returned_title[] = array($languageService->get('server_rules'));
                break; 
                
            case 'clan_rules':
                $clanRulesID = isset($parameters['id']) ? (int)$parameters['id'] : 0;

                $get = mysqli_fetch_assoc(
                    safe_query("SELECT title, text FROM plugins_clan_rules WHERE id = " . $clanRulesID . " AND displayed = '1'")
                );

                $returned_title[] = [$languageService->get('clan_rules')];

                if (!empty($get['title'])) {
                    $returned_title[] = [$get['title']];
                }

                if (!empty($get['text'])) {
                    // Meta Description aus dem Text (HTML entfernen, auf max. 160 Zeichen kürzen)
                    $desc = strip_tags($get['text']);
                    $desc = trim(preg_replace('/\s+/', ' ', $desc));
                    if (strlen($desc) > 160) {
                        $desc = substr($desc, 0, 157) . '...';
                    }
                    $metadata['description'] = $desc;
                }

                // Optional: Keywords, falls Tags verwendet werden

                break;

                

            case 'videos':
                if (isset($parameters['videoscatID'])) {
                    $videoscatID = (int)$parameters['videoscatID'];
                } else {
                    $videoscatID = 0;
                }
                if (isset($parameters['videosID'])) {
                    $videosID = (int)$parameters['videosID'];
                } else {
                    $videosID = '';
                }
                $get = mysqli_fetch_array(
                    safe_query(
                        "SELECT catname FROM plugins_videos_categories WHERE videoscatID=" . (int)$videoscatID
                    )
                );
                $get2 = mysqli_fetch_array(
                    safe_query("SELECT videoname FROM plugins_videos WHERE videosID=" . (int)$videosID)
                );
                if ($action == "watch") {
                    $returned_title[] = array(
                        $languageService->get('videos'),
                        nx_seo_url('index.php?site=videos')
                    );
                    #$returned_title[] = array($get['catname']);
                } elseif ($action == "videos") {
                    $returned_title[] = array(
                        $languageService->get('videos'),
                        nx_seo_url('index.php?site=videos')
                    );
                    $returned_title[] = array(
                        $get['catname'],
                        nx_seo_url('index.php?site=videos&action=watch&videoscatID=' . $videoscatID)
                    );
                    $returned_title[] = array($get2['videoname']);
                } else {
                    $returned_title[] = array($languageService->get('videos'));
                    #$returned_title[] = array($get2['videoname']);
                }
                break; 


            case 'blog':
                if (isset($parameters['blogID'])) {
                    $blogID = (int)$parameters['blogID'];
                } else {
                    $blogID = 0;
                }
                $get2 = mysqli_fetch_array(
                    safe_query(
                        "SELECT headline FROM plugins_blog WHERE blogID=" . (int)$blogID)
                    );
                if ($action == "show") {
                    $get = mysqli_fetch_array(
                        safe_query("SELECT headline FROM plugins_blog WHERE blogID=" . (int)$blogID)
                    );
                    $returned_title[] = array(
                        $languageService->get('blog'),
                        nx_seo_url('index.php?site=blog')
                    );
                    $returned_title[] = array($get['headline']);

                } elseif ($action == "blog") {
                    $returned_title[] = array(
                        $languageService->get('blog'),
                        nx_seo_url('index.php?site=blog')
                    );

                    $returned_title[] = array(
                        $languageService->get('blog'),
                        nx_seo_url('index.php?site=blog&action=archiv')
                    );

                    $returned_title[] = array(
                        $languageService->get('blog'),
                        nx_seo_url('index.php?site=blog&action=show&blogID=' . $blogID)
                    );

                    $returned_title[] = array(
                        $languageService->get('blog'),
                        nx_seo_url('index.php?site=blog&action=archiv&userID=' . $userID)
                    );

                    $returned_title[] = array($get['headline']);
                    $returned_title[] = array($languageService->get('archive'));
                } else {
                    $returned_title[] = array($languageService->get('blog'));
                    $returned_title[] = array($languageService->get('archive'));
                }
                break;              
# neu ENDE
            case 'whoisonline':
                $returned_title[] = array($languageService->get('whoisonline'));
                break;

            default:
                $returned_title[] = array($languageService->get('news'));
                break;
        }
    } else {
        $returned_title[] = array($languageService->get('news'));
    }
    return array('titles' => $returned_title, 'metatags' => $metadata);
}
