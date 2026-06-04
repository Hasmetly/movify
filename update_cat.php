<?php
require 'includes/db.php';
$pdo = db();

// Önce bağımlı kayıtlar varsa silinmesin diye foreign key check'i kapatıp açalım (gerçi şu an boş)
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE categories; SET FOREIGN_KEY_CHECKS = 1;");

$cats = [
    ['Tüm Kategoriler', 'tum-kategoriler'],
    ['Aksiyon', 'aksiyon'],
    ['Anime', 'anime'],
    ['Astroloji', 'astroloji'],
    ['Bağımsız', 'bagimsiz'],
    ['Belgeseller', 'belgeseller'],
    ['Bilim Kurgu', 'bilim-kurgu'],
    ['Çocuk ve Aile', 'cocuk-ve-aile'],
    ['Drama', 'drama'],
    ['Fantastik', 'fantastik'],
    ['Gerilim', 'gerilim'],
    ['Kısa Filmler', 'kisa-filmler'],
    ['Klasikler', 'klasikler'],
    ['Hollywood', 'hollywood'],
    ['Korku', 'korku'],
    ['Komedi', 'komedi'],
    ['Ödüllü Yapımlar', 'odullu-yapimlar'],
    ['Romantizm', 'romantizm'],
    ['Spor', 'spor'],
    ['Stand-Up', 'stand-up'],
    ['Yerli Yapımlar', 'yerli-yapimlar'],
    ['Suç', 'suc']
];

$stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
foreach ($cats as $c) {
    $stmt->execute([$c[0], $c[1]]);
}
echo "Kategoriler düzeltildi.\n";
