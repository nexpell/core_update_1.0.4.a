<?php
header('Content-Type: application/json');

$response = ['success' => false];

if (
    !isset($_FILES['image']) ||
    $_FILES['image']['error'] !== UPLOAD_ERR_OK
) {
    $response['message'] = 'Kein Bild erhalten oder Upload-Fehler.';
    echo json_encode($response);
    exit;
}

/* =========================================
   UPLOAD-VERZEICHNIS
========================================= */
$uploadDir = __DIR__ . '/uploads/forum_images/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

/* =========================================
   DATEITYP PRÜFEN
========================================= */
$allowedExtensions = ['jpg','jpeg','png','gif','webp'];
$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions, true)) {
    $response['message'] = 'Ungültiger Dateityp.';
    echo json_encode($response);
    exit;
}

/* =========================================
   🔥 HASH BASIERTER DATEINAME
========================================= */
$hash = sha1_file($_FILES['image']['tmp_name']);
$filename = 'img_' . substr(sha1_file($_FILES['image']['tmp_name']), 0, 16) . '.' . $ext;
$filepath = $uploadDir . $filename;

/* =========================================
   DATEI NUR SPEICHERN, WENN SIE NICHT EXISTIERT
========================================= */
if (!file_exists($filepath)) {
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
        $response['message'] = 'Fehler beim Speichern.';
        echo json_encode($response);
        exit;
    }
}

/* =========================================
   RELATIVE URL ZURÜCKGEBEN
========================================= */
$relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($uploadDir));
$relativePath = rtrim($relativePath, '/');

//$response['url'] = $relativePath . '/' . $filename;
$response['url'] = '/includes/plugins/forum/uploads/forum_images/' . $filename;

$response['success'] = true;

echo json_encode($response);
