<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function login($email, $password) {
    global $conn;

    $email = mysqli_real_escape_string($conn, $email);

    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            return true;
        }
    }

    return false;
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login");
        exit;
    }
}

function logout() {
    session_start();
    $_SESSION = [];
    session_destroy();
    header("Location: /estu/login");
    exit;
}