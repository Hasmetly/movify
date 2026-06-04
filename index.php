<?php
/**
 * Movify - Giriş Noktası
 * Kullanıcı oturum durumuna göre yönlendirme yapar
 */
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) {
    if (!currentProfileId()) {
        redirect('/profiles.php');
    } else {
        redirect('/home.php');
    }
} else {
    redirect('/login.php');
}
