<?php
/**
 * Movify - Şifremi Unuttum
 * (Basit simülasyon)
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';

if (isLoggedIn()) {
    redirect('/profiles.php');
}

$msg = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $msg = 'Lütfen e-posta adresinizi girin.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            // Gerçekte burada mail atılır. Biz sadece mesaj gösteriyoruz.
            $msg = 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.';
            $isSuccess = true;
        } else {
            $msg = 'Bu e-posta adresi sistemimizde kayıtlı değil.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum - Movify</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .auth-page { background: url('<?= BASE_URL ?>/assets/images/auth-bg.jpg') center/cover; }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .auth-card { padding: 24px !important; margin: 0 15px !important; }
            .auth-title { font-size: 1.4rem !important; }
        }
        @media (max-width: 480px) {
            .auth-card { padding: 20px !important; margin: 0 10px !important; }
            .auth-title { font-size: 1.2rem !important; }
        }
    </style>
</head>
<body data-theme="dark">

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <a href="<?= BASE_URL ?>/index.php" class="logo" style="justify-content: center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                Movify
            </a>
        </div>
        <h2 class="auth-title text-center" style="text-align: center;">Şifremi Unuttum</h2>
        
        <?php if ($msg): ?>
            <div class="alert <?= $isSuccess ? 'alert-success' : 'alert-error' ?>"><?= e($msg) ?></div>
        <?php endif; ?>
        
        <?php if (!$isSuccess): ?>
        <form action="forgot-password.php" method="POST">
            <div class="form-group">
                <label class="form-label">E-posta Adresiniz</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn--primary" style="width: 100%; margin-top: 10px;">Bağlantı Gönder</button>
        </form>
        <?php endif; ?>
        
        <div style="margin-top: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            Hatırladınız mı? <a href="login.php" style="color: var(--text-main); font-weight: 500;">Giriş Yap</a>
        </div>
    </div>
</div>

</body>
</html>
