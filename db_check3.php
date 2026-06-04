<?php
require_once __DIR__ . '/includes/db.php';
$pdo=db();
$stmt=$pdo->query('SHOW COLUMNS FROM content_cast');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
