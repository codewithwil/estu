<?php
require_once '../functions/auth.php';
require_once  '../helper/route.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';

    $result = login($email, $password);

    if ($result['success']) {
        header('Location: ' . base_url() . 'dashboard');
        exit;
    } else {
        if (!empty($result['blocked'])) {
            $_SESSION['blocked_msg'] = $result['error']; 
            header('Location: ' . base_url() . 'login?blocked=1');
        } else {
            header('Location: ' . base_url() . 'login?error=1');
        }
        exit;
    }
}