<?php
/**
 * Movify - AJAX İşlemleri API
 * Watchlist, Like/Dislike, Yorum ve Filmografi işlemleri
 */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !currentProfileId()) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

$pdo = db();
$profileId = currentProfileId();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ========== WATCHLIST TOGGLE ==========
    case 'toggle_watchlist':
        $contentId = intval($_POST['content_id'] ?? 0);
        if ($contentId <= 0) { echo json_encode(['error' => 'Geçersiz']); exit; }

        $check = $pdo->prepare("SELECT id FROM watchlist WHERE profile_id = ? AND content_id = ?");
        $check->execute([$profileId, $contentId]);
        
        if ($check->fetch()) {
            $pdo->prepare("DELETE FROM watchlist WHERE profile_id = ? AND content_id = ?")->execute([$profileId, $contentId]);
            echo json_encode(['success' => true, 'status' => 'removed']);
        } else {
            $pdo->prepare("INSERT INTO watchlist (profile_id, content_id) VALUES (?, ?)")->execute([$profileId, $contentId]);
            echo json_encode(['success' => true, 'status' => 'added']);
        }
        break;

    // ========== LIKE / DISLIKE TOGGLE ==========
    case 'toggle_like':
        $contentId = intval($_POST['content_id'] ?? 0);
        $isLike = intval($_POST['is_like'] ?? 1);
        if ($contentId <= 0) { echo json_encode(['error' => 'Geçersiz']); exit; }

        $check = $pdo->prepare("SELECT id, is_like FROM likes WHERE profile_id = ? AND content_id = ?");
        $check->execute([$profileId, $contentId]);
        $existing = $check->fetch();

        if ($existing) {
            if ($existing['is_like'] == $isLike) {
                // Aynı butona tekrar basıldı → kaldır
                $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$existing['id']]);
                echo json_encode(['success' => true, 'status' => 'removed']);
            } else {
                // Farklı buton → güncelle
                $pdo->prepare("UPDATE likes SET is_like = ? WHERE id = ?")->execute([$isLike, $existing['id']]);
                echo json_encode(['success' => true, 'status' => 'updated']);
            }
        } else {
            $pdo->prepare("INSERT INTO likes (profile_id, content_id, is_like) VALUES (?, ?, ?)")->execute([$profileId, $contentId, $isLike]);
            echo json_encode(['success' => true, 'status' => 'added']);
        }

        // Güncel sayıları döndür
        $counts = $pdo->prepare("SELECT 
            SUM(CASE WHEN is_like = 1 THEN 1 ELSE 0 END) as likes,
            SUM(CASE WHEN is_like = 0 THEN 1 ELSE 0 END) as dislikes
            FROM likes WHERE content_id = ?");
        $counts->execute([$contentId]);
        $result = $counts->fetch();
        echo json_encode(['success' => true, 'likes' => intval($result['likes']), 'dislikes' => intval($result['dislikes'])]);
        break;

    // ========== YORUM EKLEME ==========
    case 'add_comment':
        $contentId = intval($_POST['content_id'] ?? 0);
        $commentText = trim($_POST['comment_text'] ?? '');
        if ($contentId <= 0 || empty($commentText)) { echo json_encode(['error' => 'Geçersiz']); exit; }

        $stmt = $pdo->prepare("INSERT INTO comments (profile_id, content_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$profileId, $contentId, $commentText]);

        // Profil bilgisi
        $profile = $pdo->prepare("SELECT profile_name FROM profiles WHERE id = ?");
        $profile->execute([$profileId]);
        $profileData = $profile->fetch();

        echo json_encode([
            'success' => true,
            'comment' => [
                'profile_name' => $profileData['profile_name'],
                'comment_text' => $commentText,
                'created_at' => date('d.m.Y H:i')
            ]
        ]);
        break;

    // ========== FİLMOGRAFİ (Oyuncu/Yönetmen yapımları) ==========
    case 'filmography':
        $castName = trim($_GET['name'] ?? '');
        if (empty($castName)) { echo json_encode(['error' => 'Geçersiz']); exit; }

        $stmt = $pdo->prepare("SELECT DISTINCT c.id, c.title, c.type, c.cover_image, c.release_year, c.imdb_rating 
            FROM content c 
            JOIN content_cast cc ON c.id = cc.content_id 
            WHERE cc.name LIKE ? AND c.is_coming_soon = 0
            ORDER BY c.release_year DESC");
        $stmt->execute(['%' . $castName . '%']);
        $results = $stmt->fetchAll();

        foreach ($results as &$item) {
            $item['cover_url'] = $item['cover_image'] ? BASE_URL . '/uploads/' . $item['cover_image'] : null;
            unset($item['cover_image']);
        }

        echo json_encode(['results' => $results, 'name' => $castName]);
        break;

    // ========== BİLDİRİMLER (Coming Soon) ==========
    case 'get_notifications':
        $stmt = $pdo->query("SELECT * FROM notifications WHERE is_active = 1 ORDER BY expected_date ASC LIMIT 20");
        $results = $stmt->fetchAll();
        
        foreach ($results as &$item) {
            $item['image_url'] = $item['image'] ? BASE_URL . '/uploads/' . $item['image'] : null;
        }

        echo json_encode(['notifications' => $results]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Bilinmeyen işlem']);
        break;
}
