<?php
/**
 * Movify - Anasayfa
 * Film/Dizi kütüphanesi, bannerlar ve günlük öneriler
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';
requireProfile();

$pdo = db();
$profileId = currentProfileId();

// Aktif filtre (film veya dizi)
$contentType = $_GET['type'] ?? 'all'; // all, movie, series

// ----- GÜNLÜK ÖNE ÇIKAN İÇERİKLER (Featured Slider) -----
$stmtFeatured = $pdo->query("SELECT * FROM content WHERE is_featured = 1 AND is_coming_soon = 0 ORDER BY RAND() LIMIT 5");
$featureds = $stmtFeatured->fetchAll();

// Featured yoksa rastgele içerikler al
if (empty($featureds)) {
    $stmtFeatured = $pdo->query("SELECT * FROM content WHERE is_coming_soon = 0 ORDER BY RAND() LIMIT 5");
    $featureds = $stmtFeatured->fetchAll();
}

// ----- TÜM BANNER KATEGORİLERİ İÇİN VERİ ÇEKİM FONKSİYONU -----
function getContentByQuery($pdo, $query, $params = [], $limit = 20) {
    $stmt = $pdo->prepare($query . " LIMIT ?");
    $params[] = $limit;
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getContentByCategory($pdo, $slug, $type = 'all', $limit = 20) {
    $typeFilter = '';
    $params = [$slug];
    if ($type === 'movie') { $typeFilter = ' AND c.type = "movie"'; }
    elseif ($type === 'series') { $typeFilter = ' AND c.type = "series"'; }
    
    $sql = "SELECT c.* FROM content c 
            JOIN content_categories cc ON c.id = cc.content_id 
            JOIN categories cat ON cc.category_id = cat.id 
            WHERE cat.slug = ? AND c.is_coming_soon = 0 {$typeFilter}
            ORDER BY c.created_at DESC LIMIT ?";
    $params[] = $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Tip filtreleme koşulu
$typeCondition = '';
if ($contentType === 'movie') { $typeCondition = " AND type = 'movie'"; }
elseif ($contentType === 'series') { $typeCondition = " AND type = 'series'"; }

// ----- BANNER VERİLERİ -----
// 0. Kaldığın Yerden Devam Et
$stmtContinue = $pdo->prepare("SELECT wh.*, c.title as content_title, c.cover_image, c.banner_image, c.type, ep.episode_number, ep.season_number, ep.title as episode_title 
    FROM watch_history wh 
    JOIN content c ON wh.content_id = c.id 
    LEFT JOIN episodes ep ON wh.episode_id = ep.id
    WHERE wh.profile_id = ? AND wh.is_finished = 0 AND wh.watched_position_seconds > 0
    ORDER BY wh.updated_at DESC LIMIT 15");
$stmtContinue->execute([$profileId]);
$continueWatching = $stmtContinue->fetchAll();

// 1. Listem (Watchlist)
$stmtWatchlist = $pdo->prepare("SELECT c.* FROM content c JOIN watchlist w ON c.id = w.content_id WHERE w.profile_id = ? ORDER BY w.added_at DESC LIMIT 20");
$stmtWatchlist->execute([$profileId]);
$watchlist = $stmtWatchlist->fetchAll();

// 2. Amerikan Dizileri
$americanSeries = getContentByCategory($pdo, 'amerikan-dizileri', $contentType);

// 2.5 Amerikan Filmleri
$americanMovies = getContentByCategory($pdo, 'amerikan-filmleri', $contentType);

// 3. Senin için seçtiklerimiz
$forYou = getContentByCategory($pdo, 'senin-icin-sectiklerimiz', $contentType);

// 4. Eleştirmenlerden Tam Not Alanlar
$criticsChoice = getContentByCategory($pdo, 'elestirmenlerden-tam-not-alanlar', $contentType);

// 5. Yeni İçerikler
$newContent = getContentByQuery($pdo, "SELECT * FROM content WHERE is_coming_soon = 0 {$typeCondition} ORDER BY created_at DESC", []);

// 6. Sadece Movify'da
$exclusive = getContentByCategory($pdo, 'sadece-movifyda', $contentType);

// 7. Ödüllü Yapımlar
$awarded = getContentByCategory($pdo, 'odullu-yapimlar', $contentType);

// 8. Belgeseller
$documentaries = getContentByCategory($pdo, 'belgeseller', $contentType);

// 9. Animeler
$animes = getContentByCategory($pdo, 'animeler', $contentType);

// 10. Gişe Rekortmenleri
$blockbusters = getContentByCategory($pdo, 'gise-rekortmenleri', $contentType);

// 11. Çok İzlenenler
$mostWatched = getContentByQuery($pdo, "SELECT * FROM content WHERE is_coming_soon = 0 {$typeCondition} ORDER BY views_count DESC", []);

// 12. Holywood Yapımları
$hollywood = getContentByCategory($pdo, 'holywood-yapimlari', $contentType);

// 13. Movify'da Bugün Top 10 Film
$top10Movies = getContentByQuery($pdo, "SELECT * FROM content WHERE type = 'movie' AND is_coming_soon = 0 ORDER BY views_count DESC", [], 10);

// 14. Movify'da Bugün Top 10 Dizi
$top10Series = getContentByQuery($pdo, "SELECT * FROM content WHERE type = 'series' AND is_coming_soon = 0 ORDER BY views_count DESC", [], 10);

// Tüm kategorileri çek (dropdown için)
$stmtCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$allCategories = $stmtCategories->fetchAll();

$pageTitle = 'Anasayfa - Movify';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== HOME PAGE RESPONSIVE OVERRIDES ===== */

