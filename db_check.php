<?php
require_once __DIR__ . '/includes/db.php';
$pdo = db();
$stmt = $pdo->query("SHOW COLUMNS FROM content");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $pdo->query("SHOW TABLES");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
