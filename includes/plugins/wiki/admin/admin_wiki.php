<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;

// Sprache setzen, falls nicht vorhanden
$_SESSION['language'] = $_SESSION['language'] ?? 'de';

// LanguageService initialisieren
global $languageService;
$lang = $languageService->detectLanguage();
$languageService = new LanguageService($_database);

// Admin-Modul-Sprache laden
$languageService->readPluginModule('wiki');

use nexpell\AccessControl;
// Den Admin-Zugriff für das Modul überprüfen
AccessControl::checkAdminAccess('wiki');

$filepath = $plugin_path."images/article/";

// Parameter aus URL lesen
$action = $_GET['action'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortDir = ($_GET['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

// Max Wiki pro Seite
$perPage = 10;

// Whitelist für Sortierung
$allowedSorts = ['title', 'created_at'];
if (!in_array($sortBy, $allowedSorts)) {
    $sortBy = 'created_at';
}

$uploadDir = __DIR__ . '/../images/'; // für allgemeine Uploads
$plugin_path = __DIR__ . '/../images/'; // für Bannerbild Upload

// --- AJAX-Löschung ---
if (($action ?? '') === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Wikiinformationen laden (inkl. optional Bildname)
    $stmt = $_database->prepare("SELECT banner_image FROM plugins_wiki WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imageFilename);
    if ($stmt->fetch()) {
        $stmt->close();

        // Wiki aus DB löschen
        $stmtDel = $_database->prepare("DELETE FROM plugins_wiki WHERE id = ?");
        $stmtDel->bind_param("i", $id);
        $stmtDel->execute();
        $stmtDel->close();

        // Bilddatei löschen, wenn vorhanden
        if (!empty($imageFilename)) {
            @unlink($plugin_path . $imageFilename);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Wiki nicht gefunden']);
    }
    header("Location: admincenter.php?site=admin_wiki&action=edit&id=".$id);
    exit;
}

if (($action ?? '') === "delete_screenshot") {
    $id    = intval($_GET['id'] ?? 0);
    $index = intval($_GET['index'] ?? -1);

    if ($id > 0 && $index >= 0) {
        // Datensatz laden
        $stmt = $_database->prepare("SELECT screenshots FROM plugins_wiki WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($screenshotsJson);
        $stmt->fetch();
        $stmt->close();   // <-- GANZ WICHTIG!

        $screens = json_decode($screenshotsJson, true) ?? [];
        if (isset($screens[$index])) {
            $file = $plugin_path . $screens[$index];

            if (file_exists($file)) {
                @unlink($file);
            }

            unset($screens[$index]);
            $screens = array_values($screens);

            $newJson = json_encode($screens);

            $upd = $_database->prepare("UPDATE plugins_wiki SET screenshots = ? WHERE id = ?");
            $upd->bind_param("si", $newJson, $id);
            $upd->execute();
            $upd->close();
        }
    }

    header("Location: admincenter.php?site=admin_wiki&action=edit&id=".$id);
    exit;
}



// --- Wiki hinzufügen / bearbeiten ---
if (($action ?? '') === "add" || ($action ?? '') === "edit") {
    $id = intval($_GET['id'] ?? 0);
    $isEdit = $id > 0;

    // Default-Daten
    $data = [
        'category_id'   => 0,
        'title'         => '',
        'desc_short'    => '',
        'desc_long'     => '',
        'slug'          => '',
        'banner_image'  => '',
        'screenshots'   => '[]',
        'sort_order'    => 0,
        'is_active'     => 0,
    ];

    // Beim Edit vorhandene Daten laden
    if ($isEdit) {
        $stmt = $_database->prepare("
            SELECT category_id, title, desc_short, desc_long, slug, banner_image, screenshots, sort_order, is_active 
            FROM plugins_wiki WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result(
            $data['category_id'], $data['title'], $data['desc_short'], $data['desc_long'],
            $data['slug'], $data['banner_image'], $data['screenshots'], $data['sort_order'],
            $data['is_active']
        );
        if (!$stmt->fetch()) {
            echo "<div class='alert alert-danger'>Wiki nicht gefunden.</div>";
            exit;
        }
        $stmt->close();
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cat        = intval($_POST['category_id'] ?? 0);
        $title      = trim($_POST['title'] ?? '');
        $desc_short = trim($_POST['desc_short'] ?? '');
        $desc_long  = $_POST['desc_long'] ?? '';
        $slug       = trim($_POST['slug'] ?? '');
        $sort_order = intval($_POST['sort_order'] ?? 0);
        $is_active  = isset($_POST['is_active']) ? 1 : 0;

        // Bannerbild
        $filename = $data['banner_image']; // Standard: altes Banner behalten
        if (!empty($_FILES['banner_image']['name'])) {
            $allowedTypes = ['image/jpeg','image/png','image/webp','image/gif'];
            $imageType = mime_content_type($_FILES['banner_image']['tmp_name']);
            if (in_array($imageType, $allowedTypes)) {
                $ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
                $filename = $isEdit ? $id.'.'.$ext : uniqid().'.'.$ext;
                $targetPath = $plugin_path.$filename;
                if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
                    // altes Banner löschen
                    if ($isEdit && $data['banner_image'] && $data['banner_image'] !== $filename && file_exists($plugin_path.$data['banner_image'])) {
                        @unlink($plugin_path.$data['banner_image']);
                    }
                } else { $error = 'Fehler beim Speichern des Bannerbildes.'; }
            } else { $error = 'Ungültiger Bildtyp für Bannerbild.'; }
        }

        // Screenshots Upload
        $screenshots = json_decode($data['screenshots'], true) ?? [];
        if (!empty($_FILES['screenshots']['name'][0])) {
            foreach ($_FILES['screenshots']['tmp_name'] as $i => $tmp) {
                $ext = strtolower(pathinfo($_FILES['screenshots']['name'][$i], PATHINFO_EXTENSION));
                $file = uniqid().'_screenshot.'.$ext;
                if (move_uploaded_file($tmp, $plugin_path.$file)) {
                    $screenshots[] = $file;
                }
            }
        }
        $screenshots_json = json_encode($screenshots);

        if (!$error) {
            if ($isEdit) {
                safe_query("
                    UPDATE plugins_wiki SET
                        category_id='$cat',
                        title='$title',
                        desc_short='$desc_short',
                        desc_long='$desc_long',
                        slug='$slug',
                        banner_image='$filename',
                        screenshots='$screenshots_json',
                        sort_order='$sort_order',
                        is_active='$is_active',
                        updated_at=UNIX_TIMESTAMP()
                    WHERE id='$id'
                ");
            } else {
                $userID = 1; // System-User, anpassen falls nötig
                safe_query("
                    INSERT INTO plugins_wiki
                        (category_id, title, desc_short, desc_long, slug, banner_image, screenshots, sort_order, updated_at, userID, is_active)
                    VALUES
                        ('$cat','$title','$desc_short','$desc_long','$slug','$filename','$screenshots_json','$sort_order',UNIX_TIMESTAMP(),'$userID','$is_active')
                ");
            }
            header("Location: admincenter.php?site=admin_wiki");
            exit;
        }
    }

    ?>
    <div class="card">
        <div class="card-header">
            <i class="bi bi-journal-text"></i> Wiki <?= $isEdit ? "bearbeiten" : "hinzufügen" ?>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data" novalidate>

                <!-- Kategorie -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategorie:</label>
                    <select class="form-select" name="category_id" id="category_id" required>
                        <option value="">Bitte wählen...</option>
                        <?php
                        $stmtCat = $_database->prepare("SELECT id, name FROM plugins_wiki_categories ORDER BY name");
                        $stmtCat->execute();
                        $resCat = $stmtCat->get_result();
                        while ($cat = $resCat->fetch_assoc()) {
                            $selected = ($cat['id'] == $data['category_id']) ? 'selected' : '';
                            echo '<option value="'.$cat['id'].'" '.$selected.'>'.htmlspecialchars($cat['name']).'</option>';
                        }
                        $stmtCat->close();
                        ?>
                    </select>
                </div>

                <!-- Titel -->
                <div class="mb-3">
                    <label for="title" class="form-label">Titel:</label>
                    <input class="form-control" type="text" name="title" id="title" value="<?= htmlspecialchars($data['title']) ?>" required>
                </div>

                <!-- Kurzbeschreibung -->
                <div class="mb-3">
                    <label for="desc_short" class="form-label">Kurzbeschreibung:</label>
                    <textarea class="form-control" name="desc_short" rows="3"><?= htmlspecialchars($data['desc_short']) ?></textarea>
                </div>

                <!-- Langbeschreibung -->
                <div class="mb-3">
                    <label for="desc_long" class="form-label">Langbeschreibung:</label>
                    <textarea class="ckeditor" name="desc_long" rows="10"><?= $data['desc_long'] ?></textarea>
                </div>

                <!-- Slug -->
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (URL-Teil):</label>
                    <input class="form-control" type="text" name="slug" id="slug" value="<?= htmlspecialchars($data['slug']) ?>">
                </div>

                <!-- Bannerbild -->
                <div class="mb-3">
                    <label class="form-label">Bannerbild:</label>
                    <?php if ($isEdit && $data['banner_image'] && file_exists($plugin_path.$data['banner_image'])): ?>
                        <div class="mb-2">
                            <img src="/includes/plugins/wiki/images/<?= htmlspecialchars($data['banner_image']) ?>" class="img-thumbnail" style="max-height:150px;" alt="Banner Vorschau">
                        </div>
                    <?php endif; ?>
                    <input class="form-control" type="file" name="banner_image" id="banner_image">
                </div>

                <!-- Screenshots -->
                <div class="mb-3">
                    <label for="screenshots" class="form-label">Screenshots:</label>
                    <input class="form-control" type="file" name="screenshots[]" id="screenshots" multiple>

                    <?php
                    $existingScreens = json_decode($data['screenshots'], true) ?? [];
                    if ($existingScreens):
                    ?>
                        <div class="mt-2 d-flex flex-wrap">
                            <?php foreach ($existingScreens as $i => $s): ?>
                                <div class="position-relative me-2 mb-2">
                                    <img src="/includes/plugins/wiki/images/<?= htmlspecialchars($s) ?>" class="img-thumbnail" style="max-height:80px;">
                                    <a href="?site=admin_wiki&action=delete_screenshot&id=<?= $id ?>&index=<?= $i ?>" 
                                       class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                       onclick="return confirm('Screenshot wirklich löschen?');">
                                        &times;
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sortierung -->
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sortierung:</label>
                    <input class="form-control" type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars($data['sort_order']) ?>">
                </div>

                <!-- Aktiv -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $data['is_active'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Aktiv</label>
                </div>

                <!-- Buttons -->
                <button type="submit" class="btn btn-success"><?= $isEdit ? "Speichern" : "Hinzufügen" ?></button>
                <a href="admincenter.php?site=admin_wiki" class="btn btn-secondary">Abbrechen</a>
            </form>
        </div>
    </div>
<?php
}
 elseif (($action ?? '') === 'addcategory' || ($action ?? '') === 'editcategory') {
    $isEdit = $action === 'editcategory';
    $errorCat = '';
    $cat_name = '';
    $cat_description = '';
    $editId = 0;

    if ($isEdit) {
        $editId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $stmt = $_database->prepare("SELECT name, description FROM plugins_wiki_categories WHERE id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();
        $catData = $result->fetch_assoc();
        $stmt->close();

        if ($catData) {
            $cat_name = $catData['name'];
            $cat_description = $catData['description'];
        } else {
            $errorCat = "Kategorie nicht gefunden.";
        }
    }

    // Speichern
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_name'])) {
        $cat_name = trim($_POST['cat_name']);
        $cat_description = trim($_POST['cat_description'] ?? '');
        if ($cat_name === '') {
            $errorCat = "Der Kategoriename darf nicht leer sein.";
        } else {
            if ($isEdit && $editId > 0) {
                $stmt = $_database->prepare("UPDATE plugins_wiki_categories SET name = ?, description = ? WHERE id = ?");
                $stmt->bind_param("ssi", $cat_name, $cat_description, $editId);
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $_database->prepare("INSERT INTO plugins_wiki_categories (name, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $cat_name, $cat_description);
                $stmt->execute();
                $stmt->close();
            }
            header("Location: admincenter.php?site=admin_wiki&action=categories");
            exit;
        }
    }
    ?>
    <div class="card">
        <div class="card-header">
            <i class="bi bi-tags"></i> <?= $isEdit ? 'Kategorie bearbeiten' : 'Neue Kategorie hinzufügen' ?>
        </div>
        <nav class="breadcrumb bg-light p-2">
            <a class="breadcrumb-item" href="admincenter.php?site=admin_wiki&action=categories">Kategorien</a>
            <span class="breadcrumb-item active"><?= $isEdit ? 'Bearbeiten' : 'Hinzufügen' ?></span>
        </nav>
        <div class="card-body">
            <div class="container py-5">
                <?php if ($errorCat): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorCat) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="cat_name" class="form-label">Kategoriename:</label>
                        <input type="text"
                               class="form-control"
                               id="cat_name"
                               name="cat_name"
                               value="<?= htmlspecialchars($cat_name) ?>"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="cat_description" class="form-label">Beschreibung:</label>
                        <textarea class="form-control"
                                  id="cat_description"
                                  name="cat_description"
                                  rows="3"><?= htmlspecialchars($cat_description) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? 'Änderungen speichern' : 'Kategorie hinzufügen' ?>
                    </button>
                    <a href="admincenter.php?site=admin_wiki&action=categories" class="btn btn-secondary">Abbrechen</a>
                </form>
            </div>
        </div>
    </div>
    <?php
}
 elseif (($action ?? '') === 'categories') {
    $errorCat = '';

    // Kategorie löschen
    if (isset($_GET['delcat'])) {
        $delcat = intval($_GET['delcat']);
        $stmt = $_database->prepare("DELETE FROM plugins_wiki_categories WHERE id = ?");
        $stmt->bind_param("i", $delcat);
        $stmt->execute();
        $stmt->close();
        header("Location: admincenter.php?site=admin_wiki&action=categories");
        exit;
    }

    // Kategorien laden inkl. Beschreibung
    $result = $_database->query("SELECT id, name, description FROM plugins_wiki_categories ORDER BY name");
    ?>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-tags"></i> Kategorien verwalten
        </div>
        <nav class="breadcrumb bg-light p-2">
            <a class="breadcrumb-item" href="admincenter.php?site=admin_wiki">Wiki verwalten</a>
            <span class="breadcrumb-item active">Kategorien</span>
        </nav>
        <div class="card-body">
            <div class="container py-5">

                <a href="admincenter.php?site=admin_wiki&action=addcategory"
                   class="btn btn-success mb-3">
                   <i class="bi bi-plus-circle"></i> Neue Kategorie hinzufügen
                </a>
                
                <h5>Bestehende Kategorien:</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Beschreibung</th>
                        <th>Aktion</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($cat = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td>
                                <a href="admincenter.php?site=admin_wiki&action=editcategory&id=<?= (int)$cat['id'] ?>"
                                   class="btn btn-sm btn-warning">Bearbeiten</a>
                                <a href="admincenter.php?site=admin_wiki&action=categories&delcat=<?= (int)$cat['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Kategorie wirklich löschen?')">Löschen</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

                <a href="admincenter.php?site=admin_wiki" class="btn btn-secondary">Zurück</a>
            </div>
        </div>
    </div>
    <?php
}

 else {

    // --- Wikiliste anzeigen ---
    $result = $_database->query("SELECT a.id, a.title, a.sort_order, a.is_active, c.name as category_name FROM plugins_wiki a LEFT JOIN plugins_wiki_categories c ON a.category_id = c.id ORDER BY a.sort_order ASC, a.title ASC");
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="bi bi-journal-text"></i> Wiki verwalten</div>
            <div>
                <a href="admincenter.php?site=admin_wiki&action=add" class="btn btn-success btn-sm"><i class="bi bi-plus"></i> Neu</a>
                <a href="admincenter.php?site=admin_wiki&action=categories" class="btn btn-primary btn-sm"><i class="bi bi-tags"></i> Kategorien</a>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb t-5 p-2 bg-light">
                <li class="breadcrumb-item"><a href="admincenter.php?site=admin_wiki">Wiki verwalten</a></li>
                <li class="breadcrumb-item active" aria-current="page">New / Edit</li>
            </ol>
        </nav> 
        <div class="card-body p-0">
            <div class="container py-5">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Kategorie</th>
                    <th>Sortierung</th>
                    <th>Aktiv</th>
                    <th>Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                        <td><?= (int)$row['sort_order'] ?></td>
                        <td><?= $row['is_active'] ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-secondary">Nein</span>' ?></td>
                        <td>
                            <a href="admincenter.php?site=admin_wiki&action=edit&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Bearbeiten</a>
                            <a href="#" class="btn btn-sm btn-danger btn-delete-article" data-id="<?= (int)$row['id'] ?>"><i class="bi bi-trash"></i> Löschen</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
<?php
}  // schließt das else
?>
    <script>
    document.querySelectorAll('.btn-delete-article').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Wiki wirklich löschen?')) {
                const id = this.getAttribute('data-id');
                fetch('admincenter.php?site=admin_wiki&action=delete&id=' + id)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Fehler beim Löschen: ' + (data.error || 'Unbekannt'));
                        }
                    });
            }
        });
    });
    </script>

