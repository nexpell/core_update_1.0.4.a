<?php
$configPath = __DIR__ . '/../../../../system/config.inc.php';
require_once $configPath;

$_database = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($_database->connect_error) {
    http_response_code(500);
    exit('DB connection failed');
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    exit('Invalid payload');
}

$stmt = $_database->prepare("UPDATE plugins_gallery SET position = ?, sort_page = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    exit('Prepare failed');
}

foreach ($data as $item) {
    $id = isset($item['id']) ? (int) $item['id'] : 0;
    $position = isset($item['position']) ? (int) $item['position'] : 0;
    $sortPage = isset($item['sort_page']) ? (int) $item['sort_page'] : 1;

    if ($id <= 0 || $position <= 0 || $sortPage <= 0) {
        continue;
    }

    $stmt->bind_param('iii', $position, $sortPage, $id);
    if (!$stmt->execute()) {
        http_response_code(500);
        $stmt->close();
        exit('DB error');
    }
}

$stmt->close();
echo 'OK';
