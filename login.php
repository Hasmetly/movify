<?php
/**
 * Movify - Giriş Yap Sayfası
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';

if (isLoggedIn()) {
    redirect('/profiles.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            
            // Eğer profile_id set edilmişse ama başka hesaba giriyorsak sıfırla
            unset($_SESSION['profile_id']);
            
            redirect('/profiles.php');
        } else {
            $error = 'Hatalı e-posta veya şifre.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Movify</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .auth-page { background: url('<?= BASE_URL ?>/assets/images/auth-bg.jpg') center/cover; }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .auth-card { padding: 24px !important; margin: 0 15px !important; }
            .auth-logo img { height: 60px !important; }
            .auth-title { font-size: 1.4rem !important; }
        }
        @media (max-width: 480px) {
            .auth-card { padding: 20px !important; margin: 0 10px !important; }
            .auth-logo img { height: 50px !important; }
            .auth-title { font-size: 1.2rem !important; }
        }
    </style>
</head>
<body data-theme="dark">

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo" style="text-align: center; margin-bottom: 10px;">
            <a href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/assets/images/movifylogo.png" alt="Movify Logo" style="height: 80px; width: auto;">
            </a>
        </div>
        <h2 class="auth-title text-center" style="text-align: center;">Giriş Yap</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Kayıt başarılı, şimdi giriş yapabilirsiniz.</div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn--primary" style="width: 100%; margin-top: 10px;">Giriş Yap</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            <a href="forgot-password.php" style="color: var(--text-muted);">Şifremi Unuttum</a>
            <div style="margin-top: 10px;">
                Hesabınız yok mu? <a href="register.php" style="color: var(--text-main); font-weight: 500;">Hemen Kayıt Ol</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
