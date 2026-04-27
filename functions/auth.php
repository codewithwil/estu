<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

function checkAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . base_url() . 'login');
        exit;
    }
}

function getUserIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function verifyPassword($password, $hash) {
    if (password_verify($password, $hash)) {
        return true;
    }
    
    if (strpos($hash, '$2a$') === 0) {
        $converted = '$2y$' . substr($hash, 4);
        if (password_verify($password, $converted)) {
            return true;
        }

        return hash_equals($hash, crypt($password, $hash));
    }
    
    return false;
}

function login($email, $password) {
    global $conn;

    if (isRateLimited($email)) {
        $remaining = getLockRemainingTime($email);
        $msg = 'Terlalu banyak percobaan login. Coba lagi dalam ' . formatDuration($remaining);
        return ['success' => false, 'error' => $msg, 'blocked' => true];
    }
    if (isIPRateLimited()) {
        return ['success' => false, 'error' => 'Terlalu banyak request dari IP ini', 'blocked' => true];
    }

    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password, role FROM users WHERE email = ? AND status = 'active' LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && verifyPassword($password, $user['password'])) {

        clearLoginAttempts($email); 

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'] ?? 'editor';

        session_regenerate_id(true);

        return ['success' => true, 'user' => $user];
    }

    recordFailedLogin($email);

    usleep(500000);
    return ['success' => false, 'error' => 'Email atau password salah'];
}

function logout() {
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

function getCurrentRole() {
    return $_SESSION['user_role'] ?? 'guest';
}

function isSuperadmin() {
    return getCurrentRole() === 'superadmin';
}

function isAdmin() {
    return in_array(getCurrentRole(), ['superadmin', 'admin']);
}

function isEditor() {
    return in_array(getCurrentRole(), ['superadmin', 'admin', 'editor']);
}

function hasRole($requiredRole) {
    $hierarchy = ['editor' => 1, 'admin' => 2, 'superadmin' => 3];
    $current = $hierarchy[getCurrentRole()] ?? 0;
    $required = $hierarchy[$requiredRole] ?? 999;
    return $current >= $required;
}

function requireRole($minRole) {
    checkAuth();
    if (!hasRole($minRole)) {
        http_response_code(403);
        include __DIR__ . '/../pages/errors/403.php';
        exit;
    }
}

function can($permission) {
    $role = getCurrentRole();
    
    if ($role === 'superadmin') return true;
    
    $permissions = [
        'editor' => [
            'dashboard.view',
            'content.view', 'content.create', 'content.edit',
            'filemanager.view', 'filemanager.upload',
            'linkmanager.view', 'linkmanager.create', 'linkmanager.edit',
            'profile.view', 'profile.edit'
        ],
        'admin' => [
            'dashboard.view',
            'content.view', 'content.create', 'content.edit', 'content.delete',
            'filemanager.view', 'filemanager.upload', 'filemanager.delete', 'filemanager.folder',
            'linkmanager.view', 'linkmanager.create', 'linkmanager.edit', 'linkmanager.delete', 'linkmanager.folder',
            'users.view', 
            'profile.view', 'profile.edit'
        ]
    ];
    
    return in_array($permission, $permissions[$role] ?? []);
}

function requirePermission($permission) {
    checkAuth();
    if (!can($permission)) {
        http_response_code(403);
        include __DIR__ . '/../pages/errors/403.php';
        exit;
    }
}

function isRateLimited($email) {
    global $conn;
    $ip = getUserIP();

    $stmt = mysqli_prepare($conn, "SELECT attempts, locked_until FROM login_attempts WHERE email=? AND ip_address=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$data) return false;

    if ($data['locked_until'] && strtotime($data['locked_until']) > time()) {
        return true;
    }

    if ($data['locked_until'] && strtotime($data['locked_until']) <= time()) {
        $stmt = mysqli_prepare($conn, "UPDATE login_attempts SET attempts=0, locked_until=NULL WHERE email=? AND ip_address=?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
        mysqli_stmt_execute($stmt);
        return false;
    }

    return false;
}

function getLockRemainingTime($email) {
    global $conn;
    $ip = getUserIP();

    $stmt = mysqli_prepare($conn, "SELECT locked_until FROM login_attempts WHERE email=? AND ip_address=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$data || !$data['locked_until']) return 0;

    $remaining = strtotime($data['locked_until']) - time();
    return max(0, $remaining);
}

function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . ' detik';
    } elseif ($seconds < 3600) {
        $m = ceil($seconds / 60);
        return $m . ' menit';
    } elseif ($seconds < 86400) {
        $h = ceil($seconds / 3600);
        return $h . ' jam';
    } else {
        $d = ceil($seconds / 86400);
        return $d . ' hari';
    }
}

function isIPRateLimited() {
    global $conn;
    $ip = getUserIP();

    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) as total 
        FROM login_attempts 
        WHERE ip_address = ? 
        AND last_attempt > (NOW() - INTERVAL 5 MINUTE)
    ");
    mysqli_stmt_bind_param($stmt, "s", $ip);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    return $data['total'] >= 20;
}

function recordFailedLogin($email) {
    global $conn;
    $ip = getUserIP();

    $stmt = mysqli_prepare($conn, "SELECT attempts, locked_until FROM login_attempts WHERE email=? AND ip_address=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($result) {
        if ($result['locked_until'] && strtotime($result['locked_until']) > time()) {
            return;
        }

        $attempts   = $result['attempts'] + 1;
        $lockUntil  = null;
        if ($attempts > 0 && $attempts % 3 === 0) {
            $failures   = $attempts / 3;           
            $lockHours  = pow(2, $failures - 1);    
            $lockUntil  = date('Y-m-d H:i:s', time() + ($lockHours * 3600));
        }

        $stmt = mysqli_prepare($conn, "UPDATE login_attempts SET attempts=?, last_attempt=NOW(), locked_until=? WHERE email=? AND ip_address=?");
        mysqli_stmt_bind_param($stmt, "isss", $attempts, $lockUntil, $email, $ip);
        mysqli_stmt_execute($stmt);

    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (email, ip_address, attempts, last_attempt) VALUES (?, ?, 1, NOW())");
        mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
        mysqli_stmt_execute($stmt);
    }
}

function clearLoginAttempts($email) {
    global $conn;
    $ip = getUserIP();

    $stmt = mysqli_prepare($conn, "DELETE FROM login_attempts WHERE email=? AND ip_address=?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $ip);
    mysqli_stmt_execute($stmt);
}