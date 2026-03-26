<?php

$host = "localhost";
$port = 3306;
$user = "root";
$pass = "";
$db   = "estu_cms";

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}