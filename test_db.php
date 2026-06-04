<?php
require 'includes/db.php';
$pdo = db();
$stmt = $pdo->query("SELECT c.id, c.title, c.type, l.link_type, l.url FROM content c LEFT JOIN content_links l ON c.id=l.content_id WHERE c.title LIKE '%Kolpaçino%' OR c.title LIKE '%Muhteşem%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $pdo->query("SELECT id, title, video_url FROM episodes WHERE content_id IN (SELECT id FROM content WHERE title LIKE '%Muhteşem%')");
echo "\nEpisodes:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
