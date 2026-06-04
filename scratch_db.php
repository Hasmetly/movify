<?php
$pdo = new PDO('mysql:host=localhost;dbname=movify;charset=utf8mb4', 'root', '');
$count = $pdo->query('SELECT count(*) FROM content WHERE is_coming_soon = 0')->fetchColumn();
echo 'Total items: ' . $count;
