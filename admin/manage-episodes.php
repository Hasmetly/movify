<?php
/**
 * Movify - Admin: Dizi Bölümleri Yönetimi (Dinamik Form AJAX olmadan basit PHP versiyonu)
 */
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/home.php');
}

$pdo = db();
$msg = '';

// Bölüm Ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_episode') {
    $content_id = intval($_POST['content_id'] ?? 0);
    $season_number = intval($_POST['season_number'] ?? 1);
    $episode_number = intval($_POST['episode_number'] ?? 1);
    $title = trim($_POST['title'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    
    // Intro atlama süreleri (opsiyonel)
    $intro_start = !empty($_POST['intro_start']) ? intval($_POST['intro_start']) : null;
    $intro_end = !empty($_POST['intro_end']) ? intval($_POST['intro_end']) : null;

    if ($content_id > 0 && !empty($video_url)) {
        $stmt = $pdo->prepare("INSERT INTO episodes (content_id, season_number, episode_number, title, video_url, intro_start_seconds, intro_end_seconds) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$content_id, $season_number, $episode_number, $title, $video_url, $intro_start, $intro_end]);
        $msg = "Bölüm eklendi.";
    }
}

// Bölüm Sil
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM episodes WHERE id = ?")->execute([intval($_GET['delete'])]);
    redirect('/admin/manage-episodes.php');
}

// Dizileri çek
$series = $pdo->query("SELECT id, title FROM content WHERE type = 'series' ORDER BY title ASC")->fetchAll();

// Bölümleri çek
$episodes = $pdo->query("SELECT e.*, c.title as series_title FROM episodes e LEFT JOIN content c ON e.content_id = c.id ORDER BY e.id DESC")->fetchAll();

$pageTitle = 'Bölüm Yönetimi - Admin';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <style>
        /* Choices.js Movify Koyu Tema Uyarlaması */
        .choices__inner {
            background-color: var(--bg-tertiary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            color: var(--text-main) !important;
            min-height: 48px;
            display: flex;
            align-items: center;
        }
        .choices.is-focused .choices__inner, .choices.is-open .choices__inner {
            border-color: var(--accent-purple) !important;
        }
        .choices__input {
            background-color: transparent !important;
            color: var(--text-main) !important;
        }
        .choices__input--cloned {
            background-color: var(--bg-tertiary) !important;
            color: var(--text-main) !important;
        }
        .choices__list--dropdown, .choices__list[aria-expanded] {
            background-color: var(--bg-secondary) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            z-index: 100;
        }
        .choices__list--dropdown .choices__item--selectable {
            color: var(--text-main) !important;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: rgba(147, 51, 234, 0.4) !important;
            color: #fff !important;
        }
        .choices[data-type*="select-one"] .choices__button {
            filter: invert(1);
        }
        .choices__list--dropdown .choices__item {
            padding: 12px 16px;
        }
    </style>
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
            <a href="manage-episodes.php" class="admin-nav-link active">
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

    <main class="admin-content">
        <!-- Mobil Toggle Butonu -->
        <button class="admin-mobile-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 style="margin-bottom: 24px;">Bölüm (Dizi) Yönetimi</h1>
        
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <!-- Form Bölümü -->
            <div class="stat-card" style="flex: 1; min-width: 300px; max-width: 500px; height: fit-content;">
                <h3 style="margin-bottom: 20px;">Yeni Bölüm Ekle</h3>
                <form action="manage-episodes.php" method="POST">
                    <input type="hidden" name="action" value="add_episode">
                    
                    <div class="form-group">
                        <label class="form-label">Dizi Seç (Arayabilirsiniz)</label>
                        <select name="content_id" id="content_search_select" required>
                            <option value="">-- Dizi Seç --</option>
                            <?php foreach ($series as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Sezon</label>
                            <input type="number" name="season_number" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Bölüm No</label>
                            <input type="number" name="episode_number" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Bölüm Adı (Opsiyonel)</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Video URL (YouTube veya MP4)</label>
                        <input type="text" name="video_url" class="form-control" required>
                    </div>

                    <h4 style="margin: 20px 0 10px; font-size:1rem; color:var(--text-muted);">İntro Atlama (Saniye cinsinden)</h4>
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">İntro Başlangıç</label>
                            <input type="number" name="intro_start" class="form-control" placeholder="Örn: 10">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">İntro Bitiş</label>
                            <input type="number" name="intro_end" class="form-control" placeholder="Örn: 90">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn--primary" style="width:100%;">Bölüm Ekle</button>
                </form>
            </div>
            
            <!-- Mevcut Bölümler -->
            <div class="stat-card" style="flex: 2; min-width: 300px; padding:0; overflow:hidden; height: fit-content;">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dizi Adı</th>
                                <th>Sezon</th>
                                <th>Bölüm</th>
                                <th>Bölüm Adı</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($episodes as $row): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= e($row['series_title'] ?? 'Bilinmiyor') ?></td>
                                <td><?= $row['season_number'] ?></td>
                                <td><?= $row['episode_number'] ?></td>
                                <td><?= e($row['title']) ?></td>
                                <td>
                                    <a href="manage-episodes.php?delete=<?= $row['id'] ?>" class="btn btn--sm btn--ghost" style="color:#f87171;" onclick="return confirm('Bölümü silmek istediğinize emin misiniz?')">Sil</a>
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

<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const element = document.getElementById('content_search_select');
    if (element) {
        new Choices(element, {
            searchEnabled: true,
            searchPlaceholderValue: 'Dizi ismini yazın...',
            itemSelectText: '',
            noResultsText: 'Sonuç bulunamadı',
            noChoicesText: 'Seçilecek dizi yok'
        });
    }
});
</script>

</body>
</html>
