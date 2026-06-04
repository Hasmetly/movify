<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth-check.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$pdo = db();
$profileId = $_SESSION['profile_id'] ?? 0;
$contentId = intval($_GET['id'] ?? 0);

if ($contentId <= 0) {
    redirect('/home.php');
}

// İçerik bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
$stmt->execute([$contentId]);
$content = $stmt->fetch();

if (!$content) {
    redirect('/home.php');
}

// Kategorileri çek
$stmtCat = $pdo->prepare("SELECT c.name FROM categories c JOIN content_categories cc ON c.id = cc.category_id WHERE cc.content_id = ?");
$stmtCat->execute([$contentId]);
$categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

// Oyuncuları çek
$stmtCast = $pdo->prepare("SELECT * FROM content_cast WHERE content_id = ? ORDER BY sort_order ASC");
$stmtCast->execute([$contentId]);
$casts = $stmtCast->fetchAll();

// Diziyse Bölümleri çek
$episodes = [];
$seasons = [];
if ($content['type'] === 'series') {
    $stmtEps = $pdo->prepare("SELECT * FROM episodes WHERE content_id = ? ORDER BY season_number ASC, episode_number ASC");
    $stmtEps->execute([$contentId]);
    $episodes = $stmtEps->fetchAll();
    
    foreach ($episodes as $ep) {
        $seasons[$ep['season_number']][] = $ep;
    }
}

// Watchlist durumu
$stmtWl = $pdo->prepare("SELECT id FROM watchlist WHERE profile_id = ? AND content_id = ?");
$stmtWl->execute([$profileId, $contentId]);
$inWatchlist = $stmtWl->fetch() ? true : false;

// Film video url
$movieUrl = '';
if ($content['type'] === 'movie') {
    $stmtUrl = $pdo->prepare("SELECT url FROM content_links WHERE content_id = ? AND link_type = 'movie' LIMIT 1");
    $stmtUrl->execute([$contentId]);
    $movieUrl = $stmtUrl->fetchColumn();
}

// Fragman (Trailer) URL
$stmtTrailer = $pdo->prepare("SELECT url FROM content_links WHERE content_id = ? AND link_type = 'trailer' LIMIT 1");
$stmtTrailer->execute([$contentId]);
$trailerUrl = $stmtTrailer->fetchColumn();

$trailerEmbedUrl = '';
$isYoutube = false;
$videoId = '';
if ($trailerUrl) {
    if (strpos($trailerUrl, 'youtube.com/watch?v=') !== false) {
        $videoId = explode('v=', $trailerUrl)[1];
        $videoId = explode('&', $videoId)[0];
        $isYoutube = true;
    } elseif (strpos($trailerUrl, 'youtu.be/') !== false) {
        $videoId = explode('youtu.be/', $trailerUrl)[1];
        $videoId = explode('?', $videoId)[0];
        $isYoutube = true;
    } else {
        $trailerEmbedUrl = $trailerUrl; // Direkt mp4 vb url'ler için
    }
}

// Beğen durumu
$stmtLike = $pdo->prepare("SELECT is_like FROM likes WHERE profile_id = ? AND content_id = ?");
$stmtLike->execute([$profileId, $contentId]);
$likeData = $stmtLike->fetch();
$isLiked = $likeData && $likeData['is_like'] == 1;