/* --- Tablet & Large Phone (≤768px) --- */
@media (max-width: 768px) {
    /* Hero slide padding */
    .hero-slide {
        padding-left: 4% !important;
        padding-right: 4% !important;
        padding-bottom: 18% !important;
    }

    /* Hero content area */
    .hero-banner__content {
        max-width: 100% !important;
    }

    /* Hero title */
    .hero-banner__title {
        font-size: clamp(2rem, 6vw, 3rem) !important;
        letter-spacing: 1px !important;
        margin-bottom: 10px !important;
    }

    /* Hero badge */
    .hero-banner__badge {
        font-size: 0.85rem !important;
        padding: 4px 10px !important;
        margin-bottom: 10px !important;
    }

    /* Hero description */
    .hero-banner__desc {
        font-size: 0.95rem !important;
        margin-bottom: 15px !important;
        line-height: 1.5 !important;
    }

    /* Hero buttons container */
    .hero-slide > .hero-banner__content > div:last-child {
        gap: 10px !important;
    }

    /* Hero buttons (Oynat / Daha Fazla Bilgi) */
    .hero-slide button,
    .hero-banner__content button {
        padding: 10px 18px !important;
        font-size: 1rem !important;
    }
    .hero-banner__content button svg {
        width: 22px !important;
        height: 22px !important;
    }

    /* Hero arrows */
    .hero-arrow {
        width: 36px !important;
        height: 36px !important;
    }

    /* Category grid view (when browsing by category) */
    .content-row .container > div[style*="grid-template-columns"] {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)) !important;
        gap: 12px !important;
    }

    /* Content slider cards */
    .content-card {
        min-width: 130px !important;
    }
    .content-card--large {
        min-width: 200px !important;
    }
    .content-card--top10 {
        min-width: 130px !important;
    }

    /* Content row titles */
    .content-row__title {
        font-size: 1.3rem !important;
    }

    /* Hero banner height */
    .hero-banner {
        min-height: 55vh !important;
    }

    /* Filter buttons */
    .content-filter .container {
        gap: 8px;
    }
    .content-filter__btn {
        padding: 6px 14px;
        font-size: 0.9rem;
    }

    /* Continue watching season/episode text */
    .content-card__title span[style*="font-size:0.85rem"] {
        font-size: 0.75rem !important;
    }
}

