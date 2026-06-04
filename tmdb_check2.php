<?php
$keys = [
    '3e6cb44053e3612ce6a086ba011f016f',
    'f81980ff410e46f422d64daf862cb978',
    '2dca580c2a14b55200e784d157207b4d',
    '9b63a92ccb45d2994fb11d4d29f8f4a1'
];
foreach($keys as $apiKey) {
    $url = "https://api.themoviedb.org/3/search/multi?api_key={$apiKey}&language=tr-TR&query=Ezel";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    echo "Key: $apiKey -> " . substr($result, 0, 100) . "\n\n";
}
