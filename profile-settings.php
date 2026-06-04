<?php
/**
 * Movify - Profil ve Hesap Ayarları
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';
requireProfile();

$pdo = db();
$userId = $_SESSION['user_id'];
$profileId = $_SESSION['profile_id'];
$msg = '';
$msgType = 'success';

// Şifre Değiştirme / Hesap Ayarları
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_account') {
    $newName = trim($_POST['name'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    // Şifre kontrolü vb. burada yapılabilir. (Basit tutuldu)
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$newName, $newEmail, $userId]);
    $msg = 'Hesap bilgileri güncellendi.';
}

// Profil Güncelleme (Avatar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $profName = trim($_POST['profile_name'] ?? '');
    
    // Avatar upload işlemi
    $avatar = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $avatarName = 'avatar_' . $profileId . '_' . time() . '.' . $ext;
            if (!is_dir(__DIR__ . '/uploads')) { mkdir(__DIR__ . '/uploads', 0777, true); }
            move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/uploads/' . $avatarName);
            $avatar = $avatarName;
        }
    }
    
    if ($avatar) {
        $stmt = $pdo->prepare("UPDATE profiles SET profile_name = ?, avatar_image = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$profName, $avatar, $profileId, $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE profiles SET profile_name = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$profName, $profileId, $userId]);
    }
    
    $msg = 'Profil güncellendi.';
}

// Mevcut bilgileri çek
$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch();

$stmtProf = $pdo->prepare("SELECT * FROM profiles WHERE id = ?");
$stmtProf->execute([$profileId]);
$profile = $stmtProf->fetch();

// Mevcut aboneliği çek
$stmtSub = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmtSub->execute([$userId]);
$subscription = $stmtSub->fetch();

// Abonelik Güncelleme İşlemi (Mock Payment)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_subscription') {
    $planName = $_POST['plan_name'] ?? '';
    
    $price = 0;
    if ($planName === '1080p Pro Plan') $price = 100.00;
    elseif ($planName === '4K Ultra Plan') $price = 200.00;
    
    if ($price > 0) {
        $endDate = date('Y-m-d H:i:s', strtotime('+1 month'));
        // Eski aktif abonelikleri inactive yap
        $pdo->prepare("UPDATE subscriptions SET status = 'expired' WHERE user_id = ? AND status = 'active'")->execute([$userId]);
        // Yeni aboneliği ekle
        $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_name, price, status, end_date, payment_method) VALUES (?, ?, ?, 'active', ?, 'Sanal Pos')");
        $stmt->execute([$userId, $planName, $price, $endDate]);
        $msg = "Aboneliğiniz başarıyla $planName olarak güncellendi!";
        
        // Aboneliği tekrar çek
        $stmtSub->execute([$userId]);
        $subscription = $stmtSub->fetch();
    }
}

$pageTitle = 'Ayarlar - Movify';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 768px) {
        .container { padding-top: 80px !important; padding-left: 15px !important; padding-right: 15px !important; }
        .settings-card, .container > div[style*="background: var(--bg-secondary)"] { padding: 16px !important; }
        .avatar-section, div[style*="display: flex; gap: 20px; align-items: center"] { flex-direction: column !important; align-items: flex-start !important; }
        .avatar-preview { width: 80px !important; height: 80px !important; }
    }
    @media (max-width: 480px) {
        .container { padding-top: 70px !important; padding-left: 10px !important; padding-right: 10px !important; }
        .avatar-preview { width: 70px !important; height: 70px !important; }
        h2 { font-size: 1.3rem !important; }
    }
</style>

<div class="container" style="padding-top: 100px; max-width: 800px; padding-bottom: 60px;">
    
    <?php if ($msg): ?>
        <div class="alert alert-<?= $msgType ?>"><?= e($msg) ?></div>
    <?php endif; ?>

    <h2 style="margin-bottom: 24px;">Profil Ayarları</h2>
    
    <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; margin-bottom: 40px;">
        <form action="profile-settings.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">
            
            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px;">
                <div class="avatar-preview" style="width: 100px; height: 100px; border-radius: 8px; background: var(--bg-tertiary); overflow: hidden;">
                    <?php if ($profile['avatar_image'] && $profile['avatar_image'] !== 'default-avatar.png'): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($profile['avatar_image']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--text-muted);">
                            <?= e(mb_substr($profile['profile_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="form-label">Avatar Değiştir</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Profil Adı</label>
                <input type="text" name="profile_name" class="form-control" value="<?= e($profile['profile_name']) ?>" required>
            </div>
            
            <button type="submit" class="btn btn--primary">Profili Kaydet</button>
        </form>
    </div>

    <h2 style="margin-bottom: 24px;">Hesap Ayarları</h2>
    
    <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px;">
        <form action="profile-settings.php" method="POST">
            <input type="hidden" name="action" value="update_account">
            
            <div class="form-group">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
            </div>
            
            <hr style="border:0; border-top:1px solid var(--border-color); margin: 24px 0;">
            <h3 style="margin-bottom:16px; font-size:1.1rem;">Şifre Değiştirme (Opsiyonel)</h3>
            
            <div class="form-group">
                <label class="form-label">Mevcut Şifre</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            
            <button type="submit" class="btn btn--primary">Hesabı Güncelle</button>
        </form>
    </div>

    <!-- ABONELİK BÖLÜMÜ -->
    <h2 style="margin-top: 40px; margin-bottom: 24px;">Abonelik ve Planlar</h2>
    
    <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; color: var(--text-main);">Mevcut Aboneliğiniz</h3>
        <?php if ($subscription): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; background: rgba(255,255,255,0.03); padding: 15px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                <div>
                    <div style="font-size: 1.2rem; font-weight: 600; color: var(--accent-pink);"><?= e($subscription['plan_name']) ?></div>
                    <div style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">
                        Durum: <span style="color: <?= $subscription['status'] === 'active' ? '#10b981' : '#ef4444' ?>; font-weight: 500;"><?= ucfirst($subscription['status']) ?></span>
                        <span style="margin: 0 10px;">|</span>
                        Bitiş Tarihi: <?= $subscription['end_date'] ? date('d.m.Y H:i', strtotime($subscription['end_date'])) : 'Süresiz' ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="color: var(--text-muted);">Aktif bir aboneliğiniz bulunmuyor. Lütfen aşağıdaki planlardan birini seçin.</div>
        <?php endif; ?>
    </div>

    <!-- Pricing Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        
        <!-- Pro Plan -->
        <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; display: flex; flex-direction: column; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <h3 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--text-main);">1080p Pro Plan</h3>
            <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px; color: #fff;">100 ₺ <span style="font-size: 1rem; color: var(--text-muted); font-weight: 400;">/ ay</span></div>
            
            <ul style="list-style: none; padding: 0; margin: 0; flex-grow: 1; margin-bottom: 25px;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-muted);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    1080p (Full HD) Çözünürlük
                </li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-muted);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Aynı Anda 2 Cihazdan İzleme
                </li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-muted);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Sınırsız Film ve Dizi Erişimi
                </li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-muted);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Çevrimdışı İzleme (Standart Hız)
                </li>
            </ul>
            
            <form action="profile-settings.php" method="POST">
                <input type="hidden" name="action" value="update_subscription">
                <input type="hidden" name="plan_name" value="1080p Pro Plan">
                <button type="submit" class="btn" style="width: 100%; background: transparent; border: 1px solid var(--accent-purple); color: #fff; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='var(--accent-purple)'" onmouseout="this.style.background='transparent'">Bu Plana Geç</button>
            </form>
        </div>

        <!-- Ultra Plan -->
        <div style="background: linear-gradient(145deg, rgba(147, 51, 234, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%); border: 1px solid var(--accent-pink); border-radius: 16px; padding: 30px; display: flex; flex-direction: column; position: relative; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: linear-gradient(90deg, var(--accent-pink), var(--accent-purple)); color: #fff; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px;">En Popüler</div>
            
            <h3 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--text-main);">4K Ultra Plan</h3>
            <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px; color: #fff;">200 ₺ <span style="font-size: 1rem; color: var(--text-muted); font-weight: 400;">/ ay</span></div>
            
            <ul style="list-style: none; padding: 0; margin: 0; flex-grow: 1; margin-bottom: 25px;">
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-main);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    4K (Ultra HD) ve HDR Desteği
                </li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-main);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Aynı Anda 4 Cihazdan İzleme
                </li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-main);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Sınırsız İndirme
                </li>
                <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-main);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Dolby Atmos ve Mekansal Ses
                </li>
            </ul>
            
            <form action="profile-settings.php" method="POST">
                <input type="hidden" name="action" value="update_subscription">
                <input type="hidden" name="plan_name" value="4K Ultra Plan">
                <button type="submit" class="btn" style="width: 100%; background: linear-gradient(90deg, var(--accent-pink), var(--accent-purple)); border: none; color: #fff; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; opacity: 0.9;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">Bu Plana Geç</button>
            </form>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
