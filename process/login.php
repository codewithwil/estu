<?php
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        header("Location: ../pages/dashboard");
        exit;
    } else {
        header("Location: ../login.php?error=1");
        exit;
    }
}