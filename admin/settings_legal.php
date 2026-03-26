<?php
use nexpell\AccessControl;
use nexpell\Captcha;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $_database, $languageService;

$languageService->readModule('settings_legal', true);

$sectionRights = [
    'privacy_policy'   => 'ac_privacy_policy',
    'terms_of_service' => 'ac_terms_of_service',
    'cookie_policy'    => 'ac_privacy_policy',
    'imprint'          => 'ac_imprint',
];

$sectionMeta = [
    'privacy_policy' => [
        'icon'        => 'bi-shield-check',
        'title_key'   => 'privacy_policy',
        'description' => 'legal_privacy_help',
    ],
    'terms_of_service' => [
        'icon'        => 'bi-file-text',
        'title_key'   => 'terms_of_service',
        'description' => 'legal_terms_help',
    ],
    'cookie_policy' => [
        'icon'        => 'bi-cookie',
        'title_key'   => 'cookie_policy',
        'description' => 'legal_cookie_help',
    ],
    'imprint' => [
        'icon'        => 'bi-building',
        'title_key'   => 'imprint',
        'description' => 'legal_imprint_help',
    ],
];

function nx_legal_escape(string $value): string
{
    global $_database;

    return $_database->real_escape_string($value);
}

function nx_legal_has_access(string $moduleName): bool
{
    global $_database;

    $userID = (int)($_SESSION['userID'] ?? 0);
    if ($userID <= 0) {
        return false;
    }

    $stmt = $_database->prepare("
        SELECT 1
        FROM user_role_assignments ur
        JOIN user_role_admin_navi_rights ar ON ar.roleID = ur.roleID
        WHERE ur.userID = ?
          AND ar.modulname = ?
        LIMIT 1
    ");

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('is', $userID, $moduleName);
    $stmt->execute();
    $stmt->store_result();
    $hasAccess = $stmt->num_rows > 0;
    $stmt->close();

    return $hasAccess;
}

function nx_legal_h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function nx_legal_text(string $key): string
{
    global $languageService;

    return (string)$languageService->get($key);
}

function nx_legal_description(string $key): string
{
    $text = nx_legal_text($key);
    return $text !== '[' . $key . ']' ? $text : '';
}

function nx_legal_lang_buttons(array $languages, string $currentLang): string
{
    $out = '<div class="btn-group" id="lang-switch">';
    foreach ($languages as $iso => $label) {
        $activeClass = $iso === $currentLang ? 'btn-primary' : 'btn-secondary';
        $out .= '<button type="button" class="btn ' . $activeClass . '" data-lang="' . nx_legal_h($iso) . '">'
            . nx_legal_h(strtoupper($iso))
            . '</button>';
    }
    $out .= '</div>';

    return $out;
}

function nx_legal_hidden_content_fields(array $languages, array $content): string
{
    $out = '';
    foreach ($languages as $iso => $label) {
        $out .= '<input type="hidden" name="content[' . nx_legal_h($iso) . ']" id="content_' . nx_legal_h($iso)
            . '" value="' . nx_legal_h($content[$iso] ?? '') . '">';
    }

    return $out;
}

function nx_legal_render_editor(
    string $sectionKey,
    array $meta,
    array $languages,
    string $currentLang,
    array $content,
    array $lastUpdate,
    string $hash
): void {
    $lastUpdateText = !empty($lastUpdate[$currentLang])
        ? date('d.m.Y H:i', strtotime((string)$lastUpdate[$currentLang]))
        : '-';
    $lastUpdateMap = nx_legal_h(json_encode($lastUpdate, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    ?>
    <form method="post">
        <div class="nx-lang-editor" data-last-update-map="<?= $lastUpdateMap ?>">
            <section class="card shadow-sm border-0">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="card-title mb-1">
                            <i class="bi <?= nx_legal_h($meta['icon']) ?>"></i>
                            <?= nx_legal_h(nx_legal_text($meta['title_key'])) ?>
                        </div>
                        <div class="text-muted small"><?= nx_legal_h(nx_legal_description($meta['description'])) ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <?= nx_legal_lang_buttons($languages, $currentLang) ?>
                        <div class="text-end small text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            <span id="last-update-text"><?= nx_legal_h($lastUpdateText) ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="captcha_hash" value="<?= nx_legal_h($hash) ?>">
                    <input type="hidden" name="section" value="<?= nx_legal_h($sectionKey) ?>">
                    <input type="hidden" name="active_lang" id="active_lang" value="<?= nx_legal_h($currentLang) ?>">
                    <textarea id="nx-editor-main" class="form-control" data-editor="nx_editor" rows="20"><?= nx_legal_h($content[$currentLang] ?? '') ?></textarea>
                    <?= nx_legal_hidden_content_fields($languages, $content) ?>
                    <div class="mt-3">
                        <button type="submit" name="submit_section" value="1" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?= nx_legal_h(nx_legal_text('save')) ?>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </form>
    <?php
}

$allowedSections = [];
foreach ($sectionRights as $sectionKey => $moduleName) {
    if (nx_legal_has_access($moduleName)) {
        $allowedSections[$sectionKey] = true;
    }
}

if (empty($allowedSections)) {
    AccessControl::checkAdminAccess('ac_imprint');
}

$captcha = new Captcha;

$languages = [];
$resLanguages = $_database->query("SELECT iso_639_1, name_de FROM settings_languages WHERE active = 1 ORDER BY id ASC");
while ($resLanguages && ($row = $resLanguages->fetch_assoc())) {
    $languages[$row['iso_639_1']] = $row['name_de'];
}
if (empty($languages)) {
    $languages = ['de' => 'Deutsch'];
}

$currentLang = null;
if (!empty($_SESSION['legal_active_lang'])) {
    $currentLang = (string)$_SESSION['legal_active_lang'];
    unset($_SESSION['legal_active_lang']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['active_lang'])) {
    $currentLang = (string)$_POST['active_lang'];
} elseif (!empty($_SESSION['language'])) {
    $currentLang = (string)$_SESSION['language'];
} else {
    $currentLang = (string)$languageService->detectLanguage();
}
if (!isset($languages[$currentLang])) {
    $currentLang = (string)array_key_first($languages);
}

$requestedSection = (string)($_GET['section'] ?? $_SESSION['legal_active_section'] ?? '');
unset($_SESSION['legal_active_section']);
if ($requestedSection !== '' && !isset($allowedSections[$requestedSection])) {
    $requestedSection = '';
}

$imprintData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_section'])) {
    $section = (string)($_POST['section'] ?? '');
    $activeLang = (string)($_POST['active_lang'] ?? $currentLang);

    if (!isset($languages[$activeLang])) {
        $activeLang = $currentLang;
    }

    if (isset($allowedSections[$section])) {
        $requestedSection = $section;
    }

    if (!isset($sectionRights[$section]) || empty($allowedSections[$section])) {
        http_response_code(403);
        echo '<div class="alert alert-danger">Zugriff verweigert.</div>';
        exit;
    }

    if (!$captcha->checkCaptcha(0, $_POST['captcha_hash'] ?? '')) {
        nx_redirect('admincenter.php?site=settings_legal&section=' . rawurlencode($section), 'danger', 'alert_transaction_invalid', false);
    }

    if ($section === 'imprint') {
        $type = trim((string)($_POST['type'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $website = trim((string)($_POST['website'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $postalCode = trim((string)($_POST['postal_code'] ?? ''));
        $city = trim((string)($_POST['city'] ?? ''));
        $registerOffice = trim((string)($_POST['register_office'] ?? ''));
        $registerNumber = trim((string)($_POST['register_number'] ?? ''));
        $vatId = trim((string)($_POST['vat_id'] ?? ''));
        $supervisoryAuthority = trim((string)($_POST['supervisory_authority'] ?? ''));

        $companyName = '';
        $representedBy = '';
        $taxId = '';

        if ($type === 'private') {
            $companyName = trim((string)($_POST['company_name_private'] ?? ''));
        } elseif ($type === 'association') {
            $companyName = trim((string)($_POST['company_name_association'] ?? ''));
            $representedBy = trim((string)($_POST['represented_by_association'] ?? ''));
        } elseif ($type === 'small_business') {
            $companyName = trim((string)($_POST['company_name_small_business'] ?? ''));
            $taxId = trim((string)($_POST['tax_id_small_business'] ?? ''));
        } elseif ($type === 'company') {
            $companyName = trim((string)($_POST['company_name_company'] ?? ''));
            $representedBy = trim((string)($_POST['represented_by_company'] ?? ''));
            $taxId = trim((string)($_POST['tax_id_company'] ?? ''));
        }

        $imprintData = [
            'type' => $type,
            'company_name' => $companyName,
            'represented_by' => $representedBy,
            'tax_id' => $taxId,
            'email' => $email,
            'website' => $website,
            'phone' => $phone,
            'address' => $address,
            'postal_code' => $postalCode,
            'city' => $city,
            'register_office' => $registerOffice,
            'register_number' => $registerNumber,
            'vat_id' => $vatId,
            'supervisory_authority' => $supervisoryAuthority,
        ];

        $errors = [];
        if ($type === '') {
            $errors[] = nx_legal_text('give_type');
        }
        if (in_array($type, ['private', 'association', 'small_business', 'company'], true) && $companyName === '') {
            $errors[] = nx_legal_text('need_name');
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = nx_legal_text('need_email');
        }
        if ($address === '') {
            $errors[] = nx_legal_text('need_address');
        }
        if ($postalCode === '') {
            $errors[] = nx_legal_text('need_plz');
        }
        if ($city === '') {
            $errors[] = nx_legal_text('need_location');
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                nx_alert('danger', $error, false, true, true);
            }
        } else {
            $_database->query("
                UPDATE settings_imprint SET
                    type='" . nx_legal_escape($type) . "',
                    company_name='" . nx_legal_escape($companyName) . "',
                    represented_by='" . nx_legal_escape($representedBy) . "',
                    tax_id='" . nx_legal_escape($taxId) . "',
                    email='" . nx_legal_escape($email) . "',
                    website='" . nx_legal_escape($website) . "',
                    phone='" . nx_legal_escape($phone) . "',
                    address='" . nx_legal_escape($address) . "',
                    postal_code='" . nx_legal_escape($postalCode) . "',
                    city='" . nx_legal_escape($city) . "',
                    register_office='" . nx_legal_escape($registerOffice) . "',
                    register_number='" . nx_legal_escape($registerNumber) . "',
                    vat_id='" . nx_legal_escape($vatId) . "',
                    supervisory_authority='" . nx_legal_escape($supervisoryAuthority) . "'
                LIMIT 1
            ");

            $contentValue = trim((string)($_POST['content'][$activeLang] ?? ''));
            $_database->query("
                INSERT INTO settings_content_lang (content_key, language, content, updated_at)
                VALUES ('imprint', '" . nx_legal_escape($activeLang) . "', '" . nx_legal_escape($contentValue) . "', NOW())
                ON DUPLICATE KEY UPDATE
                    content = VALUES(content),
                    updated_at = NOW()
            ");

            $_SESSION['legal_active_lang'] = $activeLang;
            $_SESSION['legal_active_section'] = 'imprint';
            nx_audit_update('settings_imprint', null, true, null, 'admincenter.php?site=settings_legal&section=imprint');
            nx_redirect('admincenter.php?site=settings_legal&section=imprint', 'success', 'alert_saved', false);
        }
    } else {
        $contentValue = trim((string)($_POST['content'][$activeLang] ?? ''));
        $_database->query("
            INSERT INTO settings_content_lang (content_key, language, content, updated_at)
            VALUES ('" . nx_legal_escape($section) . "', '" . nx_legal_escape($activeLang) . "', '" . nx_legal_escape($contentValue) . "', NOW())
            ON DUPLICATE KEY UPDATE
                content = VALUES(content),
                updated_at = NOW()
        ");

        $_SESSION['legal_active_lang'] = $activeLang;
        $_SESSION['legal_active_section'] = $section;
        nx_audit_update('settings_content_lang', null, true, null, 'admincenter.php?site=settings_legal&section=' . $section);
        nx_redirect('admincenter.php?site=settings_legal&section=' . rawurlencode($section), 'success', 'alert_saved', false);
    }
}

$contentBySection = [];
$lastUpdateBySection = [];
$contentKeysSql = "'" . implode("','", array_map('nx_legal_escape', array_keys($sectionMeta))) . "'";
$resContent = $_database->query("
    SELECT content_key, language, content, updated_at
    FROM settings_content_lang
    WHERE content_key IN ($contentKeysSql)
");
while ($resContent && ($row = $resContent->fetch_assoc())) {
    $key = (string)$row['content_key'];
    $lang = (string)$row['language'];
    $contentBySection[$key][$lang] = $row['content'];
    $lastUpdateBySection[$key][$lang] = $row['updated_at'];
}

if (empty($imprintData)) {
    $resImprint = $_database->query("SELECT * FROM settings_imprint LIMIT 1");
    if ($resImprint && ($row = $resImprint->fetch_assoc())) {
        $imprintData = $row;
    }
}

$captcha->createTransaction();
$hash = $captcha->getHash();
?>
<div class="card shadow-sm border-0 mb-4 mt-3">
    <div class="card-header d-flex align-items-center">
        <div class="card-title mb-0">
            <i class="bi bi-shield-check"></i> <?= nx_legal_h(nx_legal_text('legal_center')) ?>
        </div>
        <?php if ($requestedSection !== ''): ?>
            <div class="ms-auto">
                <a href="admincenter.php?site=settings_legal" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> <?= nx_legal_h(nx_legal_text('legal_back_overview')) ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <p class="mb-2"><?= nx_legal_h(nx_legal_text('legal_center_intro')) ?></p>
        <p class="mb-0 text-muted small"><?= nx_legal_h(nx_legal_text('legal_center_overview_text')) ?></p>
    </div>
</div>

<?php if ($requestedSection === ''): ?>
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <div class="card-title mb-0">
                <i class="bi bi-list-ul"></i> <?= nx_legal_h(nx_legal_text('legal_center')) ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php foreach (array_keys($allowedSections) as $sectionKey): ?>
                    <?php $meta = $sectionMeta[$sectionKey]; ?>
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="text-muted">
                                <i class="bi <?= nx_legal_h($meta['icon']) ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= nx_legal_h(nx_legal_text($meta['title_key'])) ?></div>
                                <div class="text-muted small"><?= nx_legal_h(nx_legal_description($meta['description'])) ?></div>
                            </div>
                            <div class="ms-auto">
                                <a href="admincenter.php?site=settings_legal&amp;section=<?= nx_legal_h($sectionKey) ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> <?= nx_legal_h(nx_legal_text('legal_open_editor')) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php elseif ($requestedSection !== 'imprint'): ?>
    <?php nx_legal_render_editor(
        $requestedSection,
        $sectionMeta[$requestedSection],
        $languages,
        $currentLang,
        $contentBySection[$requestedSection] ?? [],
        $lastUpdateBySection[$requestedSection] ?? [],
        $hash
    ); ?>
<?php elseif (!empty($allowedSections['imprint'])): ?>
    <?php
    $imprintLastUpdate = $lastUpdateBySection['imprint'] ?? [];
    $imprintLastUpdateText = !empty($imprintLastUpdate[$currentLang])
        ? date('d.m.Y H:i', strtotime((string)$imprintLastUpdate[$currentLang]))
        : '-';
    $imprintLastUpdateMap = nx_legal_h(json_encode($imprintLastUpdate, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    ?>
    <form method="post">
        <div class="nx-lang-editor" data-last-update-map="<?= $imprintLastUpdateMap ?>">
            <section class="card shadow-sm border-0">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="card-title mb-1">
                            <i class="bi bi-building"></i>
                            <?= nx_legal_h(nx_legal_text('imprint')) ?>
                        </div>
                        <div class="text-muted small"><?= nx_legal_h(nx_legal_description('legal_imprint_help')) ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <?= nx_legal_lang_buttons($languages, $currentLang) ?>
                        <div class="text-end small text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            <span id="last-update-text"><?= nx_legal_h($imprintLastUpdateText) ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="captcha_hash" value="<?= nx_legal_h($hash) ?>">
                    <input type="hidden" name="section" value="imprint">
                    <input type="hidden" name="active_lang" id="active_lang" value="<?= nx_legal_h($currentLang) ?>">

                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="border rounded p-3 bg-light-subtle">
                                <div class="mb-3">
                                    <label for="type" class="form-label"><?= nx_legal_h(nx_legal_text('type_label')) ?></label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">- <?= nx_legal_h(nx_legal_text('select_type')) ?> -</option>
                                        <option value="private" <?= ($imprintData['type'] ?? '') === 'private' ? 'selected' : '' ?>><?= nx_legal_h(nx_legal_text('private_person')) ?></option>
                                        <option value="association" <?= ($imprintData['type'] ?? '') === 'association' ? 'selected' : '' ?>><?= nx_legal_h(nx_legal_text('association')) ?></option>
                                        <option value="small_business" <?= ($imprintData['type'] ?? '') === 'small_business' ? 'selected' : '' ?>><?= nx_legal_h(nx_legal_text('small_business_owner')) ?></option>
                                        <option value="company" <?= ($imprintData['type'] ?? '') === 'company' ? 'selected' : '' ?>><?= nx_legal_h(nx_legal_text('company')) ?></option>
                                    </select>
                                </div>

                                <div id="private_fields" class="type_block mb-3" style="display:none;">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('name_private')) ?></label>
                                    <input type="text" name="company_name_private" value="<?= nx_legal_h($imprintData['company_name'] ?? '') ?>" class="form-control">
                                </div>

                                <div id="association_fields" class="type_block mb-3" style="display:none;">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('name_association')) ?></label>
                                    <input type="text" name="company_name_association" value="<?= nx_legal_h($imprintData['company_name'] ?? '') ?>" class="form-control mb-2">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('represented_by_association')) ?></label>
                                    <input type="text" name="represented_by_association" value="<?= nx_legal_h($imprintData['represented_by'] ?? '') ?>" class="form-control">
                                </div>

                                <div id="small_business_fields" class="type_block mb-3" style="display:none;">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('name_small_business')) ?></label>
                                    <input type="text" name="company_name_small_business" value="<?= nx_legal_h($imprintData['company_name'] ?? '') ?>" class="form-control mb-2">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('tax_id_small_business')) ?></label>
                                    <input type="text" name="tax_id_small_business" value="<?= nx_legal_h($imprintData['tax_id'] ?? '') ?>" class="form-control">
                                </div>

                                <div id="company_fields" class="type_block mb-3" style="display:none;">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('name_company')) ?></label>
                                    <input type="text" name="company_name_company" value="<?= nx_legal_h($imprintData['company_name'] ?? '') ?>" class="form-control mb-2">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('represented_by_company')) ?></label>
                                    <input type="text" name="represented_by_company" value="<?= nx_legal_h($imprintData['represented_by'] ?? '') ?>" class="form-control mb-2">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('tax_id_company')) ?></label>
                                    <input type="text" name="tax_id_company" value="<?= nx_legal_h($imprintData['tax_id'] ?? '') ?>" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('email')) ?></label>
                                    <input type="email" class="form-control" name="email" value="<?= nx_legal_h($imprintData['email'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('website')) ?></label>
                                    <input type="text" class="form-control" name="website" value="<?= nx_legal_h($imprintData['website'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('phone')) ?></label>
                                    <input type="text" class="form-control" name="phone" value="<?= nx_legal_h($imprintData['phone'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('address')) ?></label>
                                    <input type="text" class="form-control" name="address" value="<?= nx_legal_h($imprintData['address'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?= nx_legal_h(nx_legal_text('postal_code')) ?> / <?= nx_legal_h(nx_legal_text('city')) ?></label>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control w-25" name="postal_code" value="<?= nx_legal_h($imprintData['postal_code'] ?? '') ?>" required>
                                        <input type="text" class="form-control w-75" name="city" value="<?= nx_legal_h($imprintData['city'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div id="register_fields" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label"><?= nx_legal_h(nx_legal_text('register_office')) ?></label>
                                        <input type="text" class="form-control" name="register_office" value="<?= nx_legal_h($imprintData['register_office'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?= nx_legal_h(nx_legal_text('register_number')) ?></label>
                                        <input type="text" class="form-control" name="register_number" value="<?= nx_legal_h($imprintData['register_number'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?= nx_legal_h(nx_legal_text('vat_id')) ?></label>
                                        <input type="text" class="form-control" name="vat_id" value="<?= nx_legal_h($imprintData['vat_id'] ?? '') ?>">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label"><?= nx_legal_h(nx_legal_text('supervisory_authority')) ?></label>
                                        <input type="text" class="form-control" name="supervisory_authority" value="<?= nx_legal_h($imprintData['supervisory_authority'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <textarea id="nx-editor-main" class="form-control" data-editor="nx_editor" rows="20"><?= nx_legal_h($contentBySection['imprint'][$currentLang] ?? '') ?></textarea>
                            <?= nx_legal_hidden_content_fields($languages, $contentBySection['imprint'] ?? []) ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="submit_section" value="1" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?= nx_legal_h(nx_legal_text('save')) ?>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </form>

    <script>
    (function () {
        const typeSelect = document.querySelector('select[name="type"]');
        if (!typeSelect) return;

        function toggleFields(type) {
            document.querySelectorAll('.type_block').forEach(block => {
                block.style.display = 'none';
            });

            const registerFields = document.getElementById('register_fields');
            if (registerFields) {
                registerFields.style.display = (type === 'small_business' || type === 'company') ? 'block' : 'none';
            }

            const target = document.getElementById(type + '_fields');
            if (target) {
                target.style.display = 'block';
            }
        }

        toggleFields(typeSelect.value);
        typeSelect.addEventListener('change', function () {
            toggleFields(this.value);
        });
    })();
    </script>
<?php endif; ?>
