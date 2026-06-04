/**
 * Movify - Ana JavaScript Dosyası
 * Tema değiştirme, canlı arama, detay modal, slider,
 * watchlist, like, yorum, paylaşım ve bildirim işlemleri
 */

const BASE_URL = '/movify';

// ========================
// TEMA DEĞİŞTİRME
// ========================
function initTheme() {
    const saved = localStorage.getItem('movify-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
    document.body.setAttribute('data-theme', saved);
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const newTheme = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('movify-theme', newTheme);
    
    // Toggle ikon güncelle
    const icon = document.getElementById('theme-toggle-icon');
    if (icon) {
        icon.innerHTML = newTheme === 'dark'
            ? '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>'
            : '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
    }
}

// ========================
// CANLI ARAMA (Live Search)
// ========================
let searchTimeout;
function initLiveSearch() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    if (!searchInput || !searchResults) return;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            searchResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`${BASE_URL}/search-api.php?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.results && data.results.length > 0) {
                        searchResults.innerHTML = data.results.map(item => `
                            <div class="search-result-item" onclick="openDetail(${item.id})">
                                <div class="search-result-item__image">
                                    ${item.cover_url 
                                        ? `<img src="${item.cover_url}" alt="${escapeHtml(item.title)}" loading="lazy">` 
                                        : `<div class="search-result-item__placeholder">${escapeHtml(item.title.charAt(0))}</div>`}
                                </div>
                                <div class="search-result-item__info">
                                    <span class="search-result-item__title">${escapeHtml(item.title)}</span>
                                    <span class="search-result-item__meta">
                                        ${item.type === 'movie' ? 'Film' : 'Dizi'} 
                                        ${item.release_year ? '• ' + item.release_year : ''} 
                                        ${item.imdb_rating ? '• ⭐ ' + item.imdb_rating : ''}
                                    </span>
                                </div>
                            </div>
                        `).join('');
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="search-result-empty">Sonuç bulunamadı</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(() => {
                    searchResults.style.display = 'none';
                });
        }, 300); // 300ms debounce
    });

    // Dışarı tıklanınca kapat
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // ESC ile kapat
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            searchResults.style.display = 'none';
            searchInput.blur();
        }
    });
}

// ========================
// İÇERİK DETAY MODALI
// ========================
function openDetail(contentId) {
    const modal = document.getElementById('content-detail-modal');
    const modalContent = document.getElementById('modal-content');
    if (!modal || !modalContent) return;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // AJAX ile detay yükle
    fetch(`${BASE_URL}/content-detail.php?id=${contentId}`)
        .then(r => r.text())
        .then(html => {
            modalContent.innerHTML = `
                <button class="modal__close" onclick="closeDetail()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                ${html}
            `;
        })
        .catch(err => {
            modalContent.innerHTML = '<p style="color:#fff;padding:40px;">İçerik yüklenirken hata oluştu.</p>';
        });
}

function closeDetail() {
    const modal = document.getElementById('content-detail-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        // Fragman iframe'ini durdur
        const iframe = modal.querySelector('iframe');
        if (iframe) iframe.src = '';
        const video = modal.querySelector('video');
        if (video) video.pause();
    }
}

// ESC ile modal kapat
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDetail();
});

// ========================
// VIDEO OYNATICI AÇMA
// ========================
function openPlayer(contentId, url, episodeId) {
    let playerUrl = `${BASE_URL}/play.php?id=${contentId}`;
    if (url) playerUrl += `&url=${encodeURIComponent(url)}`;
    if (episodeId) playerUrl += `&episode=${episodeId}`;
    window.location.href = playerUrl;
}

// ========================
// WATCHLIST TOGGLE
// ========================
function toggleWatchlist(contentId, btn) {
    fetch(`${BASE_URL}/api.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_watchlist&content_id=${contentId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.status === 'added') {
                btn.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polyline points="20,6 9,17 4,12"/></svg>';
                btn.title = 'Listeden Çıkar';
                showToast('Listeye eklendi');
            } else {
                btn.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
                btn.title = 'Listeme Ekle';
                showToast('Listeden çıkarıldı');
            }
        }
    })
    .catch(() => showToast('Bir hata oluştu', 'error'));
}

