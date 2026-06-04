<?php
/**
 * Movify - Admin: İçerik Yönetimi (Film / Dizi Ekleme & Düzenleme)
 */
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/home.php');
}

$pdo = db();
$msg = '';
$msgType = 'success';

// Yükleme dizini oluştur
$uploadDir = dirname(__DIR__) . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Kategorileri Veritabanından Al
$allCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// İçerik Ekleme / Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_content') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? 'movie';
    $description = trim($_POST['description'] ?? '');
    $release_year = intval($_POST['release_year'] ?? date('Y'));
    $age_rating = $_POST['age_rating'] ?? '13+';
    $imdb_rating = floatval($_POST['imdb_rating'] ?? 0);
    $duration_minutes = intval($_POST['duration_minutes'] ?? 0);
    $director = trim($_POST['director'] ?? '');
    $original_language = trim($_POST['original_language'] ?? '');
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_coming_soon = isset($_POST['is_coming_soon']) ? 1 : 0;
    $is_exclusive = isset($_POST['is_exclusive']) ? 1 : 0;
    $is_top10 = isset($_POST['is_top10']) ? 1 : 0;
    
    // Video Linkleri (Film için)
    $trailerUrl = trim($_POST['trailer_url'] ?? '');
    $videoUrl = trim($_POST['video_url'] ?? ''); // Sadece film için
    
    // Video Yükleme İşlemleri (Eğer dosya seçilmişse URL'yi ezer)
    if (isset($_FILES['trailer_file']) && $_FILES['trailer_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['trailer_file']['name'], PATHINFO_EXTENSION));
        $trailerName = 'trailer_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['trailer_file']['tmp_name'], $uploadDir . '/' . $trailerName);
        $trailerUrl = '/uploads/' . $trailerName;
    }
    
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
        $videoName = 'video_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadDir . '/' . $videoName);
        $videoUrl = '/uploads/' . $videoName;
    }
    
    $coverName = null;
    $bannerName = null;
    
    // Kapak fotoğrafı yükleme veya URL'den indirme
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $coverName = 'cover_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['cover']['tmp_name'], $uploadDir . '/' . $coverName);
    } elseif (!empty($_POST['tmdb_cover_url'])) {
        // TMDB'den gelen kapak görselini indir
        $url = trim($_POST['tmdb_cover_url']);
        $ext = 'jpg';
        $coverName = 'cover_' . time() . '_' . rand(100,999) . '.' . $ext;
        $imgData = @file_get_contents($url);
        if ($imgData !== false) {
            file_put_contents($uploadDir . '/' . $coverName, $imgData);
        } else {
            $coverName = null;
        }
    }
    
    // Banner fotoğrafı yükleme veya URL'den indirme
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));
        $bannerName = 'banner_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['banner']['tmp_name'], $uploadDir . '/' . $bannerName);
    } elseif (!empty($_POST['tmdb_banner_url'])) {
        // TMDB'den gelen banner görselini indir
        $url = trim($_POST['tmdb_banner_url']);
        $ext = 'jpg';
        $bannerName = 'banner_' . time() . '_' . rand(100,999) . '.' . $ext;
        $imgData = @file_get_contents($url);
        if ($imgData !== false) {
            file_put_contents($uploadDir . '/' . $bannerName, $imgData);
        } else {
            $bannerName = null;
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        if ($id > 0) {
            // Update (resimler boşsa eskisini tut)
            $sql = "UPDATE content SET title=?, type=?, description=?, release_year=?, age_rating=?, imdb_rating=?, duration_minutes=?, director=?, original_language=?, is_featured=?, is_coming_soon=?, is_exclusive=?, is_top10=? ";
            $params = [$title, $type, $description, $release_year, $age_rating, $imdb_rating, $duration_minutes, $director, $original_language, $is_featured, $is_coming_soon, $is_exclusive, $is_top10];
            
            if (isset($_POST['remove_cover']) && $_POST['remove_cover'] == '1') {
                $sql .= ", cover_image=NULL ";
            } elseif ($coverName) { 
                $sql .= ", cover_image=? "; 
                $params[] = $coverName; 
            }
            
            if (isset($_POST['remove_banner']) && $_POST['remove_banner'] == '1') {
                $sql .= ", banner_image=NULL ";
            } elseif ($bannerName) { 
                $sql .= ", banner_image=? "; 
                $params[] = $bannerName; 
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $msg = 'İçerik güncellendi.';
            
            // Linkleri silip tekrar ekleyelim (Basit yöntem)
            $pdo->prepare("DELETE FROM content_links WHERE content_id = ?")->execute([$id]);
            
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO content (title, type, description, release_year, age_rating, imdb_rating, duration_minutes, director, original_language, cover_image, banner_image, is_featured, is_coming_soon, is_exclusive, is_top10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $type, $description, $release_year, $age_rating, $imdb_rating, $duration_minutes, $director, $original_language, $coverName, $bannerName, $is_featured, $is_coming_soon, $is_exclusive, $is_top10]);
            $id = $pdo->lastInsertId();
            $msg = 'İçerik eklendi.';
        }
        
        // Linkleri Ekle
        if (!empty($trailerUrl)) {
            $pdo->prepare("INSERT INTO content_links (content_id, link_type, url) VALUES (?, 'trailer', ?)")->execute([$id, $trailerUrl]);
        }
        if ($type === 'movie' && !empty($videoUrl)) {
            $pdo->prepare("INSERT INTO content_links (content_id, link_type, url) VALUES (?, 'movie', ?)")->execute([$id, $videoUrl]);
        }
        
        // Kategorileri Ekle
        $pdo->prepare("DELETE FROM content_categories WHERE content_id = ?")->execute([$id]);
        if (isset($_POST['categories']) && is_array($_POST['categories'])) {
            $stmtCat = $pdo->prepare("INSERT IGNORE INTO content_categories (content_id, category_id) VALUES (?, ?)");
            $uniqueCategories = array_unique($_POST['categories']);
            foreach ($uniqueCategories as $catId) {
                $stmtCat->execute([$id, intval($catId)]);
            }
        }
        
        // Oyuncu Kadrosunu (TMDB'den gelen) Ekle
        if (!empty($_POST['tmdb_cast'])) {
            $castData = json_decode($_POST['tmdb_cast'], true);
            if (is_array($castData) && count($castData) > 0) {
                // Eski oyuncuları sil
                $pdo->prepare("DELETE FROM content_cast WHERE content_id = ?")->execute([$id]);
                $stmtCast = $pdo->prepare("INSERT INTO content_cast (content_id, name, role, character_name, photo, sort_order) VALUES (?, ?, 'actor', ?, ?, ?)");
                
                $order = 1;
                foreach ($castData as $c) {
                    $stmtCast->execute([$id, $c['name'], $c['character'], $c['photo'], $order]);
                    $order++;
                }
            }
        }
        
        $pdo->commit();
        
        if (isset($_GET['edit'])) {
            redirect('/admin/manage-content.php');
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = 'Hata: ' . $e->getMessage();
        $msgType = 'error';
    }
}

// İçerik Silme
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM content WHERE id = ?")->execute([$delId]);
    redirect('/admin/manage-content.php');
}