$pageTitle = e($content['title']) . ' - Movify';
$bannerImage = $content['banner_image'] ? BASE_URL . '/uploads/' . e($content['banner_image']) : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        .detail-hero {
            position: relative;
            width: 100%;
            min-height: 85vh;
            background-color: var(--bg-color);
            background-size: cover;
            background-position: top center;
            display: flex;
            align-items: flex-end;
            padding-bottom: 50px;
            overflow: hidden;
        }
        .hero-video-wrapper {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            z-index: 1;
            pointer-events: none; /* Üzerine tıklanmasın, arkaplan gibi dursun */
            overflow: hidden;
        }
        .hero-video-wrapper iframe {
            width: 100vw;
            height: 56.25vw; /* 16:9 oranı */
            min-height: 100vh;
            min-width: 177.77vh; /* 16:9 oranı */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
        .detail-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, var(--bg-color) 0%, rgba(10,10,10,0.6) 50%, rgba(10,10,10,0.3) 100%);
            z-index: 2;
        }
        
        .hero-sound-btn {
            position: absolute;
            right: 4%;
            bottom: 40px;
            z-index: 10;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.5);
            background: rgba(0,0,0,0.3);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .hero-sound-btn:hover {
            border-color: #fff;
            background: rgba(0,0,0,0.6);
        }

        .detail-container {
            position: relative;
            z-index: 3;
            padding: 0 4%;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .detail-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            background: linear-gradient(90deg, #ec4899 0%, #8b5cf6 40%, #6d28d9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .detail-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 1.1rem;
            color: #ddd;
            margin-bottom: 25px;
        }
        .detail-rating {
            color: #f5c518;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .detail-age {
            background-color: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .detail-desc {
            font-size: 1.15rem;
            line-height: 1.6;
            color: #ccc;
            max-width: 700px;
            margin-bottom: 30px;
        }
        .detail-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn-play-large {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #fff;
            color: #000;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-play-large:hover { opacity: 0.8; }
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(40,40,40,0.8);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-icon:hover {
            border-color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        .btn-icon.active {
            border-color: var(--accent-pink);
            color: var(--accent-pink);
        }
        .cast-section { margin-top: 50px; }
        .cast-list {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
        }
        .cast-card {
            text-align: center;
            min-width: 120px;
        }
        .cast-img {
            width: 100px; height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            background-color: #333;
        }
        .episodes-section { margin-top: 50px; }
        .ep-card {
            display: flex;
            background: rgba(40,40,40,0.5);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            transition: background 0.3s;
            cursor: pointer;
        }
        .ep-card:hover { background: rgba(60,60,60,0.8); }
        .ep-thumb {
            width: 250px;
            min-height: 140px;
            background-color: #222;
            object-fit: cover;
        }
        .ep-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .ep-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; }
        .ep-desc { font-size: 0.95rem; color: var(--text-muted); }
        
        .season-tabs { display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;}
        .season-tab { font-size: 1.2rem; font-weight: 600; color: var(--text-muted); cursor: pointer; }
        .season-tab.active { color: #fff; border-bottom: 2px solid var(--accent-pink); }

        /* ===== CONTENT DETAIL RESPONSIVE ===== */

        /* --- Tablet & Large Phone (≤768px) --- */
        @media (max-width: 768px) {
            .detail-hero {
                min-height: auto;
                padding-top: 100px;
            }
            .detail-container {
                padding: 0 5% !important;
            }
            .detail-title {
                font-size: clamp(1.8rem, 6vw, 3rem);
            }
            .detail-meta {
                font-size: 0.95rem;
                gap: 10px;
            }
            .detail-desc {
                font-size: 0.95rem;
                max-width: 100%;
            }
            .detail-actions {
                gap: 10px;
            }
            .btn-play-large {
                padding: 10px 20px;
                font-size: 1rem;
            }
            .btn-play-large svg {
                width: 22px;
                height: 22px;
            }
            .btn-icon {
                width: 42px;
                height: 42px;
            }
            .btn-icon svg {
                width: 20px;
                height: 20px;
            }

            /* Hero sound button */
            .hero-sound-btn {
                bottom: 20px;
                right: 3%;
            }

            /* Season tabs horizontal scroll */
            .season-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                flex-wrap: nowrap;
                gap: 12px;
                padding-bottom: 12px;
                scrollbar-width: none;
            }
            .season-tabs::-webkit-scrollbar {
                display: none;
            }
            .season-tab {
                flex-shrink: 0;
                font-size: 1rem;
            }

            /* Episode cards: stack vertically */
            .ep-card {
                flex-direction: column !important;
                padding: 12px !important;
                gap: 10px !important;
                align-items: flex-start !important;
            }
            /* Episode number */
            .ep-card > div[style*="font-size: 3.5rem"] {
                font-size: 2rem !important;
                width: 40px !important;
            }
            /* Episode thumbnails */
            .ep-card img[style*="width: 140px"] {
                width: 100% !important;
                height: auto !important;
                aspect-ratio: 16/9;
            }

            /* Detail info columns: stack vertically */
            #tab-details > div[style*="display: flex"] > div[style*="flex: 1"],
            #tab-details > div[style*="display: flex"] > div[style*="flex: 2"] {
                min-width: auto !important;
                flex: 1 1 100% !important;
            }

            /* Tabs container */
            .container > div[style*="display: flex; gap: 40px"] {
                gap: 20px !important;
            }

            /* Main container bottom padding */
            .container[style*="margin-top: 40px"] {
                padding-left: 4% !important;
                padding-right: 4% !important;
            }

            /* Cast grid */
            .cast-list[style*="grid-template-columns"] {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
                gap: 10px !important;
            }
        }

        /* --- Small Phone (≤480px) --- */
        @media (max-width: 480px) {
            .detail-hero {
                min-height: auto;
                padding-top: 90px;
            }
            .detail-container {
                padding: 0 4% !important;
            }
            .detail-title {
                font-size: clamp(1.5rem, 7vw, 2.5rem);
                letter-spacing: 1px;
            }
            .detail-meta {
                font-size: 0.85rem;
                gap: 8px;
                margin-bottom: 15px;
            }
            .detail-desc {
                font-size: 0.9rem;
                margin-bottom: 20px;
            }
            .detail-actions {
                gap: 8px;
            }
            .btn-play-large {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
            .btn-play-large svg {
                width: 18px;
                height: 18px;
            }
            .btn-icon {
                width: 38px;
                height: 38px;
            }

            /* Episode cards: even more compact */
            .ep-card {
                padding: 10px !important;
                gap: 8px !important;
                border-radius: 8px !important;
            }
            .ep-card > div[style*="font-size: 3.5rem"] {
                font-size: 1.5rem !important;
                width: 30px !important;
            }

            /* Episode info text */
            .ep-card div[style*="font-size: 1.15rem"] {
                font-size: 1rem !important;
            }
            .ep-card div[style*="font-size: 0.9rem"] {
                font-size: 0.8rem !important;
            }

            /* Play icon in episode card */
            .ep-card > div[style*="width: 40px; height: 40px"] {
                width: 34px !important;
                height: 34px !important;
            }

            /* Tab buttons */
            .detail-tab {
                font-size: 1rem !important;
            }

            /* Cast grid */
            .cast-list[style*="grid-template-columns"] {
                grid-template-columns: repeat(auto-fill, minmax(85px, 1fr)) !important;
            }

            /* Section spacing */
            .episodes-section,
            .cast-section {
                margin-top: 30px;
            }
        }

        /* Prevent horizontal overflow */
        body {
            overflow-x: hidden;
        }
        main {
            overflow-x: hidden;
        }
    </style>
</head>
<body data-theme="dark">

<?php include __DIR__ . '/includes/header.php'; ?>

<main>
    <div class="detail-hero" style="<?= (!$isYoutube && !$trailerEmbedUrl && $bannerImage) ? "background-image: url('$bannerImage');" : '' ?>">
        <?php if ($isYoutube && $videoId): ?>
            <div class="hero-video-wrapper">
                <div id="hero-yt-player"></div>
            </div>
            
            <button class="hero-sound-btn" id="hero-sound-btn" onclick="toggleHeroSound()" title="Sesi Aç/Kapat">
                <svg id="icon-muted" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>
                <svg id="icon-unmuted" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            
            <script src="https://www.youtube.com/iframe_api"></script>
            <script>
            var heroPlayer;
            var heroIsMuted = true;
            
            function onYouTubeIframeAPIReady() {
                heroPlayer = new YT.Player('hero-yt-player', {
                    videoId: '<?= $videoId ?>',
                    playerVars: {
                        'autoplay': 1, 'controls': 0, 'mute': 1, 'loop': 1,
                        'playlist': '<?= $videoId ?>', 'showinfo': 0, 'rel': 0, 'modestbranding': 1, 'fs': 0, 'playsinline': 1
                    },
                    events: {
                        'onReady': function(event) {
                            event.target.playVideo();
                        }
                    }
                });
            }
            
            function toggleHeroSound() {
                if (heroPlayer && typeof heroPlayer.unMute === 'function') {
                    if (heroIsMuted) {
                        heroPlayer.unMute();
                        heroPlayer.setVolume(100);
                        document.getElementById('icon-muted').style.display = 'none';
                        document.getElementById('icon-unmuted').style.display = 'block';
                        heroIsMuted = false;
                    } else {
                        heroPlayer.mute();
                        document.getElementById('icon-unmuted').style.display = 'none';
                        document.getElementById('icon-muted').style.display = 'block';
                        heroIsMuted = true;
                    }
                }
            }
            </script>
        <?php elseif ($trailerEmbedUrl): ?>
            <div class="hero-video-wrapper">
                <!-- YouTube Dışı Mp4 vb. video için -->
                <video src="<?= e($trailerEmbedUrl) ?>" id="hero-mp4-player" autoplay loop muted style="width: 100vw; height: 56.25vw; min-height: 100vh; min-width: 177.77vh; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); object-fit: cover;"></video>
            </div>
            
            <button class="hero-sound-btn" id="hero-sound-btn" onclick="toggleMp4Sound()" title="Sesi Aç/Kapat">
                <svg id="icon-muted-mp4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>
                <svg id="icon-unmuted-mp4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
            </button>
            <script>
            function toggleMp4Sound() {
                var vid = document.getElementById('hero-mp4-player');
                if(vid.muted) {
                    vid.muted = false;
                    document.getElementById('icon-muted-mp4').style.display = 'none';
                    document.getElementById('icon-unmuted-mp4').style.display = 'block';
                } else {
                    vid.muted = true;
                    document.getElementById('icon-unmuted-mp4').style.display = 'none';
                    document.getElementById('icon-muted-mp4').style.display = 'block';
                }
            }
            </script>
        <?php endif; ?>
        <div class="detail-container">
            <h1 class="detail-title"><?= e($content['title']) ?></h1>
            
            <div class="detail-meta">
                <span class="detail-rating">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?= $content['imdb_rating'] ?: 'N/A' ?>
                </span>
                <span><?= $content['release_year'] ?></span>
                <span class="detail-age"><?= e($content['age_rating'] ?: 'Genel') ?></span>
                <span><?= $content['duration_minutes'] ? $content['duration_minutes'] . ' Dk' : '' ?></span>
                <?php if (!empty($categories)): ?>
                    <span style="color:#aaa;">&bull; <?= e(implode(', ', $categories)) ?></span>
                <?php endif; ?>
            </div>
            
            <p class="detail-desc"><?= e($content['description']) ?></p>
            
            <div class="detail-actions">
                <?php if ($content['type'] === 'movie'): ?>
                <button class="btn-play-large" onclick="window.location.href='<?= BASE_URL ?>/play.php?id=<?= $contentId ?>'">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    İZLE
                </button>
                <?php else: ?>
                <button class="btn-play-large" onclick="document.querySelector('.episodes-section').scrollIntoView({behavior: 'smooth'})">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    BÖLÜMLER
                </button>
                <?php endif; ?>
                
                <button id="btn-watchlist" class="btn-icon <?= $inWatchlist ? 'active' : '' ?>" title="Listeme Ekle" onclick="toggleAction('toggle_watchlist', <?= $contentId ?>)">
                    <?php if($inWatchlist): ?>
                        <svg id="icon-watchlist" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php else: ?>
                        <svg id="icon-watchlist" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <?php endif; ?>
                </button>
                
                <button id="btn-like" class="btn-icon <?= $isLiked ? 'active' : '' ?>" title="Beğen" onclick="toggleAction('toggle_like', <?= $contentId ?>)">
                    <?php if($isLiked): ?>
                        <svg id="icon-like" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    <?php else: ?>
                        <svg id="icon-like" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    <?php endif; ?>
                </button>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: 40px; padding-bottom: 80px; max-width:1200px;">
        
        <!-- Sekmeler veya Alt Alta Bölümler (Bölümler ve Ayrıntılar) -->
        <div style="display: flex; gap: 40px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 30px;">
            <?php if ($content['type'] === 'series' && !empty($seasons)): ?>
            <div class="detail-tab active" onclick="switchDetailTab('episodes', this)" style="font-size:1.2rem; font-weight:600; padding-bottom:15px; cursor:pointer; border-bottom: 3px solid var(--accent-purple);">Bölümler</div>
            <?php endif; ?>
            <div class="detail-tab <?= ($content['type'] === 'movie' || empty($seasons)) ? 'active' : '' ?>" onclick="switchDetailTab('details', this)" style="font-size:1.2rem; font-weight:600; padding-bottom:15px; cursor:pointer; <?= ($content['type'] === 'movie' || empty($seasons)) ? 'border-bottom: 3px solid var(--accent-purple);' : 'color:#aaa;' ?>">Ayrıntılar</div>
        </div>

        <!-- AYRINTILAR SEKMESİ -->
        <div id="tab-details" style="<?= ($content['type'] === 'movie' || empty($seasons)) ? 'display:block;' : 'display:none;' ?>">
            <div style="display: flex; flex-wrap: wrap; gap: 40px;">
                <!-- Sol Kolon: Künye -->
                <div style="flex: 1; min-width: 300px; background: rgba(255,255,255,0.03); padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                    <h3 style="font-size:1.5rem; margin-bottom:20px; color:var(--text-main); font-weight:700;">Künye & İstatistikler</h3>
                    
                    <div style="display:flex; flex-direction:column; gap:15px;">
                        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:10px;">
                            <span style="color:var(--text-muted);">Yönetmen / Yapımcı</span>
                            <span style="font-weight:600; text-align:right;"><?= e($content['director'] ?? 'Belirtilmedi') ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:10px;">
                            <span style="color:var(--text-muted);">Orijinal Dil</span>
                            <span style="font-weight:600; text-transform:uppercase;"><?= e($content['original_language'] ?? 'tr') ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:10px;">
                            <span style="color:var(--text-muted);">Yaş Sınırı</span>
                            <span style="font-weight:600; background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:4px;"><?= e($content['age_rating'] ?: 'Genel') ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:10px;">
                            <span style="color:var(--text-muted);">Süre</span>
                            <span style="font-weight:600;"><?= $content['duration_minutes'] ? $content['duration_minutes'] . ' Dk' : 'Bilinmiyor' ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:var(--text-muted);">IMDb Puanı</span>
                            <span style="font-weight:700; color:#f5c518; font-size:1.2rem; display:flex; align-items:center; gap:5px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <?= number_format($content['imdb_rating'] ?? 0, 1) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Oyuncular -->
                <div style="flex: 2; min-width: 300px;">
                    <?php if (!empty($casts)): ?>
                    <h3 style="margin-bottom:20px; font-size:1.5rem; font-weight:700;">Oyuncu Kadrosu</h3>
                    <div class="cast-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px;">
                        <?php foreach ($casts as $cast): ?>
                        <div class="cast-card" style="background: rgba(255,255,255,0.03); border-radius: 8px; overflow: hidden; text-align: center; padding-bottom: 10px; border: 1px solid rgba(255,255,255,0.05);">
                            <?php if ($cast['photo']): ?>
                                <?php if(strpos($cast['photo'], 'http') === 0): ?>
                                    <img src="<?= e($cast['photo']) ?>" style="width: 100%; aspect-ratio: 1; object-fit: cover; margin-bottom: 10px;" alt="<?= e($cast['name']) ?>">
                                <?php else: ?>
                                    <img src="<?= BASE_URL ?>/uploads/<?= e($cast['photo']) ?>" style="width: 100%; aspect-ratio: 1; object-fit: cover; margin-bottom: 10px;" alt="<?= e($cast['name']) ?>">
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="width: 100%; aspect-ratio: 1; background: #222; display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:#555; margin-bottom:10px;">
                                    <?= e(mb_substr($cast['name'],0,1)) ?>
                                </div>
                            <?php endif; ?>
                            <div style="font-weight:600; font-size:0.95rem; padding: 0 5px;"><?= e($cast['name']) ?></div>
                            <div style="font-size:0.8rem; color:var(--text-muted); padding: 0 5px;"><?= e($cast['character_name']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <h3 style="margin-bottom:20px; font-size:1.5rem; font-weight:700;">Oyuncu Kadrosu</h3>
                    <p style="color:var(--text-muted);">Oyuncu bilgisi henüz eklenmemiş.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- BÖLÜMLER SEKMESİ -->
        <?php if ($content['type'] === 'series' && !empty($seasons)): ?>
        <div id="tab-episodes" class="episodes-section" style="<?= ($content['type'] === 'series' && !empty($seasons)) ? 'display:block;' : 'display:none;' ?>">
            
            <div class="season-tabs">
                <?php $first = true; foreach (array_keys($seasons) as $sNum): ?>
                    <div class="season-tab <?= $first ? 'active' : '' ?>" onclick="document.querySelectorAll('.season-tab').forEach(t=>t.classList.remove('active')); this.classList.add('active'); document.querySelectorAll('.season-content').forEach(c=>c.style.display='none'); document.getElementById('season-<?= $sNum ?>').style.display='block';">
                        <?= $sNum ?>. Sezon
                    </div>
                <?php $first = false; endforeach; ?>
            </div>
            
            <?php $first = true; foreach ($seasons as $sNum => $eps): ?>
            <div class="season-content" id="season-<?= $sNum ?>" style="<?= $first ? 'display:block;' : 'display:none;' ?>">
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                <?php foreach ($eps as $ep): ?>
                <?php 
                    // Başlık kontrolü: Eğer başlık boşsa veya "Sezon" kelimesi geçiyorsa standart "Bölüm X" ismini kullan
                    $epTitle = trim($ep['title']);
                    if (empty($epTitle) || stripos($epTitle, 'sezon') !== false) {
                        $epTitle = 'Bölüm ' . $ep['episode_number'];
                    } else {
                        // Eğer başlık varsa "X. Başlık" formatı
                        $epTitle = $ep['episode_number'] . '. ' . $epTitle;
                    }
                ?>
                <div class="ep-card" onclick="window.location.href='<?= BASE_URL ?>/play.php?id=<?= $contentId ?>&episode=<?= $ep['id'] ?>'" style="display:flex; align-items:center; gap:20px; padding:20px; background:var(--bg-secondary); border-radius:12px; transition:all 0.2s; cursor:pointer; border:1px solid transparent;" onmouseover="this.style.borderColor='rgba(147, 51, 234, 0.4)'; this.style.background='rgba(147, 51, 234, 0.05)';" onmouseout="this.style.borderColor='transparent'; this.style.background='var(--bg-secondary)';">
                    
                    <!-- Devasa Bölüm Numarası -->
                    <div style="font-size: 3.5rem; font-weight: 800; color: rgba(255,255,255,0.1); width: 60px; text-align: center; flex-shrink: 0; line-height: 1;">
                        <?= $ep['episode_number'] ?>
                    </div>

                    <!-- Varsa Thumbnail -->
                    <?php if ($ep['thumbnail']): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= e($ep['thumbnail']) ?>" style="width: 140px; height: 80px; object-fit: cover; border-radius: 6px; flex-shrink: 0;" alt="<?= e($epTitle) ?>">
                    <?php endif; ?>

                    <!-- Bölüm Bilgileri -->
                    <div style="flex: 1;">
                        <div style="font-size: 1.15rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px;">
                            <?= e($epTitle) ?>
                        </div>
                        <?php if(!empty($ep['description'])): ?>
                            <div style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.4; max-width: 800px;">
                                <?= e(mb_substr($ep['description'], 0, 150)) ?><?= mb_strlen($ep['description']) > 150 ? '...' : '' ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Varsa Süre -->
                    <?php if(!empty($ep['duration'])): ?>
                        <div style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500; flex-shrink: 0;">
                            <?= e($ep['duration']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Oynat İkonu -->
                    <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #fff;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-left:2px;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    </div>

                </div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php $first = false; endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</main>

<script>
function toggleAction(action, contentId) {
    let formData = new FormData();
    formData.append('action', action);
    formData.append('content_id', contentId);
    
    fetch('<?= BASE_URL ?>/api/user_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(action === 'toggle_watchlist') {
                const btn = document.getElementById('btn-watchlist');
                const icon = document.getElementById('icon-watchlist');
                if(data.status === 'added') {
                    btn.classList.add('active');
                    icon.innerHTML = '<polyline points="20 6 9 17 4 12"/>';
                } else {
                    btn.classList.remove('active');
                    icon.innerHTML = '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>';
                }
            } else if (action === 'toggle_like') {
                const btn = document.getElementById('btn-like');
                const icon = document.getElementById('icon-like');
                if(data.status === 'added') {
                    btn.classList.add('active');
                    icon.setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('active');
                    icon.setAttribute('fill', 'none');
                }
            }
        } else {
            alert(data.message || 'Bir hata oluştu.');
        }
    })
    .catch(err => console.error(err));
}

function switchDetailTab(tabName, el) {
    // Tab butonlarını sıfırla
    document.querySelectorAll('.detail-tab').forEach(t => {
        t.classList.remove('active');
        t.style.borderBottom = 'none';
        t.style.color = '#aaa';
    });
    
    // Tıklanan butonu aktifleştir
    el.classList.add('active');
    el.style.borderBottom = '3px solid var(--accent-purple)';
    el.style.color = 'var(--text-main)';
    
    // İçerikleri gizle
    const tabDetails = document.getElementById('tab-details');
    const tabEpisodes = document.getElementById('tab-episodes');
    
    if (tabDetails) tabDetails.style.display = 'none';
    if (tabEpisodes) tabEpisodes.style.display = 'none';
    
    // İstenilen içeriği göster
    if (tabName === 'details' && tabDetails) {
        tabDetails.style.display = 'block';
    } else if (tabName === 'episodes' && tabEpisodes) {
        tabEpisodes.style.display = 'block';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
