<?php
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $result = login($email, $password);

    if ($result === true) {
        header("Location: ../pages/dashboard");
        exit;
    } elseif ($result === 'blocked') {
        header("Location: ../login.php?blocked=1");
        exit;
    } else {
        header("Location: ../login.php?error=1");
        exit;
    }
    
}