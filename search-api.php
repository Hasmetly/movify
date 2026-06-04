<?php
/**
 * Movify - Canlı Arama API'si
 * app.js tarafından çağrılır. Geriye JSON olarak arama sonuçlarını döndürür.
 */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');

if (mb_strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id, type, title, cover_image, release_year, imdb_rating FROM content WHERE title LIKE ? ORDER BY id DESC LIMIT 5");
$stmt->execute(['%' . $query . '%']);
$results = $stmt->fetchAll();

$jsonResults = [];
foreach ($results as $row) {
    $cover = $row['cover_image'] ? (strpos($row['cover_image'], 'http') === 0 ? $row['cover_image'] : BASE_URL . '/uploads/' . $row['cover_image']) : '';
    
    $jsonResults[] = [
        'id' => $row['id'],
        'type' => $row['type'],
        'title' => $row['title'],
        'cover_url' => $cover,
        'release_year' => $row['release_year'],
        'imdb_rating' => $row['imdb_rating']
    ];
}

echo json_encode(['results' => $jsonResults]);
exit;