// İçerik Düzenleme Verilerini Çekme
$editItem = null;
$editCategories = [];
$editTrailer = '';
$editVideo = '';

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
    
    if ($editItem) {
        $stmtCat = $pdo->prepare("SELECT category_id FROM content_categories WHERE content_id = ?");
        $stmtCat->execute([$editId]);
        $editCategories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);
        
        $stmtLinks = $pdo->prepare("SELECT * FROM content_links WHERE content_id = ?");
        $stmtLinks->execute([$editId]);
        foreach($stmtLinks->fetchAll() as $lk) {
            if ($lk['link_type'] === 'trailer') $editTrailer = $lk['url'];
            if ($lk['link_type'] === 'movie') $editVideo = $lk['url'];
        }
    }
}

// Tüm içerikler
$contents = $pdo->query("SELECT * FROM content ORDER BY id DESC")->fetchAll();
$pageTitle = 'İçerik Yönetimi - Admin';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <script>
        function toggleMovieFields() {
            var type = document.getElementById('type_select').value;
            document.getElementById('movie_url_group').style.display = (type === 'movie') ? 'block' : 'none';
        }
        window.onload = function() {
            toggleMovieFields();
        };
    </script>
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
            <a href="manage-content.php" class="admin-nav-link active">
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

    <main class="admin-content">
        <!-- Mobil Toggle Butonu -->
        <button class="admin-mobile-toggle" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 style="margin-bottom: 24px;">İçerik Yönetimi</h1>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?= $msgType ?>"><?= e($msg) ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <!-- Form Bölümü -->
            <div class="stat-card" style="flex: 1; min-width: 300px; max-width: 500px; height: fit-content;">
                <h3 style="margin-bottom: 20px;">
                    <?= $editItem ? 'İçeriği Düzenle: ' . e($editItem['title']) : 'Yeni İçerik Ekle (Film / Dizi)' ?>
                </h3>
                
                <?php if ($editItem): ?>
                    <div style="margin-bottom: 15px;">
                        <a href="manage-content.php" class="btn btn--sm btn--ghost">< Yeni Ekleme Moduna Dön</a>
                    </div>
                <?php endif; ?>

                <form action="manage-content.php<?= $editItem ? '?edit='.$editItem['id'] : '' ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_content">
                    <input type="hidden" name="id" value="<?= $editItem ? $editItem['id'] : 0 ?>">
                    
                    <div class="form-group">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <label class="form-label" style="margin-bottom:0;">Başlık</label>
                            <button type="button" class="btn btn--sm" style="background:#032541; color:#fff;" onclick="fetchFromTMDB()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px; vertical-align:middle;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                TMDB'den Doldur
                            </button>
                        </div>
                        <input type="text" name="title" id="content_title" class="form-control" value="<?= $editItem ? e($editItem['title']) : '' ?>" required>
                        <span id="tmdb_status" style="display:none; font-size:0.85rem; color:#a78bfa; margin-top:5px;">Veriler çekiliyor...</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tür</label>
                        <select name="type" id="type_select" class="form-control" onchange="toggleMovieFields()">
                            <option value="movie" <?= ($editItem && $editItem['type'] === 'movie') ? 'selected' : '' ?>>Film</option>
                            <option value="series" <?= ($editItem && $editItem['type'] === 'series') ? 'selected' : '' ?>>Dizi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategoriler (Birden fazla seçilebilir)</label>
                        <div style="max-height: 120px; overflow-y: auto; background: var(--bg-tertiary); padding: 10px; border-radius: 8px; border: 1px solid var(--border-color);">
                            <?php foreach ($allCategories as $cat): ?>
                                <?php $isChecked = in_array($cat['id'], $editCategories) ? 'checked' : ''; ?>
                                <label style="display: block; margin-bottom: 5px; cursor: pointer; color: var(--text-main);">
                                    <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= $isChecked ?>> <?= e($cat['name']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" id="content_desc" class="form-control" rows="3"><?= $editItem ? e($editItem['description']) : '' ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Yıl</label>
                            <input type="number" name="release_year" id="content_year" class="form-control" value="<?= $editItem ? $editItem['release_year'] : date('Y') ?>">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Yaş Sınırı</label>
                            <input type="text" name="age_rating" class="form-control" value="<?= $editItem ? e($editItem['age_rating']) : '13+' ?>">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">IMDb / TMDB</label>
                            <input type="number" step="0.1" name="imdb_rating" id="content_imdb" class="form-control" value="<?= $editItem ? $editItem['imdb_rating'] : '0.0' ?>">
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Süre (Dk)</label>
                            <input type="number" name="duration_minutes" id="content_duration" class="form-control" value="<?= $editItem ? $editItem['duration_minutes'] : '0' ?>">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Yönetmen / Yapımcı</label>
                            <input type="text" name="director" id="content_director" class="form-control" value="<?= $editItem ? e($editItem['director'] ?? '') : '' ?>">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Orijinal Dil</label>
                            <input type="text" name="original_language" id="content_lang" class="form-control" value="<?= $editItem ? e($editItem['original_language'] ?? '') : 'tr' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kapak Görseli (Dikey - 2:3)</label>
                        <?php if ($editItem && $editItem['cover_image']): ?>
                            <div style="margin-bottom:8px; display:flex; align-items:flex-end; gap:15px;">
                                <img src="<?= BASE_URL ?>/uploads/<?= e($editItem['cover_image']) ?>" style="width:80px; height:120px; object-fit:cover; border-radius:8px; border:1px solid var(--border-color);">
                                <div style="display:flex; flex-direction:column; padding-bottom:5px;">
                                    <span style="font-size:0.8rem; color:var(--text-muted);">Mevcut görsel (değiştirmek için yeni yükleyin)</span>
                                    <label style="display:inline-flex; align-items:center; margin-top:8px; color:#ff4d4d; font-size:0.85rem; cursor:pointer;">
                                        <input type="checkbox" name="remove_cover" value="1" style="margin-right:6px; accent-color:#ff4d4d;"> Görseli Kaldır
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="drop-zone" id="drop-zone-cover">
                            <input type="file" name="cover" class="drop-zone__input" accept="image/*">
                            <div class="drop-zone__content">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span class="drop-zone__text">Görseli buraya sürükleyin veya tıklayarak seçin</span>
                                <span class="drop-zone__hint">PNG, JPG, WEBP (Maks: 10MB)</span>
                            </div>
                            <div class="drop-zone__preview" style="display:none;">
                                <img class="drop-zone__preview-img" src="" alt="Önizleme">
                                <span class="drop-zone__filename"></span>
                                <button type="button" class="drop-zone__remove" title="Kaldır">&times;</button>
                            </div>
                        </div>
                        <!-- TMDB Cover URL Hidden Input -->
                        <input type="hidden" name="tmdb_cover_url" id="tmdb_cover_url" value="">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Banner Görseli (Yatay - 16:9)</label>
                        <?php if ($editItem && $editItem['banner_image']): ?>
                            <div style="margin-bottom:8px; display:flex; align-items:flex-end; gap:15px;">
                                <img src="<?= BASE_URL ?>/uploads/<?= e($editItem['banner_image']) ?>" style="width:200px; height:112px; object-fit:cover; border-radius:8px; border:1px solid var(--border-color);">
                                <div style="display:flex; flex-direction:column; padding-bottom:5px;">
                                    <span style="font-size:0.8rem; color:var(--text-muted);">Mevcut görsel (değiştirmek için yeni yükleyin)</span>
                                    <label style="display:inline-flex; align-items:center; margin-top:8px; color:#ff4d4d; font-size:0.85rem; cursor:pointer;">
                                        <input type="checkbox" name="remove_banner" value="1" style="margin-right:6px; accent-color:#ff4d4d;"> Görseli Kaldır
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="drop-zone" id="drop-zone-banner">
                            <input type="file" name="banner" class="drop-zone__input" accept="image/*">
                            <div class="drop-zone__content">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span class="drop-zone__text">Görseli buraya sürükleyin veya tıklayarak seçin</span>
                                <span class="drop-zone__hint">PNG, JPG, WEBP (Maks: 10MB)</span>
                            </div>
                            <div class="drop-zone__preview" style="display:none;">
                                <img class="drop-zone__preview-img" src="" alt="Önizleme">
                                <span class="drop-zone__filename"></span>
                                <button type="button" class="drop-zone__remove" title="Kaldır">&times;</button>
                            </div>
                        </div>
                        <!-- TMDB Banner URL Hidden Input -->
                        <input type="hidden" name="tmdb_banner_url" id="tmdb_banner_url" value="">
                        <!-- TMDB Cast JSON Hidden Input -->
                        <input type="hidden" name="tmdb_cast" id="tmdb_cast" value="">
                    </div>
                    
                    <hr style="border-color: rgba(255,255,255,0.1); margin:20px 0;">
                    
                    <div class="form-group" style="background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <label class="form-label" style="margin-bottom:15px; font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Fragman Kaynağı</label>
                        <div style="display:flex; flex-direction:column; gap:15px;">
                            <div>
                                <span style="font-size:0.85rem; color:var(--text-muted); display:block; margin-bottom:5px;">Seçenek 1: YouTube Linki Girin</span>
                                <input type="text" name="trailer_url" class="form-control" value="<?= e($editTrailer) ?>" placeholder="Örn: https://youtube.com/watch?v=...">
                            </div>
                            <div style="text-align:center; font-size:0.85rem; color:var(--text-muted); position:relative;">
                                <hr style="border-color:rgba(255,255,255,0.05); margin:0;">
                                <span style="background:var(--bg-secondary); padding:0 10px; position:absolute; top:-9px; left:50%; transform:translateX(-50%);">VEYA</span>
                            </div>
                            <div>
                                <span style="font-size:0.85rem; color:var(--text-muted); display:block; margin-bottom:5px;">Seçenek 2: Sunucuya MP4 Yükleyin</span>
                                <input type="file" name="trailer_file" class="form-control" accept="video/mp4,video/webm" style="padding:10px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="movie_url_group" style="background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); margin-top:20px;">
                        <label class="form-label" style="margin-bottom:15px; font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Film Video Kaynağı</label>
                        <div style="display:flex; flex-direction:column; gap:15px;">
                            <div>
                                <span style="font-size:0.85rem; color:var(--text-muted); display:block; margin-bottom:5px;">Seçenek 1: Harici Link (YouTube, M3U8 vb.)</span>
                                <input type="text" name="video_url" class="form-control" value="<?= e($editVideo) ?>" placeholder="Sadece film için video kaynağı">
                            </div>
                            <div style="text-align:center; font-size:0.85rem; color:var(--text-muted); position:relative;">
                                <hr style="border-color:rgba(255,255,255,0.05); margin:0;">
                                <span style="background:var(--bg-secondary); padding:0 10px; position:absolute; top:-9px; left:50%; transform:translateX(-50%);">VEYA</span>
                            </div>
                            <div>
                                <span style="font-size:0.85rem; color:var(--text-muted); display:block; margin-bottom:5px;">Seçenek 2: Sunucuya MP4 Yükleyin</span>
                                <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm" style="padding:10px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="background: rgba(147,51,234,0.08); padding:20px; border-radius:12px; margin-top:20px; border: 1px solid rgba(147,51,234,0.2);">
                        <label class="form-label" style="font-size: 1rem; font-weight: 600; margin-bottom: 16px; display: block; color: #a78bfa;">
                            📋 Anasayfa Listeleri (Bu içerik hangi listelerde gösterilsin?)
                        </label>
                        
                        <!-- Özel Bayraklar (Veritabanı flag'leri) -->
                        <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">⭐ Öne Çıkan Alanlar</div>
                            <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer;">
                                <input type="checkbox" name="is_featured" id="is_featured" <?= ($editItem && $editItem['is_featured'] == 1) ? 'checked' : '' ?>> 
                                Hero (Ana) Banner'da Göster
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer;">
                                <input type="checkbox" name="is_coming_soon" id="is_coming_soon" <?= ($editItem && $editItem['is_coming_soon'] == 1) ? 'checked' : '' ?>> 
                                Yakında Gelecekler (Coming Soon) Listesinde Göster
                            </label>
                        </div>

                        <!-- Kategori Bazlı Anasayfa Listeleri -->
                        <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">🎬 Anasayfa Kategorileri</div>
                            <?php 
                            // Anasayfada gösterilen kategori listelerinin slug'ları
                            $homepageCategorySlugs = [
                                'amerikan-dizileri' => 'Amerikan Dizileri',
                                'amerikan-filmleri' => 'Amerikan Filmleri',
                                'senin-icin-sectiklerimiz' => 'Senin İçin Seçtiklerimiz',
                                'elestirmenlerden-tam-not-alanlar' => 'Eleştirmenlerden Tam Not Alanlar',
                                'sadece-movifyda' => "Sadece Movify'da",
                                'odullu-yapimlar' => 'Ödüllü Yapımlar',
                                'belgeseller' => 'Belgeseller',
                                'animeler' => 'Animeler',
                                'gise-rekortmenleri' => 'Gişe Rekortmenleri',
                                'holywood-yapimlari' => 'Holywood Yapımları',
                            ];
                            
                            // Her slug için veritabanından category id'sini bul
                            foreach ($homepageCategorySlugs as $slug => $label):
                                $catStmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
                                $catStmt->execute([$slug]);
                                $catRow = $catStmt->fetch();
                                if ($catRow):
                                    $catId = $catRow['id'];
                                    $isChecked = in_array($catId, $editCategories) ? 'checked' : '';
                            ?>
                                <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer;">
                                    <input type="checkbox" name="categories[]" value="<?= $catId ?>" <?= $isChecked ?>> 
                                    <?= e($label) ?> Listesinde Göster
                                </label>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>

                        <!-- Otomatik Listeler (Bilgi) -->
                        <div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 8px;">📊 Otomatik Listeler (Sistem tarafından doldurulur)</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.6;">
                                • <strong>Yeni İçerikler</strong> — Otomatik (en son eklenen içerikler)<br>
                                • <strong>Çok İzlenenler</strong> — Otomatik (en çok izlenme sayısına göre)<br>
                                • <strong>Movify'da Bugün Top 10 Film</strong> — Otomatik (en çok izlenen filmler)<br>
                                • <strong>Movify'da Bugün Top 10 Dizi</strong> — Otomatik (en çok izlenen diziler)
                            </div>
                        </div>
                    </div>

                    
                    <button type="submit" class="btn btn--primary" style="width:100%;">
                        <?= $editItem ? 'Değişiklikleri Kaydet' : 'Kaydet' ?>
                    </button>
                </form>
            </div>
            
            <!-- Liste Bölümü -->
            <div class="stat-card" style="flex: 2; min-width: 300px; padding:0; overflow:hidden; height: fit-content;">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kapak</th>
                                <th>Başlık</th>
                                <th>Tür</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contents as $row): ?>
                            <tr style="<?= ($editItem && $editItem['id'] == $row['id']) ? 'background: rgba(236,72,153,0.1);' : '' ?>">
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <?php if ($row['cover_image']): ?>
                                        <img src="<?= BASE_URL ?>/uploads/<?= e($row['cover_image']) ?>" style="width:40px; height:60px; object-fit:cover; border-radius:4px;">
                                    <?php else: ?>
                                        <div style="width:40px; height:60px; background:#333; border-radius:4px;"></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= e($row['title']) ?>
                                    <?php if ($row['is_exclusive']) echo '<span style="font-size:0.7rem; color:pink; margin-left:5px;">(Originals)</span>'; ?>
                                    <?php if ($row['is_top10']) echo '<span style="font-size:0.7rem; color:gold; margin-left:5px;">(Top 10)</span>'; ?>
                                </td>
                                <td><?= $row['type'] === 'movie' ? 'Film' : 'Dizi' ?></td>
                                <td>
                                    <a href="manage-content.php?edit=<?= $row['id'] ?>" class="btn btn--sm" style="background: var(--bg-tertiary); color:white; margin-right:5px;">Düzenle</a>
                                    <a href="manage-content.php?delete=<?= $row['id'] ?>" class="btn btn--sm btn--ghost" style="color:#f87171;" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
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

