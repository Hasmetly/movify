<?php
/**
 * Movify - İzleme Geçmişi API
 * Video oynatıcıdan periyodik olarak çağrılır
 * İzleme konumunu ve tamamlanma durumunu kaydeder
 */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !currentProfileId()) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Sadece POST']);
    exit;
}

$pdo = db();
$profileId = currentProfileId();
$contentId = intval($_POST['content_id'] ?? 0);
$position = intval($_POST['position'] ?? 0);
$totalDuration = intval($_POST['total_duration'] ?? 0);
$isFinished = intval($_POST['is_finished'] ?? 0);
$episodeId = !empty($_POST['episode_id']) ? intval($_POST['episode_id']) : null;

if ($contentId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Geçersiz içerik']);
    exit;
}

// UPSERT: Mevcut kayıt varsa güncelle, yoksa ekle
$stmt = $pdo->prepare("SELECT id FROM watch_history WHERE profile_id = ? AND content_id = ? AND (episode_id = ? OR (episode_id IS NULL AND ? IS NULL))");
$stmt->execute([$profileId, $contentId, $episodeId, $episodeId]);
$existing = $stmt->fetch();

if ($existing) {
    $update = $pdo->prepare("UPDATE watch_history SET 
        watched_position_seconds = ?, 
        total_duration_seconds = ?, 
        is_finished = ?,
        updated_at = NOW()
        WHERE id = ?");
    $update->execute([$position, $totalDuration, $isFinished, $existing['id']]);
} else {
    $insert = $pdo->prepare("INSERT INTO watch_history (profile_id, content_id, episode_id, watched_position_seconds, total_duration_seconds, is_finished) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$profileId, $contentId, $episodeId, $position, $totalDuration, $isFinished]);
}

echo json_encode(['success' => true]);
