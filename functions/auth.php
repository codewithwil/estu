<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function getUserIP() {
    return $_SERVER['REMOTE_ADDR'];
}

function login($email, $password) {
    global $conn;

    $ip = getUserIP();

    $check = mysqli_query($conn, "SELECT * FROM login_attempts WHERE ip_address='$ip' LIMIT 1");
    $attemptData = mysqli_fetch_assoc($check);

    if ($attemptData) {
        if ($attemptData['blocked_until'] && strtotime($attemptData['blocked_until']) > time()) {
            return 'blocked';
        }
    }

    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    $success = false;

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $success = true;

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
        }
    }

    if ($success) {
        mysqli_query($conn, "DELETE FROM login_attempts WHERE ip_address='$ip'");
        return true;
    }

    if ($attemptData) {
        $attempts = $attemptData['attempts'] + 1;

        if ($attempts >= 3) {
            $blockedUntil = date('Y-m-d H:i:s', strtotime('+24 hours'));

            mysqli_query($conn, "
                UPDATE login_attempts 
                SET attempts=$attempts, 
                    last_attempt=NOW(),
                    blocked_until='$blockedUntil'
                WHERE ip_address='$ip'
            ");

            return 'blocked';
        } else {
            mysqli_query($conn, "
                UPDATE login_attempts 
                SET attempts=$attempts, last_attempt=NOW()
                WHERE ip_address='$ip'
            ");
        }
    } else {
        mysqli_query($conn, "
            INSERT INTO login_attempts (ip_address, attempts, last_attempt)
            VALUES ('$ip', 1, NOW())
        ");
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