<style>
/* Drag & Drop Upload Zone */
.drop-zone {
    position: relative;
    border: 2px dashed rgba(147, 51, 234, 0.35);
    border-radius: 12px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(147, 51, 234, 0.04);
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.drop-zone:hover {
    border-color: rgba(147, 51, 234, 0.6);
    background: rgba(147, 51, 234, 0.08);
}
.drop-zone.drag-over {
    border-color: #a78bfa;
    background: rgba(147, 51, 234, 0.15);
    transform: scale(1.01);
    box-shadow: 0 0 20px rgba(147, 51, 234, 0.2);
}
.drop-zone__input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.drop-zone__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    pointer-events: none;
}
.drop-zone__text {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 500;
}
.drop-zone__hint {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.3);
}
.drop-zone__preview {
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    pointer-events: none;
}
.drop-zone__preview-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid rgba(147, 51, 234, 0.3);
}
.drop-zone__filename {
    font-size: 0.85rem;
    color: var(--text-main);
    font-weight: 500;
    flex: 1;
    text-align: left;
    word-break: break-all;
}
.drop-zone__remove {
    pointer-events: auto;
    z-index: 3;
    position: relative;
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #f87171;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.drop-zone__remove:hover {
    background: rgba(239, 68, 68, 0.4);
    transform: scale(1.1);
}
</style>

