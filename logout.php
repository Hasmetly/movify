<?php
/**
 * Movify - Çıkış Yap
 */
require_once __DIR__ . '/includes/db.php';

session_start();
session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/login.php');
exit;
