<?php
/**
 * Movify - Kayıt Ol Sayfası
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';

if (isLoggedIn()) {
    redirect('/profiles.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        $pdo = db();
        
        // Email kontrolü
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->fetch()) {
            $error = 'Bu e-posta adresi zaten kullanılıyor.';
        } else {
            // Transaction başlat (Kullanıcı + Varsayılan Profil + Deneme Aboneliği)
            try {
                $pdo->beginTransaction();
                
                // 1. Kullanıcı oluştur
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hash]);
                $userId = $pdo->lastInsertId();
                
                // 2. Varsayılan profil oluştur
                $stmtProfile = $pdo->prepare("INSERT INTO profiles (user_id, profile_name) VALUES (?, ?)");
                $stmtProfile->execute([$userId, $name]);
                
                // 3. Deneme aboneliği (7 gün)
                $trialEnd = date('Y-m-d H:i:s', strtotime('+7 days'));
                $stmtSub = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_name, end_date, status, payment_method) VALUES (?, 'Deneme Aboneliği', ?, 'active', 'Ücretsiz Deneme')");
                $stmtSub->execute([$userId, $trialEnd]);
                
                $pdo->commit();
                
                redirect('/login.php?registered=1');
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Movify</title>
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
        <h2 class="auth-title text-center" style="text-align: center;">Kayıt Ol</h2>
        <p style="text-align:center; color:var(--text-muted); margin-top:-15px; margin-bottom:20px; font-size:0.9rem;">Hemen katıl, 7 gün deneme süresi kazan!</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        
        <form action="register.php" method="POST">
            <div class="form-group">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn--primary" style="width: 100%; margin-top: 10px;">Kayıt Ol</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            Zaten hesabınız var mı? <a href="login.php" style="color: var(--text-main); font-weight: 500;">Giriş Yap</a>
        </div>
    </div>
</div>

</body>
</html>
