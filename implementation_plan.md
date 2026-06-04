# Gelişmiş Video Oynatıcı Kontrolleri (Settings Menu) Ekleme Planı

Özel oynatıcımıza (play.php) modern video izleme platformlarındaki gibi gelişmiş özellikler entegre edilecek. Oynatıcı paneline bir **Ayar Menüsü (Dişli Çark)** ve alt kısımdaki butonlara **Önceki / Sonraki Bölüm** navigasyonları eklenecek.

## User Review Required

> [!WARNING]
> YouTube'un yeni politikaları gereği API üzerinden **çözünürlük/kalite değiştirme** (`setPlaybackQuality`) komutları çalışmamaktadır (YouTube hızı kendi otomatik belirler). HTML5 (mp4) yüklemelerinde de tek bir video bağlantınız varsa gerçek zamanlı kalite değişimi olmaz.
> Ancak bu özelliği arayüze (UI) modern bir görünüme sahip olması ve gelecekte çoklu sunucu sistemi eklendiğinde aktif kullanılabilmesi için menüye dahil edeceğim. YouTube izlenirken de sistem komutu gönderecek, ancak Youtube'un uygulayıp uygulamaması kendi inisiyatifinde kalacak. Bu UI eklentisini onaylıyor musunuz?

> [!WARNING]
> Şu an veritabanınızda Alt Yazı (Subtitle - vtt/srt) barındırmak için özel bir tablo veya yükleme alanı (Admin panelinde) bulunmuyor. Ben oynatıcıya **YouTube'un kendi altyazılarını** kontrol edebilecek (Varsa açıp kapatacak) bir alt yazı ayarı ve HTML5 mp4'ler için altyapı ekleyeceğim. Eğer yerel MP4 videolarınız için kendi alt yazılarınızı yüklemek isterseniz, daha sonra admin paneline alt yazı (vtt/srt) upload modülü yazmamız gerekecektir.

## Proposed Changes

Aşağıdaki dosyalarda köklü güncellemeler yapılacaktır:

### Oynatıcı Kontrolleri (play.php)
Oynatıcı panelinde yer alan kontrol çubuğu baştan aşağı yenilenecek ve menü yapısı oluşturulacak.

- **[MODIFY]** `play.php`
  - Kontrol paneline (Control Bar) `Önceki Bölüm (|<)` ve `Sonraki Bölüm (>|)` butonları eklenecek.
  - Oynatma hızı butonunun yanına yeni bir `Dişli (Ayarlar)` ikonu eklenecek.
  - Settings Modal/Menu eklenecek. Bu menüye tıklandığında:
    - **Ekran Boyutu:** (Normal, Doldur, Sığdır) - CSS ile Iframe/Video Scale ayarı yapılarak görüntü formatı değiştirilecek.
    - **Kalite:** (Otomatik, 1080p, 720p, 480p) - Youtube API ve HTML5 oynatma kalitesi (Arayüz amaçlı)
    - **Oynatma Hızı:** (Mevcut buton menünün içine entegre edilerek daha düzenli hale getirilecek).
    - **Alt Yazı:** (Açık, Kapalı) - `cc_load_policy` yönergeleri ve Track (Altyazı) görünürlüğü tetiklenecek.
    - **Alt Yazı Boyutu:** (Küçük, Normal, Büyük) - Kullanıcı tercihini `localStorage` ile tarayıcıya kaydedeceğiz, sayfa yenilense de seçimi sabit kalacak.
  - Gerekli tüm JavaScript fonksiyonları (`changeVideoFit`, `changeQuality`, `toggleSubtitles`, `changeSubSize`) eklenecek.

### Stil Düzenlemeleri (style.css)
- **[MODIFY]** `assets/css/style.css`
  - Oynatıcı Ayarlar Menüsü (Settings Dropdown) için modern, siyah gradientli şık bir arayüz stili eklenecek.
  - Menüdeki "Aktif" (Seçili) ayarı göstermek için Pembe tik ikonları veya vurgulama stilleri eklenecek.
  - Video Fit (Zoom/Cover) seçenekleri için ekstra CSS classları eklenecek.

## Verification Plan

### Manual Verification
- Oynatıcı sayfasına girilecek ve Ayarlar ikonuna tıklanacak.
- Ekran Boyutu "Doldur" (Fill) seçildiğinde videonun dış kenarlardaki siyah boşlukları kapatıp kapatmadığı test edilecek.
- Alt Yazı Aç/Kapat fonksiyonu (varsa YouTube CC özelliği ile) test edilecek.
- Sonraki ve Önceki Bölüm tuşlarının çalışıp doğru URL'ye yönlendirdiği teyit edilecek.
- Alt Yazı boyutunun değiştirildiğinde, kapatılıp açıldığında ayarların korunduğu teyit edilecek.
