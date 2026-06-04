<?php
$apiKey = '15d2ea6d0dc1d476efbca3eba2428bab';
$url = "https://api.themoviedb.org/3/search/multi?api_key={$apiKey}&language=tr-TR&query=Ezel";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
echo "Result:\n" . $result;