/* --- Small Phone (≤480px) --- */
@media (max-width: 480px) {
    /* Hero slide padding */
    .hero-slide {
        padding-left: 3% !important;
        padding-right: 3% !important;
        padding-bottom: 22% !important;
    }

    /* Hero title */
    .hero-banner__title {
        font-size: clamp(1.5rem, 7vw, 2.2rem) !important;
        letter-spacing: 0.5px !important;
    }

    /* Hero description */
    .hero-banner__desc {
        font-size: 0.85rem !important;
        margin-bottom: 12px !important;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Hero badge */
    .hero-banner__badge {
        font-size: 0.75rem !important;
        padding: 3px 8px !important;
    }

    /* Hero buttons */
    .hero-slide button,
    .hero-banner__content button {
        padding: 8px 14px !important;
        font-size: 0.9rem !important;
    }
    .hero-banner__content button svg {
        width: 18px !important;
        height: 18px !important;
    }

    /* Hero banner height */
    .hero-banner {
        min-height: 50vh !important;
    }

    /* Category grid view */
    .content-row .container > div[style*="grid-template-columns"] {
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)) !important;
        gap: 8px !important;
    }

    /* Content slider cards */
    .content-card {
        min-width: 110px !important;
    }
    .content-card--large {
        min-width: 160px !important;
    }
    .content-card--top10 {
        min-width: 110px !important;
    }

    /* Content row titles */
    .content-row__title {
        font-size: 1.1rem !important;
    }

    /* Content card titles */
    .content-card__title {
        font-size: 0.8rem !important;
    }

    /* Filter buttons */
    .content-filter__btn {
        padding: 5px 10px;
        font-size: 0.8rem;
    }

    /* Category page title */
    .content-row__title[style*="font-size:2rem"] {
        font-size: 1.4rem !important;
    }
}

/* Prevent horizontal overflow globally */
body {
    overflow-x: hidden;
}
.main-content {
    overflow-x: hidden;
}
</style>

