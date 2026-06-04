<?php
/**
 * Movify - Admin Paneli Dashboard
 */
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

// Admin yetkisi kontrolü
if (!isLoggedIn() || !isAdmin()) {
    redirect('/home.php');
}

$pdo = db();

// İstatistikleri çek
$statUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$statMovies = $pdo->query("SELECT COUNT(*) FROM content WHERE type = 'movie'")->fetchColumn();
$statSeries = $pdo->query("SELECT COUNT(*) FROM content WHERE type = 'series'")->fetchColumn();
$statAmericanMovies = $pdo->query("SELECT COUNT(c.id) FROM content c JOIN content_categories cc ON c.id = cc.content_id JOIN categories cat ON cc.category_id = cat.id WHERE cat.slug = 'amerikan-filmleri'")->fetchColumn();
$statViews = $pdo->query("SELECT SUM(views_count) FROM content")->fetchColumn() ?: 0;

$pageTitle = 'Admin Dashboard - Movify';
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
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar__logo">
            <a href="<?= BASE_URL ?>/index.php">
                <img src="<?= BASE_URL ?>/assets/images/movifylogo.png" alt="Movify" class="admin-logo-img">
            </a>
            <span class="admin-badge">Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-link active">
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
            <a href="manage-coming-soon.php" class="admin-nav-link">
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

    <!-- Ana İçerik -->
    <main class="admin-content">
        <!-- Mobil Toggle Butonu -->
        <button class="admin-mobile-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 style="margin-bottom: 24px;">Dashboard</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="stat-card">
                <div style="color:var(--text-muted); font-size:0.9rem;">Toplam Kullanıcı</div>
                <div class="stat-value"><?= number_format($statUsers) ?></div>
            </div>
            <div class="stat-card">
                <div style="color:var(--text-muted); font-size:0.9rem;">Film Sayısı</div>
                <div class="stat-value"><?= number_format($statMovies) ?></div>
            </div>
            <div class="stat-card">
                <div style="color:var(--text-muted); font-size:0.9rem;">Dizi Sayısı</div>
                <div class="stat-value"><?= number_format($statSeries) ?></div>
            </div>
            <div class="stat-card">
                <div style="color:var(--text-muted); font-size:0.9rem;">Amerikan Filmleri</div>
                <div class="stat-value"><?= number_format($statAmericanMovies) ?></div>
            </div>
            <div class="stat-card">
                <div style="color:var(--text-muted); font-size:0.9rem;">Toplam İzlenme</div>
                <div class="stat-value"><?= number_format($statViews) ?></div>
            </div>
        </div>

        <h2 style="margin-bottom: 16px;">Son Eklenen İçerikler</h2>
        <div class="stat-card" style="padding:0; overflow:hidden;">
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Tür</th>
                            <th>Ekleme Tarihi</th>
                            <th>İzlenme</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = $pdo->query("SELECT id, title, type, created_at, views_count FROM content ORDER BY created_at DESC LIMIT 5")->fetchAll();
                        foreach ($recent as $row):
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= e($row['title']) ?></td>
                            <td><?= $row['type'] === 'movie' ? 'Film' : 'Dizi' ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><?= number_format($row['views_count']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
