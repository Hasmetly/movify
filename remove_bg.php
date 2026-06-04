<?php
// Kullanıcının Artifact'inden gelen dosyayı oku (eğer varsa proje klasöründe arayalım)
// Önce Artifact klasöründeki orjinal jpg resmini bulmalıyız: C:\Users\ma737\.gemini\antigravity\brain\7479eb4b-c5bb-4200-a210-2f3a759f86c5\media__1779403038074.jpg

$srcPath = 'C:\Users\ma737\.gemini\antigravity\brain\7479eb4b-c5bb-4200-a210-2f3a759f86c5\media__1779403038074.jpg';
if (!file_exists($srcPath)) {
    echo "Resim bulunamadı!\n";
    exit;
}

$img = imagecreatefromjpeg($srcPath);
if (!$img) {
    echo "Resim açılamadı!\n";
    exit;
}

$width = imagesx($img);
$height = imagesy($img);

$outImg = imagecreatetruecolor($width, $height);
imagesavealpha($outImg, true);
$transparent = imagecolorallocatealpha($outImg, 0, 0, 0, 127);
imagefill($outImg, 0, 0, $transparent);

// Tolerans sınırları
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // Eğer renk gri veya beyaz tonlarındaysa (R, G, B değerleri birbirine çok yakın ve > 130 ise)
        // Check for checkerboard: genellikle RGB(153,153,153) ve RGB(204,204,204) ya da RGB(255,255,255) vs
        // Renksiz (veya az renkli) açık pikseller. R-G, R-B farkı düşük olmalı.
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        
        if ($min > 120 && ($max - $min) < 30) {
            // Arka plan veya beyaz "Movify" yazısı, transparan yap
            imagesetpixel($outImg, $x, $y, $transparent);
        } else {
            // Logodaki mor/pembe renkler korunur
            // Kenarları yumuşatmak için hafif alfa verebiliriz ama düz geçelim
            $color = imagecolorallocatealpha($outImg, $r, $g, $b, 0);
            imagesetpixel($outImg, $x, $y, $color);
        }
    }
}

$destPath = 'c:\xampp\htdocs\movify\assets\images\logo-extracted.png';
imagepng($outImg, $destPath);
imagedestroy($img);
imagedestroy($outImg);

echo "Bitti: " . $destPath;
