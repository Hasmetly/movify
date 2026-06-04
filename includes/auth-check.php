<?php
/**
 * Movify - Auth Check Middleware
 * Yetkilendirme kontrol ara katmanı
 */
require_once __DIR__ . '/db.php';

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function requireProfile() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
    if (!currentProfileId()) {
        redirect('/profiles.php');
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        redirect('/login.php');
    }
}
