<?php

$url = $_GET['url'] ?? 'home';

// bersihin slash
$url = rtrim($url, '/');

switch ($url) {

    case '':
    case 'home':
        require 'pages/home/index.php';
        break;

    case 'login':
        require 'login.php';
        break;

    case 'dashboard':
        require 'pages/dashboard/index.php';
        break;

    case 'homeDesc':
        require 'pages/homeDesc/index.php';
        break;

    case 'homeImage':
        require 'pages/homeImages/index.php';
        break;

    case 'about':
        require 'pages/about/index.php';
        break;

    case 'service':
        require 'pages/service/index.php';
        break;

    case 'portofolio':
        require 'pages/portofolio/index.php';
        break;

    case 'client':
        require 'pages/client/index.php';
        break;

    case 'contact':
        require 'pages/contact/index.php';
        break;

    case 'fileManager':
        require 'pages/fileManager/index.php';
        break;

    case 'linkManager':
        require 'pages/linkManager/index.php';
        break;

    case 'logout':
        require 'logout.php';
        break;

    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}