// ========================
// LIKE / DISLIKE
// ========================
function toggleLike(contentId, isLike, btn) {
    fetch(`${BASE_URL}/api.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_like&content_id=${contentId}&is_like=${isLike}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.likes !== undefined) {
            // Detay modal'ını yeniden yükle
            openDetail(contentId);
        }
    })
    .catch(() => showToast('Bir hata oluştu', 'error'));
}

// ========================
// YORUM GÖNDERME
// ========================
function submitComment(contentId) {
    const textarea = document.getElementById('comment-text');
    const commentText = textarea.value.trim();
    if (!commentText) return;

    fetch(`${BASE_URL}/api.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_comment&content_id=${contentId}&comment_text=${encodeURIComponent(commentText)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const commentList = document.getElementById('comment-list');
            const emptyMsg = commentList.querySelector('.comment-empty');
            if (emptyMsg) emptyMsg.remove();

            const newComment = document.createElement('div');
            newComment.className = 'comment-item comment-item--new';
            newComment.innerHTML = `
                <div class="comment-item__avatar">${escapeHtml(data.comment.profile_name.charAt(0))}</div>
                <div class="comment-item__body">
                    <span class="comment-item__name">${escapeHtml(data.comment.profile_name)}</span>
                    <span class="comment-item__date">${data.comment.created_at}</span>
                    <p class="comment-item__text">${escapeHtml(data.comment.comment_text)}</p>
                </div>
            `;
            commentList.insertBefore(newComment, commentList.firstChild);
            textarea.value = '';
            showToast('Yorum eklendi');
        }
    })
    .catch(() => showToast('Yorum eklenirken hata oluştu', 'error'));
}

// ========================
// SEZON DEĞİŞTİRME
// ========================
function switchSeason(seasonNum, btn) {
    // Tüm sezon tablarını pasifleştir
    document.querySelectorAll('.season-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    
    // Tüm bölüm listelerini gizle, seçileni göster
    document.querySelectorAll('.episode-list').forEach(el => el.style.display = 'none');
    const target = document.querySelector('.season-' + seasonNum);
    if (target) target.style.display = '';
}

// ========================
// OYUNCU/YÖNETMEN KADROSU TOGGLE
// ========================
function toggleCastSection(btn) {
    const section = document.getElementById('cast-section');
    if (!section) return;
    const visible = section.style.display !== 'none';
    section.style.display = visible ? 'none' : 'block';
    btn.querySelector('svg').style.transform = visible ? '' : 'rotate(180deg)';
}

// ========================
// FİLMOGRAFİ FİLTRESİ
// ========================
function filterByCast(castName) {
    fetch(`${BASE_URL}/api.php?action=filmography&name=${encodeURIComponent(castName)}`)
        .then(r => r.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                let html = `<h3 style="margin-bottom:16px;">${escapeHtml(data.name)} - Filmografi</h3><div class="filmography-grid">`;
                data.results.forEach(item => {
                    html += `
                        <div class="content-card content-card--mini" onclick="openDetail(${item.id})">
                            <div class="content-card__image">
                                ${item.cover_url 
                                    ? `<img src="${item.cover_url}" alt="${escapeHtml(item.title)}" loading="lazy">` 
                                    : `<div class="content-card__placeholder">${escapeHtml(item.title.charAt(0))}</div>`}
                            </div>
                            <span class="content-card__title">${escapeHtml(item.title)}</span>
                        </div>
                    `;
                });
                html += '</div>';
                
                const castSection = document.getElementById('cast-section');
                if (castSection) {
                    castSection.innerHTML += html;
                }
            } else {
                showToast('Bu kişiye ait başka yapım bulunamadı');
            }
        })
        .catch(() => showToast('Filmografi yüklenirken hata oluştu', 'error'));
}

