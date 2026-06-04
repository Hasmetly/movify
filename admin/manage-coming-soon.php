<?php
/**
 * Movify - Admin: Yakında / Bildirim Yönetimi
 */
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/home.php');
}

$pdo = db();
$msg = '';

$uploadDir = dirname(__DIR__) . '/uploads';

// Ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_notification') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $expected_date = trim($_POST['expected_date'] ?? '');
    
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imageName = 'notif_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . '/' . $imageName);
    }
    
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message, image, expected_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $message, $imageName, $expected_date]);
        $msg = "Bildirim eklendi.";
    }
}

// Sil
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM notifications WHERE id = ?")->execute([intval($_GET['delete'])]);
    redirect('/admin/manage-coming-soon.php');
}

// Durum değiştir
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE notifications SET is_active = NOT is_active WHERE id = ?")->execute([intval($_GET['toggle'])]);
    redirect('/admin/manage-coming-soon.php');
}

$notifs = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'Yakında / Bildirimler - Admin';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body data-theme="dark">

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar__logo">
            <a href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/assets/images/movifylogo.png" alt="Movify" class="admin-logo-img">
            </a>
            <span class="admin-badge">Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="manage-content.php" class="admin-nav-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                İçerik Yönetimi
            </a>
            <a href="manage-episodes.php" class="admin-nav-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                Bölüm/Dizi Yönetimi
            </a>
            <a href="manage-coming-soon.php" class="admin-nav-link active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Yakında / Bildirimler
            </a>
        </nav>
        <div class="admin-sidebar__footer">
            <a href="<?= BASE_URL ?>/home.php" class="admin-nav-link admin-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Siteye Dön
            </a>
        </div>
    </aside>

    <main class="admin-content">
        <!-- Mobil Toggle Butonu -->
        <button class="admin-mobile-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 style="margin-bottom: 24px;">Yakında Çıkacaklar & Bildirimler</h1>
        
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <div class="stat-card" style="flex: 1; min-width: 300px; max-width: 400px; height: fit-content;">
                <h3 style="margin-bottom: 20px;">Yeni Bildirim Ekle</h3>
                <form action="manage-coming-soon.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_notification">
                    
                    <div class="form-group">
                        <label class="form-label">Başlık</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Mesaj (Opsiyonel)</label>
                        <textarea name="message" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Beklenen Tarih (Örn: Kasım 2026)</label>
                        <input type="text" name="expected_date" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Görsel (Opsiyonel)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    
                    <button type="submit" class="btn btn--primary" style="width:100%;">Ekle</button>
                </form>
            </div>
            
            <div class="stat-card" style="flex: 2; min-width: 300px; padding:0; overflow:hidden; height: fit-content;">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Görsel</th>
                                <th>Başlık</th>
                                <th>Tarih</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifs as $row): ?>
                            <tr>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="<?= BASE_URL ?>/uploads/<?= e($row['image']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= e($row['title']) ?></td>
                                <td><?= e($row['expected_date']) ?></td>
                                <td>
                                    <a href="manage-coming-soon.php?toggle=<?= $row['id'] ?>" style="color: <?= $row['is_active'] ? '#4ade80' : '#f87171' ?>; text-decoration:none;">
                                        <?= $row['is_active'] ? 'Aktif' : 'Pasif' ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="manage-coming-soon.php?delete=<?= $row['id'] ?>" class="btn btn--sm btn--ghost" style="color:#f87171;" onclick="return confirm('Silinsin mi?')">Sil</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