<!-- Hero Banner Slider -->
<section class="hero-banner" id="hero-banner">
    <?php foreach ($featureds as $index => $hero): ?>
    <div class="hero-slide" id="hero-slide-<?= $index ?>" style="<?= $index === 0 ? 'display:flex;' : 'display:none;' ?> width:100%; height:100%; position:absolute; top:0; left:0; flex-direction:column; align-items:flex-start; justify-content:flex-end; padding-bottom: 8%; padding-left: 5%;" onclick="window.location.href='<?= BASE_URL ?>/content-detail.php?id=<?= $hero['id'] ?>'">
        <div class="hero-banner__backdrop" style="position:absolute; top:0; left:0; width:100%; height:100%; overflow:hidden; z-index:0;">
            <?php if ($hero['banner_image']): ?>
                <!-- Tam Boyut Görsel -->
                <img src="<?= BASE_URL ?>/uploads/<?= e($hero['banner_image']) ?>" alt="Banner" class="hero-banner__image" style="width:100%; height:100%; object-fit:cover; object-position:top center;">
            <?php else: ?>
                <div style="width:100%; height:100%; background: linear-gradient(135deg, #0d0d0f, #1a1a22);"></div>
            <?php endif; ?>
            <!-- Sol ve Alta Doğru Gradient (Netflix Style) -->
            <div class="hero-banner__overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(to right, rgba(13,13,15,0.95) 0%, rgba(13,13,15,0.4) 50%, transparent 100%), linear-gradient(to top, rgba(13,13,15,1) 0%, rgba(13,13,15,0.2) 50%, transparent 100%); z-index:1;"></div>
        </div>
        
        <div class="hero-banner__content" style="z-index:2; text-align:left; max-width: 700px;">
            <span class="hero-banner__badge" style="background:var(--accent-purple); color:#fff; border:none; border-radius:4px; margin-bottom:15px; display:inline-block; font-size:1rem; padding:6px 14px;">ÖNE ÇIKAN</span>
            <h1 class="hero-banner__title" style="font-size:clamp(3rem, 5vw, 5rem); font-weight:700; text-transform:uppercase; margin-bottom:15px; text-shadow:2px 2px 10px rgba(0,0,0,0.8); line-height:1.1; font-family:'Bebas Neue', sans-serif; letter-spacing:3px; color:#fff;"><?= e($hero['title']) ?></h1>
            <p class="hero-banner__desc" style="font-size:1.15rem; line-height:1.6; color:rgba(255,255,255,0.85); text-shadow:1px 1px 5px rgba(0,0,0,0.8); margin-bottom:25px;"><?= e(mb_substr($hero['description'], 0, 180)) ?>...</p>
            <div style="display:flex; gap:15px; align-items:center;">
                <button onclick="window.location.href='<?= BASE_URL ?>/content-detail.php?id=<?= $hero['id'] ?>'; event.stopPropagation();" style="background:#fff; color:#000; border:none; padding:12px 30px; font-size:1.2rem; font-weight:600; border-radius:5px; cursor:pointer; display:flex; align-items:center; gap:10px; transition:0.2s;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg> Oynat
                </button>
                <button onclick="window.location.href='<?= BASE_URL ?>/content-detail.php?id=<?= $hero['id'] ?>'; event.stopPropagation();" style="background:rgba(109, 109, 110, 0.7); color:#fff; border:none; padding:12px 30px; font-size:1.2rem; font-weight:600; border-radius:5px; cursor:pointer; display:flex; align-items:center; gap:10px; transition:0.2s;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg> Daha Fazla Bilgi
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (count($featureds) > 1): ?>
    <button class="hero-arrow hero-arrow--left" onclick="changeHeroSlide(-1); event.stopPropagation();">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="hero-arrow hero-arrow--right" onclick="changeHeroSlide(1); event.stopPropagation();">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <?php endif; ?>
</section>

<script>
let currentHeroIndex = 0;
const totalHeroSlides = <?= count($featureds) ?>;

function changeHeroSlide(dir) {
    if(totalHeroSlides <= 1) return;
    document.getElementById('hero-slide-' + currentHeroIndex).style.display = 'none';
    currentHeroIndex = (currentHeroIndex + dir + totalHeroSlides) % totalHeroSlides;
    document.getElementById('hero-slide-' + currentHeroIndex).style.display = 'flex';
}

// Otomatik kaydırma
if(totalHeroSlides > 1) {
    setInterval(() => { changeHeroSlide(1); }, 6000);
}
</script>

<!-- Film / Dizi Filtre Butonları -->
<div class="content-filter" id="content-filter">
    <div class="container">
        <a href="<?= BASE_URL ?>/home.php?type=all" class="content-filter__btn <?= $contentType === 'all' ? 'active' : '' ?>">Tümü</a>
        <a href="<?= BASE_URL ?>/home.php?type=movie" class="content-filter__btn <?= $contentType === 'movie' ? 'active' : '' ?>">Filmler</a>
        <a href="<?= BASE_URL ?>/home.php?type=series" class="content-filter__btn <?= $contentType === 'series' ? 'active' : '' ?>">Diziler</a>
    </div>
</div>

