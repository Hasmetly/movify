<?php
/**
 * Movify - Yakında Gelecekler
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';
requireProfile();

$pdo = db();

// Yakında eklenecek içerikleri çek
$stmt = $pdo->query("SELECT * FROM content WHERE is_coming_soon = 1 ORDER BY coming_soon_date ASC, created_at DESC");
$comingSoonItems = $stmt->fetchAll();

// Bildirimleri ve duyuruları çek
$stmtNotif = $pdo->query("SELECT * FROM notifications WHERE is_active = 1 ORDER BY expected_date ASC, created_at DESC");
$notifications = $stmtNotif->fetchAll();

$pageTitle = 'Yakında Gelecekler - Movify';
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* ===== COMING SOON PAGE STYLES ===== */
.cs-header {
    margin-top: 100px;
    margin-bottom: 40px;
    text-align: center;
}
.cs-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 10px;
    background: linear-gradient(90deg, #fff, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.cs-header p {
    color: var(--text-muted);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

.cs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 30px;
    padding-bottom: 60px;
}

.cs-card {
    background: var(--bg-secondary);
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    border: 1px solid var(--border-color);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.cs-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    border-color: var(--accent-purple);
}

.cs-cover-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 2/3;
    overflow: hidden;
}

.cs-cover-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.cs-card:hover .cs-cover-wrapper img {
    transform: scale(1.08);
}

.cs-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.6) 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 15px;
}

.cs-badge {
    align-self: flex-end;
    background: rgba(236, 72, 153, 0.9);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 4px 10px rgba(236, 72, 153, 0.3);
}

.cs-type {
    align-self: flex-start;
    background: rgba(0,0,0,0.6);
    color: #a78bfa;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(167, 139, 250, 0.3);
}

.cs-info {
    padding: 15px;
    background: var(--bg-secondary);
}

.cs-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-main);
    margin: 0 0 8px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cs-date {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #10b981;
    font-size: 0.9rem;
    font-weight: 500;
}

.cs-empty {
    text-align: center;
    padding: 80px 20px;
    color: var(--text-muted);
    background: var(--bg-secondary);
    border-radius: 16px;
    border: 1px dashed var(--border-color);
    grid-column: 1 / -1;
}

@media (max-width: 768px) {
    .cs-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 15px;
    }
    .cs-header h1 {
        font-size: 2rem;
    }
}
@media (max-width: 480px) {
    .cs-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .cs-header {
        margin-top: 80px;
        margin-bottom: 30px;
    }
    .cs-header h1 {
        font-size: 1.6rem;
    }
    .cs-title {
        font-size: 1rem;
    }
}
</style>

<div class="container">
    <div class="cs-header">
        <h1>Yakında Gelecekler</h1>
        <p>Movify'a çok yakında eklenecek olan en yeni ve heyecan verici içerikleri keşfedin.</p>
    </div>

    <?php if (count($notifications) > 0): ?>
    <h2 style="color:var(--text-main); margin-bottom: 20px; font-size:1.5rem; display:flex; align-items:center; gap:10px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
        Duyurular & Bildirimler
    </h2>
    <div class="cs-grid" style="margin-bottom: 50px;">
        <?php foreach ($notifications as $notif): ?>
            <div class="cs-card">
                <div class="cs-cover-wrapper">
                    <?php if ($notif['image']): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($notif['image']) ?>" alt="<?= e($notif['title']) ?>">
                    <?php else: ?>
                        <img src="<?= BASE_URL ?>/assets/images/placeholder.jpg" alt="<?= e($notif['title']) ?>">
                    <?php endif; ?>
                    <div class="cs-overlay">
                        <span class="cs-type" style="background:rgba(16, 185, 129, 0.6); border-color: rgba(16, 185, 129, 0.3); color: #fff;">DUYURU</span>
                    </div>
                </div>
                <div class="cs-info">
                    <h3 class="cs-title"><?= e($notif['title']) ?></h3>
                    <?php if ($notif['message']): ?>
                        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:8px; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= e($notif['message']) ?></p>
                    <?php endif; ?>
                    <div class="cs-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <?= $notif['expected_date'] ? date('d.m.Y', strtotime($notif['expected_date'])) : 'Yakında' ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <h2 style="color:var(--text-main); margin-bottom: 20px; font-size:1.5rem; display:flex; align-items:center; gap:10px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
        Yakında Eklenecek Dizi & Filmler
    </h2>
    <?php endif; ?>

    <div class="cs-grid">
        <?php if (count($comingSoonItems) > 0): ?>
            <?php foreach ($comingSoonItems as $item): ?>
                <?php
                    $cover = $item['cover_image'] ? BASE_URL . '/uploads/' . $item['cover_image'] : BASE_URL . '/assets/images/placeholder.jpg';
                    $typeText = $item['type'] === 'movie' ? 'FİLM' : 'DİZİ';
                    
                    // Tarih Formatlama
                    $dateText = 'Çok Yakında';
                    if (!empty($item['coming_soon_date'])) {
                        $timestamp = strtotime($item['coming_soon_date']);
                        // Türkçe ay isimleri (Basit yöntem)
                        $months = ['', 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                        $m = (int)date('m', $timestamp);
                        $y = date('Y', $timestamp);
                        $dateText = $months[$m] . ' ' . $y;
                    }
                ?>
                <a href="<?= BASE_URL ?>/content-detail.php?id=<?= $item['id'] ?>" class="cs-card">
                    <div class="cs-cover-wrapper">
                        <img src="<?= $cover ?>" alt="<?= e($item['title']) ?>">
                        <div class="cs-overlay">
                            <span class="cs-type"><?= $typeText ?></span>
                            <span class="cs-badge">YAKINDA</span>
                        </div>
                    </div>
                    <div class="cs-info">
                        <h3 class="cs-title"><?= e($item['title']) ?></h3>
                        <div class="cs-date">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <?= $dateText ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="cs-empty">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--border-color)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:20px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <h3 style="color:var(--text-main); margin-bottom:10px;">Henüz bir içerik yok</h3>
                <p>Şu anda yakında eklenecek bir içerik bulunmuyor. Lütfen daha sonra tekrar kontrol edin.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