<script>
document.querySelectorAll('.drop-zone').forEach(zone => {
    const input = zone.querySelector('.drop-zone__input');
    const content = zone.querySelector('.drop-zone__content');
    const preview = zone.querySelector('.drop-zone__preview');
    const previewImg = zone.querySelector('.drop-zone__preview-img');
    const filename = zone.querySelector('.drop-zone__filename');
    const removeBtn = zone.querySelector('.drop-zone__remove');

    // Tıklama ile dosya seçme (input zaten üstte olduğu için otomatik çalışır)
    input.addEventListener('change', () => {
        if (input.files.length) {
            showPreview(input.files[0]);
        }
    });

    // Drag events
    ['dragenter', 'dragover'].forEach(evt => {
        zone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(evt => {
        zone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('drag-over');
        });
    });

    zone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files.length && files[0].type.startsWith('image/')) {
            // DataTransfer ile input'a dosya atama
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            showPreview(files[0]);
        }
    });

    // Kaldır butonu
    removeBtn.addEventListener('click', e => {
        e.preventDefault();
        e.stopPropagation();
        input.value = '';
        preview.style.display = 'none';
        content.style.display = 'flex';
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            filename.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            content.style.display = 'none';
            preview.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
});

async function fetchFromTMDB() {
    const title = document.getElementById('content_title').value.trim();
    if(!title) {
        alert("Lütfen önce bir başlık yazın.");
        return;
    }
    
    const type = document.getElementById('type_select').value; // movie or series
    const tmdbType = type === 'series' ? 'tv' : 'movie';
    const statusText = document.getElementById('tmdb_status');
    statusText.style.display = 'block';
    statusText.textContent = 'Aranıyor...';
    
    // TMDB API Public Test Key
    const apiKey = '2dca580c2a14b55200e784d157207b4d';
    
    try {
        let itemId;
        let actualTmdbType = tmdbType; // formdan seçilen (movie veya series -> tv)
        
        // Eğer başlık sadece sayılardan oluşuyorsa, direkt o ID'yi hedef al
        if (/^\d+$/.test(title)) {
            itemId = title;
        } else {
            // İsim ise Multi Search (Film ve Dizi Karışık) yap
            const searchRes = await fetch(`https://api.themoviedb.org/3/search/multi?api_key=${apiKey}&language=tr-TR&query=${encodeURIComponent(title)}`);
            const searchData = await searchRes.json();
            
            // Sadece movie ve tv (dizi) sonuçlarını filtrele (kişileri vs alma)
            const validResults = (searchData.results || []).filter(item => item.media_type === 'movie' || item.media_type === 'tv');
            
            if(validResults.length === 0) {
                statusText.textContent = 'Sonuç bulunamadı.';
                statusText.style.color = 'red';
                return;
            }
            
            // En iyi eşleşmeyi al
            const item = validResults[0];
            itemId = item.id;
            actualTmdbType = item.media_type;
        }
        
        // Formdaki select box'ı otomatik olarak güncelle
        const typeSelect = document.getElementById('type_select');
        if (actualTmdbType === 'tv') {
            typeSelect.value = 'series';
        } else {
            typeSelect.value = 'movie';
        }
        if (typeof toggleMovieFields === 'function') toggleMovieFields();
        
        statusText.textContent = 'Detaylar çekiliyor...';
        
        // Fetch full details
        const detailRes = await fetch(`https://api.themoviedb.org/3/${actualTmdbType}/${itemId}?api_key=${apiKey}&language=tr-TR`);
        const detailData = await detailRes.json();
        
        // Fetch credits (cast & crew)
        const creditsRes = await fetch(`https://api.themoviedb.org/3/${actualTmdbType}/${itemId}/credits?api_key=${apiKey}&language=tr-TR`);
        const creditsData = await creditsRes.json();
        
        // Map data to inputs
        // Eğer başlığa ID girilmişse asıl ismini (title veya name) input'a geri bas
        if (/^\d+$/.test(title)) {
            if(detailData.title) document.getElementById('content_title').value = detailData.title;
            else if(detailData.name) document.getElementById('content_title').value = detailData.name;
        }
        
        if(detailData.overview) document.getElementById('content_desc').value = detailData.overview;
        
        if(actualTmdbType === 'movie') {
            if(detailData.release_date) document.getElementById('content_year').value = detailData.release_date.substring(0,4);
            if(detailData.runtime) document.getElementById('content_duration').value = detailData.runtime;
        } else {
            if(detailData.first_air_date) document.getElementById('content_year').value = detailData.first_air_date.substring(0,4);
            if(detailData.episode_run_time && detailData.episode_run_time.length > 0) document.getElementById('content_duration').value = detailData.episode_run_time[0];
        }
        
        if(detailData.vote_average) document.getElementById('content_imdb').value = parseFloat(detailData.vote_average).toFixed(1);
        if(detailData.original_language) document.getElementById('content_lang').value = detailData.original_language;
        
        // Find Director or Creator
        let director = '';
        if(actualTmdbType === 'movie') {
            const dirObj = creditsData.crew.find(c => c.job === 'Director');
            if(dirObj) director = dirObj.name;
        } else {
            if(detailData.created_by && detailData.created_by.length > 0) {
                director = detailData.created_by.map(c => c.name).join(', ');
            }
        }
        if(director) document.getElementById('content_director').value = director;
        
        // Extract Cast (Ilk 12 kisi)
        let castArray = [];
        if(creditsData.cast && creditsData.cast.length > 0) {
            let maxCast = Math.min(12, creditsData.cast.length);
            for(let i=0; i<maxCast; i++) {
                let c = creditsData.cast[i];
                castArray.push({
                    name: c.name,
                    character: c.character || '',
                    photo: c.profile_path ? `https://image.tmdb.org/t/p/w185${c.profile_path}` : ''
                });
            }
        }
        document.getElementById('tmdb_cast').value = JSON.stringify(castArray);
        
        // Set Images
        if(detailData.poster_path) {
            const posterUrl = `https://image.tmdb.org/t/p/w500${detailData.poster_path}`;
            document.getElementById('tmdb_cover_url').value = posterUrl;
            
            // Show preview
            const dropZoneCover = document.getElementById('drop-zone-cover');
            const previewImgCover = dropZoneCover.querySelector('.drop-zone__preview-img');
            const contentCover = dropZoneCover.querySelector('.drop-zone__content');
            const previewCover = dropZoneCover.querySelector('.drop-zone__preview');
            
            previewImgCover.src = posterUrl;
            dropZoneCover.querySelector('.drop-zone__filename').textContent = "TMDB'den Kapak Çekildi";
            contentCover.style.display = 'none';
            previewCover.style.display = 'flex';
        }
        
        if(detailData.backdrop_path) {
            const backdropUrl = `https://image.tmdb.org/t/p/w1280${detailData.backdrop_path}`;
            document.getElementById('tmdb_banner_url').value = backdropUrl;
            
            // Show preview
            const dropZoneBanner = document.getElementById('drop-zone-banner');
            const previewImgBanner = dropZoneBanner.querySelector('.drop-zone__preview-img');
            const contentBanner = dropZoneBanner.querySelector('.drop-zone__content');
            const previewBanner = dropZoneBanner.querySelector('.drop-zone__preview');
            
            previewImgBanner.src = backdropUrl;
            dropZoneBanner.querySelector('.drop-zone__filename').textContent = "TMDB'den Banner Çekildi";
            contentBanner.style.display = 'none';
            previewBanner.style.display = 'flex';
        }
        
        statusText.textContent = 'Veriler başarıyla dolduruldu! (Lütfen kaydedin)';
        statusText.style.color = '#10b981'; // green
        
    } catch(err) {
        console.error(err);
        statusText.textContent = 'Veri çekilirken hata oluştu.';
        statusText.style.color = 'red';
    }
}
</script>

</body>
</html>
