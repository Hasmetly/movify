<?php
// Test IMDB Scraping using suggestion endpoint
$title = "Inception";
$firstLetter = strtolower(substr($title, 0, 1));
$searchUrl = "https://v3.sg.media-imdb.com/suggestion/x/" . urlencode(strtolower($title)) . ".json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $searchUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!empty($data['d'][0]['id'])) {
    $imdbId = $data['d'][0]['id'];
    echo "Found ID: " . $imdbId . "\n";
    
    // Scrape details from imdb page
    $pageUrl = "https://www.imdb.com/title/" . $imdbId . "/";
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $pageUrl);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        "Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7"
    ]);
    $html = curl_exec($ch2);
    curl_close($ch2);
    
    // Extract rating
    if (preg_match('/<span class="sc-[^"]+ imdb-rating">([0-9.]+)<\/span>/i', $html, $matches) || preg_match('/"aggregateRating":\{"@type":"AggregateRating","ratingValue":([0-9.]+),/i', $html, $matches)) {
        echo "Rating: " . $matches[1] . "\n";
    } else {
        echo "Rating not found.\n";
    }
    
    // Extract year
    if (preg_match('/<a[^>]*href="\/title\/tt[^\/]+\/releaseinfo[^"]*"[^>]*>(\d{4})<\/a>/i', $html, $matches) || preg_match('/"datePublished":\s*"(\d{4})-\d{2}-\d{2}"/i', $html, $matches)) {
        echo "Year: " . $matches[1] . "\n";
    } else {
        echo "Year not found.\n";
    }
    
    // Extract description
    if (preg_match('/<span data-testid="plot-xs_to_m"[^>]*>(.*?)<\/span>/i', $html, $matches) || preg_match('/"description":\s*"([^"]+)"/i', $html, $matches)) {
        echo "Description: " . html_entity_decode($matches[1]) . "\n";
    } else {
        echo "Description not found.\n";
    }

} else {
    echo "No results for title.\n";
}
