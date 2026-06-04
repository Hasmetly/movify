<?php
/**
 * Movify - Footer Bileşeni
 */
?>
<!-- Mobil Alt Navigasyon (Bottom Nav) -->
<style>
    @media (max-width: 768px) {
        .bottom-nav {
            padding: 8px 0 calc(8px + env(safe-area-inset-bottom)) !important;
        }
        .bottom-nav__link {
            font-size: 0.7rem !important;
            gap: 3px !important;
            padding: 6px 4px !important;
            min-width: 48px;
            min-height: 48px;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .bottom-nav__link svg {
            width: 28px !important;
            height: 28px !important;
        }
    }
    @media (max-width: 480px) {
        .bottom-nav__link {
            font-size: 0.65rem !important;
        }
        .bottom-nav__link svg {
            width: 26px !important;
            height: 26px !important;
        }
    }
</style>
<nav class="bottom-nav">
    <div class="bottom-nav__inner">
        <a href="<?= BASE_URL ?>/home.php" class="bottom-nav__link active">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Ana Sayfa</span>
        </a>
        <a href="#" class="bottom-nav__link" onclick="document.getElementById('search-input').focus(); return false;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <span>Ara</span>
        </a>
        <a href="#my-list" class="bottom-nav__link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Listem</span>
        </a>
        <a href="<?= BASE_URL ?>/profiles.php" class="bottom-nav__link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profil</span>
        </a>
    </div>
</nav>

<footer style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.9rem; border-top: 1px solid var(--border-color); margin-top: 40px;">
    <p>Movify &copy; 2026. Tüm hakları saklıdır.</p>
</footer>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<script>
    // Header Scroll Şeridi
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        if(header) {
            if(window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
    });
</script>
</body>
</html>
