<?php
require_once __DIR__ . '/../config/database.php';

define('UPLOAD_DIR', __DIR__ . '/../uploads/files/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024);

// ==================== FOLDER FUNCTIONS ====================

function getFolderById($id) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT * FROM folders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function getSubfolders($parentId) {
    global $conn;

    $sql = "
        SELECT f.*,
        (SELECT COUNT(*) FROM files WHERE folder_id = f.id) as file_count,
        (SELECT COUNT(*) FROM folders WHERE parent_id = f.id) as subfolder_count
        FROM folders f
        WHERE f.parent_id = ?
        ORDER BY f.name ASC
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $parentId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function getBreadcrumbs($folderId) {
    $crumbs = [];
    $currentId = $folderId;
    
    while ($currentId) {
        $folder = getFolderById($currentId);
        if (!$folder) break;
        array_unshift($crumbs, $folder);
        $currentId = $folder['parent_id'];
    }
    
    return $crumbs;
}

function createFolder($name, $parentId = 1) {
    global $conn;

    $name = trim($name);
    if (!$name) {
        return ['success' => false, 'error' => 'Nama kosong'];
    }

    // cek duplicate
    $stmt = mysqli_prepare($conn, "SELECT id FROM folders WHERE parent_id = ? AND name = ?");
    mysqli_stmt_bind_param($stmt, "is", $parentId, $name);
    mysqli_stmt_execute($stmt);

    if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
        return ['success' => false, 'error' => 'Folder sudah ada'];
    }

    $parent = getFolderById($parentId);
    $path = $parent['path'] . $name . '/';

    $stmt = mysqli_prepare($conn, "INSERT INTO folders (parent_id, name, path) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iss", $parentId, $name, $path);
    mysqli_stmt_execute($stmt);

    return ['success' => true, 'id' => mysqli_insert_id($conn)];
}

function renameFolder($id, $newName) {
    global $conn;

    $folder = getFolderById($id);
    if (!$folder || $id == 1) {
        return ['success' => false];
    }

    $parent = getFolderById($folder['parent_id']);

    $oldPath = $folder['path'];
    $newPath = $parent['path'] . $newName . '/';

    mysqli_begin_transaction($conn);

    // update folder
    $stmt = mysqli_prepare($conn, "UPDATE folders SET name=?, path=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $newName, $newPath, $id);
    mysqli_stmt_execute($stmt);

    // update children
    $like = $oldPath . '%';
    $stmt = mysqli_prepare($conn, "
        UPDATE folders 
        SET path = REPLACE(path, ?, ?) 
        WHERE path LIKE ?
    ");
    mysqli_stmt_bind_param($stmt, "sss", $oldPath, $newPath, $like);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);

    return ['success' => true];
}

function moveFolder($id, $targetFolderId) {
    global $conn;

    $current = getFolderById($id);
    $target = getFolderById($targetFolderId);

    if (!$current || !$target) {
        return ['success' => false];
    }

    if (strpos($target['path'], $current['path']) === 0) {
        return ['success' => false];
    }

    $oldPath = $current['path'];
    $newPath = $target['path'] . $current['name'] . '/';

    mysqli_begin_transaction($conn);

    $stmt = mysqli_prepare($conn, "UPDATE folders SET parent_id=?, path=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "isi", $targetFolderId, $newPath, $id);
    mysqli_stmt_execute($stmt);

    $like = $oldPath . '%';
    $stmt = mysqli_prepare($conn, "
        UPDATE folders 
        SET path = REPLACE(path, ?, ?) 
        WHERE path LIKE ?
    ");
    mysqli_stmt_bind_param($stmt, "sss", $oldPath, $newPath, $like);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);

    return ['success' => true];
}

function deleteFolder($id) {
    global $conn;

    if ($id == 1) {
        return ['success' => false, 'error' => 'Tidak bisa hapus root'];
    }

    // cek subfolder
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM folders WHERE parent_id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($result['total'] > 0) {
        return ['success' => false, 'error' => 'Masih ada subfolder'];
    }

    $folder = getFolderById($id);
    $parentId = $folder['parent_id'] ?? 1;

    // pindahin file
    $stmt = mysqli_prepare($conn, "UPDATE files SET folder_id=? WHERE folder_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $parentId, $id);
    mysqli_stmt_execute($stmt);

    // delete folder
    $stmt = mysqli_prepare($conn, "DELETE FROM folders WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    return ['success' => true];
}

function getFolderTree($parentId = 1) {
    $folders = getSubfolders($parentId);
    foreach ($folders as &$folder) {
        $folder['children'] = getFolderTree($folder['id']);
    }
    return $folders;
}

// ==================== FILE FUNCTIONS ====================

function getFilesByFolder($folderId, $type = null) {
    global $conn;

    $sql = "SELECT * FROM files WHERE folder_id=?";
    $types = "i";
    $params = [$folderId];

    if ($type && $type !== 'all') {
        $sql .= " AND mime_type LIKE ?";
        $types .= "s";
        $params[] = "%$type%";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $files = [];
    while ($file = mysqli_fetch_assoc($result)) {
        $file['type'] = detectFileType($file['mime_type'], $file['filename']);
        $file['extension'] = pathinfo($file['filename'], PATHINFO_EXTENSION);
        $file['size_formatted'] = formatFileSize($file['size']);
        $files[] = $file;
    }

    return $files;
}

function detectFileType($mime_type, $filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (strpos($mime_type, 'word') !== false || in_array($ext, ['doc', 'docx'])) {
        return 'word';
    }
    if (strpos($mime_type, 'excel') !== false || strpos($mime_type, 'spreadsheet') !== false || in_array($ext, ['xls', 'xlsx'])) {
        return 'excel';
    }
    if (strpos($mime_type, 'powerpoint') !== false || strpos($mime_type, 'presentation') !== false || in_array($ext, ['ppt', 'pptx'])) {
        return 'powerpoint';
    }
    if (strpos($mime_type, 'pdf') !== false || $ext === 'pdf') {
        return 'pdf';
    }
    if (strpos($mime_type, 'image/') === 0 || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        return 'image';
    }
    return 'other';
}

function uploadFile($file, $folderId = 1) {
    global $conn;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error: ' . $file['error']];
    }

    $allowed = ['jpg','jpeg','png','pdf','doc','docx','xls','xlsx','ppt','pptx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan. Allowed: ' . implode(', ', $allowed)];
    }

    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $originalName = preg_replace('/[^a-zA-Z0-9\s\-_.]/', '', $originalName);
    $originalName = trim($originalName);
    
    if (empty($originalName)) {
        $originalName = 'file';
    }

    $baseName = $originalName;
    $counter = 1;
    $filename = $baseName . '.' . $ext;
    
    while (file_exists(UPLOAD_DIR . $filename)) {
        $filename = $baseName . ' (' . $counter . ').' . $ext;
        $counter++;
    }

    $filepath = UPLOAD_DIR . $filename;

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'error' => 'Gagal memindahkan file'];
    }

    $mime = mime_content_type($filepath);
    $size = $file['size'];

    $stmt = mysqli_prepare($conn, "
        INSERT INTO files (folder_id, filename, filepath, mime_type, size, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");

    $pathDb = '/uploads/files/' . $filename;

    mysqli_stmt_bind_param($stmt, "isssi", $folderId, $filename, $pathDb, $mime, $size);
    mysqli_stmt_execute($stmt);

    return ['success' => true, 'filename' => $filename];
}

function renameFile($id, $newName) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT * FROM files WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $file = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$file) return ['success' => false];

    $ext = pathinfo($file['filename'], PATHINFO_EXTENSION);

    if (!str_ends_with(strtolower($newName), '.' . $ext)) {
        $newName .= '.' . $ext;
    }

    $stmt = mysqli_prepare($conn, "UPDATE files SET filename=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $newName, $id);
    mysqli_stmt_execute($stmt);

    return ['success' => true];
}

function moveFile($id, $targetFolderId) {
    global $conn;

    $stmt = mysqli_prepare($conn, "UPDATE files SET folder_id=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ii", $targetFolderId, $id);
    mysqli_stmt_execute($stmt);

    return ['success' => true];
}

function deleteFile($id) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT * FROM files WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $file = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$file) {
        return ['success' => false];
    }

    $path = __DIR__ . '/../../' . ltrim($file['filepath'], '/');

    if (file_exists($path)) {
        unlink($path);
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM files WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    return ['success' => true];
}

function downloadFile($id) {
    global $conn;

    if (!$id) {
        http_response_code(400);
        echo "ID tidak valid";
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM files WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $file = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$file) {
        http_response_code(404);
        echo "File tidak ditemukan di database";
        exit;
    }
    $filename = basename($file['filepath']); 
    $path = UPLOAD_DIR . $filename; 

    if (!file_exists($path)) {
        http_response_code(404);
        echo "File tidak ditemukan di server: " . $path . "<br>";
        echo "Database path: " . $file['filepath'] . "<br>";
        echo "Coba path alternatif...";
        
        $altPath = __DIR__ . '/../../uploads/files/' . $filename;
        if (file_exists($altPath)) {
            $path = $altPath;
        } else {
            exit;
        }
    }

    if (ob_get_level()) ob_end_clean();

    $displayName = basename($file['filename']);
    
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $file['mime_type']);
    header('Content-Disposition: attachment; filename="' . $displayName . '"');
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');
    header('Expires: 0');

    readfile($path);
    exit;
}

function formatFileSize($bytes) {
    if ($bytes === null) return '-';
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;
    while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
        $bytes /= 1024;
        $unitIndex++;
    }
    return round($bytes, 2) . ' ' . $units[$unitIndex];
}

