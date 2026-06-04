<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth-check.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın.']);
    exit;
}

$pdo = db();
$profileId = $_SESSION['profile_id'] ?? 0;

if ($profileId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Profil seçilmemiş.']);
    exit;
}

$action = $_POST['action'] ?? '';
$contentId = intval($_POST['content_id'] ?? 0);

if ($contentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz içerik.']);
    exit;
}

if ($action === 'toggle_watchlist') {
    $stmt = $pdo->prepare("SELECT id FROM watchlist WHERE profile_id = ? AND content_id = ?");
    $stmt->execute([$profileId, $contentId]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        $pdo->prepare("DELETE FROM watchlist WHERE profile_id = ? AND content_id = ?")->execute([$profileId, $contentId]);
        echo json_encode(['success' => true, 'status' => 'removed']);
    } else {
        $pdo->prepare("INSERT INTO watchlist (profile_id, content_id) VALUES (?, ?)")->execute([$profileId, $contentId]);
        echo json_encode(['success' => true, 'status' => 'added']);
    }
    exit;
}

if ($action === 'toggle_like') {
    $stmt = $pdo->prepare("SELECT id, is_like FROM likes WHERE profile_id = ? AND content_id = ?");
    $stmt->execute([$profileId, $contentId]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        if ($exists['is_like'] == 1) {
            $pdo->prepare("DELETE FROM likes WHERE profile_id = ? AND content_id = ?")->execute([$profileId, $contentId]);
            echo json_encode(['success' => true, 'status' => 'removed']);
        } else {
            $pdo->prepare("UPDATE likes SET is_like = 1 WHERE profile_id = ? AND content_id = ?")->execute([$profileId, $contentId]);
            echo json_encode(['success' => true, 'status' => 'added']);
        }
    } else {
        $pdo->prepare("INSERT INTO likes (profile_id, content_id, is_like) VALUES (?, ?, 1)")->execute([$profileId, $contentId]);
        echo json_encode(['success' => true, 'status' => 'added']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Geçersiz işlem.']);
