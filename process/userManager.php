<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/userManager.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

function res($data = [], $success = true) {
    echo json_encode(['success' => $success, 'data' => $data]);
    exit;
}

requireRole('admin');

$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($action) {
        case 'list':
            $search = $_GET['search'] ?? '';
            $role = $_GET['role'] ?? '';
            res(getAllUsers($search, $role));
            break;

        case 'get':
            $user = getUserById($_GET['id'] ?? 0);
            if (!$user) throw new Exception('User tidak ditemukan');
            res($user);
            break;

        case 'create':
            if (!isSuperadmin()) throw new Exception('Hanya Superadmin yang bisa menambah user');
            $result = createUser($input);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'User berhasil dibuat', 'id' => $result['id']]);
            break;

        case 'update':
            if (empty($input['id'])) throw new Exception('ID user wajib diisi');
            $target = getUserById($input['id']);
            if (!$target) throw new Exception('User tidak ditemukan');
            if (!isSuperadmin() && $target['role'] !== 'editor') {
                throw new Exception('Admin hanya bisa mengedit Editor');
            }
            $result = updateUser($input['id'], $input);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'User berhasil diupdate']);
            break;

        case 'updatePassword':
            if (empty($input['id']) || empty($input['password'])) {
                throw new Exception('ID dan password baru wajib diisi');
            }
            $target = getUserById($input['id']);
            if (!$target) throw new Exception('User tidak ditemukan');
            if (!isSuperadmin() && $target['role'] !== 'editor') {
                throw new Exception('Admin hanya bisa reset password Editor');
            }
            $result = updateUserPassword($input['id'], $input['password']);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'Password berhasil diupdate']);
            break;

        case 'delete':
            if (empty($input['id'])) throw new Exception('ID user wajib diisi');
            $target = getUserById($input['id']);
            if (!$target) throw new Exception('User tidak ditemukan');
            if (!isSuperadmin() && $target['role'] !== 'editor') {
                throw new Exception('Admin hanya bisa menghapus Editor');
            }
            $result = deleteUser($input['id']);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'User berhasil dihapus']);
            break;

        case 'stats':
            res(getRoleStats());
            break;

        default:
            throw new Exception('Action tidak valid');
    }

} catch (Exception $e) {
    res(['error' => $e->getMessage()], false);
}