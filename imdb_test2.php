<?php
$imdbId = "tt1375666";
$pageUrl = "https://www.imdb.com/title/" . $imdbId . "/";
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $pageUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    "Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7",
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
    "Accept-Encoding: gzip, deflate, br"
]);
curl_setopt($ch2, CURLOPT_ENCODING, ""); // Auto decode gzip/deflate
$html = curl_exec($ch2);
curl_close($ch2);

file_put_contents('imdb_html.txt', substr($html, 0, 10000));

// Check for JSON-LD
if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches)) {
    file_put_contents('imdb_json.txt', $matches[1]);
} else {
    file_put_contents('imdb_json.txt', 'No JSON-LD found');
}
