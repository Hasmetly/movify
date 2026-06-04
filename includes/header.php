<?php
/**
 * Movify - Header Bileşeni
 */
$pageTitle = $pageTitle ?? 'Movify';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
    <script>
        const savedTheme = localStorage.getItem('movify-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>

<header class="header">
    <div class="header__inner container">
        
        <div class="header__left">
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <a href="<?= BASE_URL ?>/home.php" class="logo">
                <img src="<?= BASE_URL ?>/assets/images/movifylogo.png" alt="Movify Logo" class="logo-img">
            </a>
            
            <nav class="nav-links" id="main-nav">
                <a href="<?= BASE_URL ?>/home.php?type=all" class="nav-link <?= (!isset($_GET['type']) || $_GET['type'] == 'all') ? 'active' : '' ?>">Ana Sayfa</a>
                <a href="<?= BASE_URL ?>/home.php?type=series" class="nav-link <?= (isset($_GET['type']) && $_GET['type'] == 'series') ? 'active' : '' ?>">Diziler</a>
                <a href="<?= BASE_URL ?>/home.php?type=movie" class="nav-link <?= (isset($_GET['type']) && $_GET['type'] == 'movie') ? 'active' : '' ?>">Filmler</a>
                    <div class="dropdown-container">
                        <button class="dropdown-btn nav-link" onclick="document.getElementById('cat-dropdown').style.display = document.getElementById('cat-dropdown').style.display === 'grid' ? 'none' : 'grid'" style="background:none; border:none; padding:0; font-family:inherit; cursor:pointer;">
                            Kategoriler <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:4px;"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="dropdown-menu" id="cat-dropdown" style="display: none; grid-template-columns: repeat(3, 1fr); gap: 5px; min-width: 450px; padding: 15px; left: 0; right: auto;">
                            <?php 
                            if (function_exists('db')) {
                                $cats = db()->query("SELECT * FROM categories")->fetchAll();
                                foreach ($cats as $cat) {
                                    echo '<a href="'.BASE_URL.'/home.php?cat='.e($cat['id']).'" class="dropdown-item" style="padding: 8px 15px; text-align: left; border-radius: 4px;">'.e($cat['name']).'</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
            </nav>
        </div>

        <div class="header__right">
            <!-- Yakında Gelecekler -->
            <a href="<?= BASE_URL ?>/coming-soon.php" class="nav-link" style="display:flex; align-items:center; gap:5px; margin-right: 15px; color: var(--text-main);" title="Yakında Gelecekler">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span class="search-text" style="font-weight: 500;">YAKINDA</span>
            </a>

            <!-- Arama -->
            <div class="search-wrapper">
                <div class="search-trigger" onclick="document.querySelector('.search-input-container').classList.toggle('active'); document.getElementById('search-input').focus()">
                    <span class="search-text">ARA</span>
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <form action="<?= BASE_URL ?>/search.php" method="GET" class="search-input-container" style="margin:0; padding:0; display:flex; position:relative;">
                    <input type="text" name="q" class="search-input" id="search-input" placeholder="Aramak için yazın..." autocomplete="off">
                    <button type="submit" style="display:none;"></button>
                    <div class="search-results" id="search-results"></div>
                </form>
            </div>

            <!-- Profil Dropdown -->
            <?php if (isLoggedIn()): ?>
            <div class="dropdown-container">
                <button class="dropdown-btn" id="profile-btn" onclick="toggleProfileDropdown()">
                    <?php 
                        $headerAvatar = 'default-avatar.png';
                        $headerName = 'Profil';
                        if (currentProfileId()) {
                            $pdoHeader = db();
                            $stmtH = $pdoHeader->prepare("SELECT profile_name, avatar_image FROM profiles WHERE id = ?");
                            $stmtH->execute([currentProfileId()]);
                            $ph = $stmtH->fetch();
                            if ($ph) {
                                $headerAvatar = $ph['avatar_image'];
                                $headerName = $ph['profile_name'];
                            }
                        }
                    ?>
                    <div class="avatar-glow">
                        <?php if ($headerAvatar && $headerAvatar !== 'default-avatar.png'): ?>
                            <img src="<?= BASE_URL ?>/uploads/<?= e($headerAvatar) ?>" alt="<?= e($headerName) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:bold;color:#fff;">
                                <?= e(mb_substr($headerName, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </button>
                <div class="dropdown-menu" id="profile-dropdown" style="display: none; right: 0;">
                    <a href="<?= BASE_URL ?>/profiles.php" class="dropdown-item">Profil Değiştir</a>
                    <a href="<?= BASE_URL ?>/profile-settings.php" class="dropdown-item">Hesap Ayarları</a>
                    <?php if (isAdmin()): ?>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>/admin/index.php" class="dropdown-item" style="color:var(--accent-pink);">Yönetim Paneli</a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item" style="width:100%; text-align:left; background:none; border:none; cursor:pointer;" onclick="toggleTheme()">
                        Tema Değiştir <span id="theme-toggle-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-left:8px;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg></span>
                    </button>
                    <div class="dropdown-divider"></div>
                    <a href="<?= BASE_URL ?>/logout.php" class="dropdown-item">Çıkış Yap</a>
                </div>
            </div>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php" class="btn btn--primary">Giriş Yap</a>
            <?php endif; ?>
        </div>
        
    </div>
</header>

<!-- Mobile Nav Overlay -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay">
    <button class="mobile-nav-overlay__close" onclick="toggleMobileMenu()" aria-label="Menüyü Kapat">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <a href="<?= BASE_URL ?>/home.php?type=all" class="mobile-nav-overlay__link <?= (!isset($_GET['type']) || $_GET['type'] == 'all') ? 'active' : '' ?>">Ana Sayfa</a>
    <a href="<?= BASE_URL ?>/home.php?type=series" class="mobile-nav-overlay__link <?= (isset($_GET['type']) && $_GET['type'] == 'series') ? 'active' : '' ?>">Diziler</a>
    <a href="<?= BASE_URL ?>/home.php?type=movie" class="mobile-nav-overlay__link <?= (isset($_GET['type']) && $_GET['type'] == 'movie') ? 'active' : '' ?>">Filmler</a>
    <a href="#" class="mobile-nav-overlay__link" onclick="event.preventDefault(); document.getElementById('mobile-cat-list').style.display = document.getElementById('mobile-cat-list').style.display === 'flex' ? 'none' : 'flex';">Kategoriler</a>
    <div id="mobile-cat-list" style="display:none; flex-wrap:wrap; gap:10px; justify-content:center; max-width:320px; padding:10px 0;">
        <?php 
        if (function_exists('db')) {
            $mobileCats = db()->query("SELECT * FROM categories")->fetchAll();
            foreach ($mobileCats as $mcat) {
                echo '<a href="'.BASE_URL.'/home.php?cat='.e($mcat['id']).'" class="mobile-nav-overlay__link" style="font-size:1rem; padding:8px 16px; min-width:auto; background:rgba(255,255,255,0.05); border-radius:8px;">'.e($mcat['name']).'</a>';
            }
        }
        ?>
    </div>
</div>