<main class="main-content">
    
    <?php
    if (isset($_GET['cat'])):
        $catId = intval($_GET['cat']);
        $stmtCat = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmtCat->execute([$catId]);
        $currentCat = $stmtCat->fetch();
        
        $sql = "SELECT c.* FROM content c JOIN content_categories cc ON c.id = cc.content_id WHERE cc.category_id = ? {$typeCondition} ORDER BY c.created_at DESC";
        $stmtItems = $pdo->prepare($sql);
        $stmtItems->execute([$catId]);
        $catItems = $stmtItems->fetchAll();
    ?>
        <section class="content-row">
            <div class="container">
                <h2 class="content-row__title" style="font-size:2rem; margin-bottom: 30px;"><?= $currentCat ? e($currentCat['name']) : 'Kategori' ?></h2>
                <?php if (empty($catItems)): ?>
                    <div style="color:var(--text-faint);">Bu kategoride henüz içerik bulunmuyor.</div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px;">
                        <?php foreach ($catItems as $item): ?>
                            <div class="content-card" style="width: 100%; min-width: auto; margin-right: 0;" onclick="window.location.href='<?= BASE_URL ?>/content-detail.php?id=<?= $item['id'] ?>'">
                                <div class="content-card__image" style="aspect-ratio: 2/3; overflow: hidden; position: relative; border-radius: 8px;">
                                    <?php $imgSrc = $item['cover_image'] ? $item['cover_image'] : $item['banner_image']; ?>
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= BASE_URL ?>/uploads/<?= e($imgSrc) ?>" alt="<?= e($item['title']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
                                    <?php else: ?>
                                        <div class="content-card__placeholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;"><?= e(mb_substr($item['title'], 0, 1)) ?></div>
                                    <?php endif; ?>
                                    <div class="content-card__overlay">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                                    </div>
                                </div>
                                <span class="content-card__title" style="display:block; margin-top:8px;"><?= e($item['title']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
    <?php
    // Slider Oluşturucu Helper Fonksiyonu (DRY Prensibi)
    function renderSlider($title, $items, $cardType = 'normal') {
        if (empty($items)) {
            return; // İçerik yoksa slider'ı hiç gösterme (Filtreleme sisteminde de çalışır)
        }
        ?>
        <section class="content-row">
            <div class="container">
                <h2 class="content-row__title" style="<?= $cardType=='large' ? 'font-size:1.8rem; font-weight:700; text-transform:uppercase;' : '' ?>"><?= e($title) ?></h2>
                <div class="content-slider">
                    <button class="slider-arrow slider-arrow--left" aria-label="Sola kaydır">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg>
                    </button>
                    <div class="content-slider__track">
                        <?php foreach ($items as $index => $item): ?>
                        <?php 
                            if ($cardType == 'continue') {
                                $clickUrl = BASE_URL . '/play.php?id=' . $item['content_id'];
                                if (!empty($item['episode_id'])) {
                                    $clickUrl .= '&episode=' . $item['episode_id'];
                                }
                            } else {
                                $clickUrl = BASE_URL . '/content-detail.php?id=' . $item['id'];
                            }
                        ?>
                        <div class="content-card <?= $cardType=='continue' ? 'content-card--continue' : ($cardType=='large' ? 'content-card--large' : ($cardType=='top10' ? 'content-card--top10' : '')) ?>" onclick="window.location.href='<?= $clickUrl ?>'">
                            <?php if ($cardType=='top10'): ?>
                                <span class="content-card__rank"><?= $index + 1 ?></span>
                            <?php endif; ?>
                            
                            <div class="content-card__image">
                                <?php 
                                    $imgSrc = $item['cover_image'] ? $item['cover_image'] : ($item['banner_image'] ?? null); 
                                ?>
                                <?php if ($imgSrc): ?>
                                    <img src="<?= BASE_URL ?>/uploads/<?= e($imgSrc) ?>" alt="Banner" loading="lazy">
                                <?php else: ?>
                                    <div class="content-card__placeholder"><?= e(mb_substr($item['content_title'] ?? $item['title'], 0, 1)) ?></div>
                                <?php endif; ?>
                                
                                <div class="content-card__overlay">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                                </div>
                            </div>
                            
                            <?php if ($cardType=='continue'): ?>
                                <?php 
                                    $progress = ($item['total_duration_seconds'] > 0) 
                                        ? ($item['watched_position_seconds'] / $item['total_duration_seconds']) * 100 
                                        : 0;
                                ?>
                                <div class="content-card__progress-container" style="width:100%; height:4px; background:rgba(255,255,255,0.2); position:absolute; bottom:0; left:0; border-radius:0 0 8px 8px; overflow:hidden; z-index:5;">
                                    <div class="content-card__progress-bar" style="width: <?= round($progress) ?>%; height:100%; background:var(--accent-pink);"></div>
                                </div>
                            <?php endif; ?>
                            
                            <span class="content-card__title" style="display:block; margin-top:8px;">
                                <?= e($item['content_title'] ?? $item['title']) ?>
                                <?php if ($cardType == 'continue' && $item['type'] == 'series' && !empty($item['episode_number'])): ?>
                                    <span style="display:block; font-size:0.85rem; color:var(--text-muted); margin-top:2px;">Sezon <?= $item['season_number'] ?> Bölüm <?= $item['episode_number'] ?></span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="slider-arrow slider-arrow--right" aria-label="Sağa kaydır">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg>
                    </button>
                </div>
            </div>
        </section>
        <?php
    }

    // İstenen 14 Sıralama
    // 0. Kaldığın Yerden Devam Et
    if (!empty($continueWatching)) {
        renderSlider('Kaldığın Yerden Devam Et', $continueWatching, 'continue');
    }

    // 1. Listem
    renderSlider('Listem', $watchlist);

    // 2. Amerikan Dizileri
    renderSlider('Amerikan Dizileri', $americanSeries);

    // 2.5 Amerikan Filmleri
    renderSlider('Amerikan Filmleri', $americanMovies);

    // 3. Senin için seçtiklerimiz
    renderSlider('Senin için seçtiklerimiz', $forYou);

    // 4. Eleştirmenlerden Tam Not Alanlar
    renderSlider('Eleştirmenlerden Tam Not Alanlar', $criticsChoice);

    // 5. Yeni İçerikler
    renderSlider('Yeni İçerikler', $newContent);

    // 6. Sadece Movify'da (Büyük kartlar kullanılsın)
    renderSlider('Sadece Movify\'da', $exclusive, 'large');

    // 7. Ödüllü Yapımlar
    renderSlider('Ödüllü Yapımlar', $awarded);

    // 8. Belgeseller
    renderSlider('Belgeseller', $documentaries);

    // 9. Animeler
    renderSlider('Animeler', $animes);

    // 10. Gişe Rekortmenleri
    renderSlider('Gişe Rekortmenleri', $blockbusters);

    // 11. Çok İzlenenler
    renderSlider('Çok İzlenenler', $mostWatched);

    // 12. Holywood Yapımları
    renderSlider('Holywood Yapımları', $hollywood);

    // 13. Movify'da Bugün Top 10 Film
    renderSlider('Movify\'da Bugün Top 10 Film', $top10Movies, 'top10');

    // 14. Movify'da Bugün Top 10 Dizi
    renderSlider('Movify\'da Bugün Top 10 Dizi', $top10Series, 'top10');
    ?>
    <?php endif; ?>

</main>

<!-- Paylaşım Modali -->
<div class="share-modal" id="share-modal">
    <div class="share-modal__overlay" onclick="closeShareModal()"></div>
    <div class="share-modal__content">
        <h3>Paylaş</h3>
        <div class="share-modal__buttons">
            <a href="#" class="share-btn share-btn--twitter" id="share-twitter" target="_blank" title="X (Twitter)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="#" class="share-btn share-btn--facebook" id="share-facebook" target="_blank" title="Facebook">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="#" class="share-btn share-btn--whatsapp" id="share-whatsapp" target="_blank" title="WhatsApp">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            <button class="share-btn share-btn--copy" id="share-copy" title="Linki Kopyala">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
        </div>
        <button class="btn btn--ghost btn--sm" onclick="closeShareModal()">Kapat</button>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
