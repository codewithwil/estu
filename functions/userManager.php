<?php
require_once __DIR__ . '/../config/database.php';

function getAllUsers($search = '', $role = '') {
    global $conn;
    
    $sql = "SELECT id, name, email, role, created_at, 
            (SELECT COUNT(*) FROM links WHERE created_by = users.id) as link_count
            FROM users WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($search) {
        $sql .= " AND (name LIKE ? OR email LIKE ?)";
        $like = "%$search%";
        $params[] = $like; $params[] = $like;
        $types .= "ss";
    }
    
    if ($role && in_array($role, ['superadmin','admin','editor'])) {
        $sql .= " AND role = ?";
        $params[] = $role;
        $types .= "s";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function getUserById($id) {
    global $conn;
    $id = intval($id);
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function createUser($data) {
    global $conn;
    
    $name       = trim($data['name'] ?? '');
    $email      = trim($data['email'] ?? '');
    $password   = $data['password'] ?? '';
    $role       = $data['role'] ?? 'editor';
    
    if (!$name || !$email || !$password) {
        return ['success' => false, 'error' => 'Semua field wajib diisi'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Email tidak valid'];
    }
    
    if (!in_array($role, ['superadmin','admin','editor'])) {
        return ['success' => false, 'error' => 'Role tidak valid'];
    }
    
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
        return ['success' => false, 'error' => 'Email sudah terdaftar'];
    }
    
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hash, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'id' => mysqli_insert_id($conn)];
    }
    return ['success' => false, 'error' => 'Gagal membuat user'];
}

function updateUser($id, $data) {
    global $conn;
    $id = intval($id);
    
    $currentUser = getCurrentUser();
    if ($currentUser['id'] == $id && isset($data['role']) && $data['role'] !== $currentUser['role']) {
        return ['success' => false, 'error' => 'Tidak bisa mengubah role diri sendiri'];
    }
    
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $role = $data['role'] ?? null;
    
    if (!$name || !$email) {
        return ['success' => false, 'error' => 'Nama dan email wajib diisi'];
    }
    
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $email, $id);
    mysqli_stmt_execute($stmt);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
        return ['success' => false, 'error' => 'Email sudah digunakan user lain'];
    }
    
    if ($role && !in_array($role, ['superadmin','admin','editor'])) {
        return ['success' => false, 'error' => 'Role tidak valid'];
    }
    
    if ($role) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, email=?, role=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $role, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, email=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
    }
    
    return ['success' => mysqli_stmt_execute($stmt)];
}

function updateUserPassword($id, $newPassword) {
    global $conn;
    $id = intval($id);
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'error' => 'Password minimal 6 karakter'];
    }
    
    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $hash, $id);
    return ['success' => mysqli_stmt_execute($stmt)];
}

function deleteUser($id) {
    global $conn;
    $id = intval($id);
    
    $currentUser = getCurrentUser();
    if ($currentUser['id'] == $id) {
        return ['success' => false, 'error' => 'Tidak bisa menghapus diri sendiri'];
    }
    
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'superadmin'");
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    $target = getUserById($id);
    if ($target['role'] === 'superadmin' && $result['total'] <= 1) {
        return ['success' => false, 'error' => 'Tidak bisa menghapus superadmin terakhir'];
    }
    
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return ['success' => mysqli_stmt_execute($stmt)];
}

function getRoleStats() {
    global $conn;
    $sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $result = mysqli_query($conn, $sql);
    $stats = ['superadmin' => 0, 'admin' => 0, 'editor' => 0];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[$row['role']] = $row['count'];
    }
    return $stats;
}