// ========================
// PAYLAŞIM MODALI
// ========================
function openShareModal(contentId, title) {
    const modal = document.getElementById('share-modal');
    if (!modal) return;
    
    const shareUrl = window.location.origin + BASE_URL + '/home.php?detail=' + contentId;
    const shareText = title + ' - Movify\'da izle!';

    document.getElementById('share-twitter').href = `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;
    document.getElementById('share-facebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
    document.getElementById('share-whatsapp').href = `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
    document.getElementById('share-telegram').href = `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;
    
    document.getElementById('share-copy').onclick = () => {
        navigator.clipboard.writeText(shareUrl).then(() => {
            showToast('Link panoya kopyalandı!');
        });
    };

    modal.classList.add('active');
}

function closeShareModal() {
    const modal = document.getElementById('share-modal');
    if (modal) modal.classList.remove('active');
}

// ========================
// BİLDİRİMLER (Coming Soon)
// ========================
function loadNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    if (!dropdown) return;

    fetch(`${BASE_URL}/api.php?action=get_notifications`)
        .then(r => r.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                dropdown.innerHTML = data.notifications.map(n => `
                    <div class="notification-item">
                        ${n.image_url ? `<img src="${n.image_url}" alt="" class="notification-item__image">` : ''}
                        <div class="notification-item__info">
                            <span class="notification-item__title">${escapeHtml(n.title)}</span>
                            <span class="notification-item__message">${escapeHtml(n.message || '')}</span>
                            ${n.expected_date ? `<span class="notification-item__date">${n.expected_date}</span>` : ''}
                        </div>
                    </div>
                `).join('');
            } else {
                dropdown.innerHTML = '<p class="notification-empty">Yeni bildirim yok</p>';
            }
        });
}

function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    if (!dropdown) return;
    const isVisible = dropdown.style.display === 'block';
    dropdown.style.display = isVisible ? 'none' : 'block';
    if (!isVisible) loadNotifications();
}

// ========================
// SLIDER KAYDIRMA
// ========================
function initSliders() {
    document.querySelectorAll('.content-slider').forEach(slider => {
        const track = slider.querySelector('.content-slider__track');
        const leftArrow = slider.querySelector('.slider-arrow--left');
        const rightArrow = slider.querySelector('.slider-arrow--right');
        if (!track) return;

        const scrollAmount = 400;

        if (leftArrow) {
            leftArrow.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }
        if (rightArrow) {
            rightArrow.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }
    });
}

// ========================
// TOAST BİLDİRİMİ
// ========================
function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ========================
// PROFİL DROPDOWN
// ========================
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
}

// ========================
// MOBİL MENÜ
// ========================
function toggleMobileMenu() {
    const overlay = document.getElementById('mobile-nav-overlay');
    if (!overlay) return;
    
    overlay.classList.toggle('active');
    document.body.classList.toggle('mobile-menu-open');
}

// Mobil menü linklerine tıklanınca menüyü kapat
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('mobile-nav-overlay');
    if (overlay) {
        overlay.querySelectorAll('.mobile-nav-overlay__link').forEach(link => {
            link.addEventListener('click', () => {
                overlay.classList.remove('active');
                document.body.classList.remove('mobile-menu-open');
            });
        });
    }
});

// ========================
// YARDIMCI FONKSİYONLAR
// ========================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ========================
// BAŞLATMA
// ========================
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initLiveSearch();
    initSliders();

    // Dışarı tıklayınca dropdown'ları kapat
    document.addEventListener('click', (e) => {
        // Profil dropdown
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');
        if (profileBtn && profileDropdown && !profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.style.display = 'none';
        }
        // Bildirim dropdown
        const notifBtn = document.getElementById('notifications-btn');
        const notifDropdown = document.getElementById('notifications-dropdown');
        if (notifBtn && notifDropdown && !notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.style.display = 'none';
        }
    });
});
