<?php
require_once __DIR__ . '/includes/db.php';
$pdo = db();
try {
    $pdo->exec("ALTER TABLE content ADD director VARCHAR(255) NULL AFTER type, ADD original_language VARCHAR(50) NULL AFTER director");
    echo "DB Updated successfully.\n";
} catch (Exception $e) {
    echo "Error (might already exist): " . $e->getMessage() . "\n";
}
