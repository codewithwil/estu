<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/fileManager.php';

header('Content-Type: application/json');

checkAuth();

$method = $_SERVER['REQUEST_METHOD'];
$path   = $_GET['path'] ?? '';

if (!in_array($path, ['files/download', 'folders/download'])) {
    header('Content-Type: application/json');
}

function getJson() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function res($data = [], $success = true) {
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

try {
    switch ($path) {
        case 'folders/list':
            if ($method !== 'GET') throw new Exception('Method not allowed');
            $parentId = $_GET['parent_id'] ?? 1;
            res(['data' => getSubfolders($parentId)]);
            break;

        case 'files/list':
            if ($method !== 'GET') throw new Exception('Method not allowed');
            $folderId = $_GET['folder_id'] ?? 1;
            res(['data' => getFilesByFolder($folderId)]);
            break;

        case 'folders/tree':
            if ($method !== 'GET') throw new Exception('Method not allowed');
            $excludeId = $_GET['exclude_id'] ?? null;
            $tree = buildFolderTree(1, $excludeId);
            res(['data' => $tree]);
            break;

        case 'folders/breadcrumbs':
            if ($method !== 'GET') throw new Exception('Method not allowed');
            $id = $_GET['id'] ?? 1;
            res(['data' => getBreadcrumbs($id)]);
            break;

        case 'folders/create':
            if ($method !== 'POST') throw new Exception('Method not allowed');
            $d = getJson();
            res(createFolder($d['name'], $d['parent_id'] ?? 0));
            break;

        case 'folders/rename':
            if ($method !== 'POST') throw new Exception('Method not allowed');
            $d = getJson();
            res(renameFolder($d['id'], $d['name']));
            break;

        case 'folders/move':
            if ($method !== 'POST') throw new Exception('Method not allowed');
            $d = getJson();
            res(moveFolder($d['id'], $d['target_folder_id']));
            break;

        case 'folders/delete':
            if ($method !== 'DELETE') throw new Exception('Method not allowed');
            $d = getJson();
            res(deleteFolder($d['id']));
            break;

        case 'files/upload':
            if ($method !== 'POST') throw new Exception('Method not allowed');
            res(uploadFile($_FILES['file'] ?? null, $_POST['folder_id'] ?? 0));
            break;

        case 'files/rename':
            if ($method !== 'POST') throw new Exception('Method not allowed');
            $d = getJson();
            res(renameFile($d['id'], $d['name']));
            break;

        case 'files/move':
            if ($method !== 'POST') throw new Exception('Method not allowed');
            $d = getJson();
            res(moveFile($d['id'], $d['target_folder_id']));
            break;

        case 'files/delete':
            if ($method !== 'DELETE') throw new Exception('Method not allowed');
            $d = getJson();
            res(deleteFile($d['id']));
            break;

        case 'files/download':
            downloadFile($_GET['id'] ?? null);
            exit;

        case 'folders/download':
            if ($method !== 'GET') throw new Exception('Method not allowed');
            downloadFolder($_GET['id']);
            break;
        default:
            throw new Exception('Invalid path');
        }
    } catch (Exception $e) {
        res(['error' => $e->getMessage()], false);
}
// Helper function untuk update file content
function updateFileContent($fileId, $content) {
    global $conn;
    
    // Get file info dari database
    $stmt = mysqli_prepare($conn, "SELECT filepath, filename FROM files WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $fileId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $file = mysqli_fetch_assoc($result);
    
    if (!$file) {
        error_log("updateFileContent: File ID $fileId not found in database");
        return false;
    }
    
    // Resolve path dengan benar
    $relativePath = ltrim($file['filepath'], '/');
    $possiblePaths = [
        __DIR__ . '/../../' . $relativePath,
        __DIR__ . '/../../uploads/files/' . basename($file['filepath']),
        '/var/www/html/' . $relativePath, // Sesuaikan dengan document root Anda
    ];
    
    $path = null;
    foreach ($possiblePaths as $testPath) {
        if (file_exists(dirname($testPath))) {
            $path = $testPath;
            break;
        }
    }
    
    if (!$path) {
        $path = $possiblePaths[0]; // Default ke path pertama
    }
    
    error_log("updateFileContent: Saving to path: $path");
    
    // Cek permission folder
    $dir = dirname($path);
    if (!is_dir($dir)) {
        error_log("updateFileContent: Directory does not exist: $dir");
        if (!mkdir($dir, 0755, true)) {
            error_log("updateFileContent: Failed to create directory: $dir");
            return false;
        }
    }
    
    if (!is_writable($dir)) {
        error_log("updateFileContent: Directory not writable: $dir");
        chmod($dir, 0755);
    }
    
    // Backup file lama jika ada
    if (file_exists($path)) {
        $backupPath = $path . '.backup.' . time();
        if (!copy($path, $backupPath)) {
            error_log("updateFileContent: Failed to create backup");
        } else {
            error_log("updateFileContent: Backup created: $backupPath");
        }
    }
    
    // Write new content
    $result = file_put_contents($path, $content);
    
    if ($result === false) {
        error_log("updateFileContent: file_put_contents failed for: $path");
        return false;
    }
    
    error_log("updateFileContent: Successfully wrote $result bytes");
    
    // Update timestamp di database
    $stmt = mysqli_prepare($conn, "UPDATE files SET updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $fileId);
    mysqli_stmt_execute($stmt);
    
    return true;
}