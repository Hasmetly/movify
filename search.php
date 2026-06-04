<?php
/**
 * Movify - Arama Sonuçları Sayfası
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';
requireProfile();

$pdo = db();
$profileId = currentProfileId();

$q = trim($_GET['q'] ?? '');
$results = [];

if (!empty($q)) {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE title LIKE ? OR description LIKE ? ORDER BY id DESC");
    $stmt->execute(['%' . $q . '%', '%' . $q . '%']);
    $results = $stmt->fetchAll();
}

$pageTitle = 'Arama Sonuçları: ' . e($q) . ' - Movify';
include __DIR__ . '/includes/header.php';
?>

<style>
    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 768px) {
        .container { margin-top: 80px !important; padding-left: 15px !important; padding-right: 15px !important; }
        .content-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)) !important; gap: 15px !important; }
    }
    @media (max-width: 480px) {
        .container { margin-top: 70px !important; padding-left: 10px !important; padding-right: 10px !important; }
        .content-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important; gap: 10px !important; }
        h2 { font-size: 1.3rem !important; }
    }
</style>

<div class="container" style="margin-top: 100px; padding-bottom: 80px; min-height: 60vh;">
    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 10px;">
        "<?= e($q) ?>" için arama sonuçları
    </h2>
    <p style="color: var(--text-muted); margin-bottom: 30px;">
        <?= count($results) ?> sonuç bulundu.
    </p>

    <?php if (count($results) > 0): ?>
    <div class="content-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px;">
        <?php foreach ($results as $item): ?>
            <?php 
                $cover = $item['cover_image'] ? (strpos($item['cover_image'], 'http') === 0 ? $item['cover_image'] : BASE_URL . '/uploads/' . $item['cover_image']) : ''; 
            ?>
            <a href="<?= BASE_URL ?>/content-detail.php?id=<?= $item['id'] ?>" class="content-card" style="text-decoration: none;">
                <div class="content-card__image">
                    <?php if ($cover): ?>
                        <img src="<?= e($cover) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="content-card__placeholder">
                            <?= e(mb_substr($item['title'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="content-card__overlay">
                        <div class="play-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        </div>
                        <div class="content-card__info">
                            <h4 class="title"><?= e($item['title']) ?></h4>
                            <div class="meta">
                                <span class="match"><?= e($item['imdb_rating'] ?? 'N/A') ?> IMDB</span>
                                <span class="age-rating"><?= e($item['age_rating'] ?? '+13') ?></span>
                                <span><?= e($item['release_year'] ?? '') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px 0; color: var(--text-muted);">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:20px; opacity:0.5;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <h3 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 10px;">Sonuç Bulunamadı</h3>
            <p>Aradığınız kritere uygun dizi veya film bulunamadı.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