function getFolderTreeAPI() {
    header('Content-Type: application/json');
    
    try {
        $tree = buildFolderTree(1); 
        echo json_encode(['success' => true, 'folders' => $tree]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

function buildFolderTree($parentId, $excludeId = null) {
    $folders = getSubfolders($parentId);
    $result = [];
    
    foreach ($folders as $folder) {
        if ($excludeId && $folder['id'] == $excludeId) continue;
        
        $node = [
            'id' => $folder['id'],
            'name' => $folder['name'],
            'children' => buildFolderTree($folder['id'], $excludeId)
        ];
        $result[] = $node;
    }
    
    return $result;
}

function downloadFolder($folderId) {
    $zip = new ZipArchive();
    $zipName = 'folder_' . $folderId . '.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipName;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        exit("Tidak bisa membuat ZIP");
    }

    addFolderToZip($folderId, $zip);

    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($zipPath));

    readfile($zipPath);
    unlink($zipPath);
    exit;
}

function addFolderToZip($folderId, $zip, $basePath = '') {
    $files = getFilesByFolder($folderId);
    foreach ($files as $file) {
        $path = $_SERVER['DOCUMENT_ROOT'] . $file['filepath'];
        if (file_exists($path)) {
            $zip->addFile($path, $basePath . $file['filename']);
        }
    }

    $folders = getSubfolders($folderId);
    foreach ($folders as $folder) {
        addFolderToZip($folder['id'], $zip, $basePath . $folder['name'] . '/');
    }
}