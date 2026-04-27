<?php
require_once __DIR__ . '/../config/database.php';

// ==================== FOLDER FUNCTIONS ====================

function getLinkFolderById($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM link_folders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function getLinkSubfolders($parentId) {
    global $conn;
    $sql = "
        SELECT f.*,
        (SELECT COUNT(*) FROM links WHERE folder_id = f.id) as link_count,
        (SELECT COUNT(*) FROM link_folders WHERE parent_id = f.id) as subfolder_count
        FROM link_folders f
        WHERE f.parent_id = ?
        ORDER BY f.name ASC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $parentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
    return $data;
}

function getLinkFolderBreadcrumbs($folderId) {
    $crumbs = [];
    $currentId = $folderId;
    while ($currentId) {
        $folder = getLinkFolderById($currentId);
        if (!$folder) break;
        array_unshift($crumbs, $folder);
        $currentId = $folder['parent_id'];
    }
    return $crumbs;
}

function createLinkFolder($name, $parentId = 1, $icon = 'fa-folder', $color = '#eab308') {
    global $conn;

    $name = trim($name);
    if (!$name) return ['success' => false, 'error' => 'Nama folder kosong'];

    $parent = getLinkFolderById($parentId);
    if (!$parent) {
        return ['success' => false, 'error' => 'Parent folder tidak valid'];
    }

    $stmt = mysqli_prepare($conn, "SELECT id FROM link_folders WHERE parent_id = ? AND name = ?");
    mysqli_stmt_bind_param($stmt, "is", $parentId, $name);
    mysqli_stmt_execute($stmt);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
        return ['success' => false, 'error' => 'Folder sudah ada'];
    }

    $path = ($parent['path'] ?? '/') . $name . '/';
    $stmt = mysqli_prepare($conn, "
        INSERT INTO link_folders (parent_id, name, icon, color, path)
        VALUES (?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "issss", $parentId, $name, $icon, $color, $path);
    mysqli_stmt_execute($stmt);

    return ['success' => true, 'id' => mysqli_insert_id($conn)];
}

function renameLinkFolder($id, $newName) {
    global $conn;
    $folder = getLinkFolderById($id);
    if (!$folder || $id == 1) return ['success' => false, 'error' => 'Tidak valid'];

    $parent = getLinkFolderById($folder['parent_id']);
    $oldPath = $folder['path'];
    $newPath = ($parent['path'] ?? '/') . $newName . '/';

    mysqli_begin_transaction($conn);
    $stmt = mysqli_prepare($conn, "UPDATE link_folders SET name=?, path=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $newName, $newPath, $id);
    mysqli_stmt_execute($stmt);

    $like = $oldPath . '%';
    $stmt = mysqli_prepare($conn, "UPDATE link_folders SET path = REPLACE(path, ?, ?) WHERE path LIKE ?");
    mysqli_stmt_bind_param($stmt, "sss", $oldPath, $newPath, $like);
    mysqli_stmt_execute($stmt);
    mysqli_commit($conn);

    return ['success' => true];
}

function moveLinkFolder($id, $targetFolderId) {
    global $conn;
    $current = getLinkFolderById($id);
    $target = getLinkFolderById($targetFolderId);
    if (!$current || !$target) return ['success' => false, 'error' => 'Folder tidak ditemukan'];
    if (strpos($target['path'], $current['path']) === 0) {
        return ['success' => false, 'error' => 'Tidak bisa pindah ke subfolder sendiri'];
    }

    $oldPath = $current['path'];
    $newPath = $target['path'] . $current['name'] . '/';

    mysqli_begin_transaction($conn);
    $stmt = mysqli_prepare($conn, "UPDATE link_folders SET parent_id=?, path=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "isi", $targetFolderId, $newPath, $id);
    mysqli_stmt_execute($stmt);

    $like = $oldPath . '%';
    $stmt = mysqli_prepare($conn, "UPDATE link_folders SET path = REPLACE(path, ?, ?) WHERE path LIKE ?");
    mysqli_stmt_bind_param($stmt, "sss", $oldPath, $newPath, $like);
    mysqli_stmt_execute($stmt);
    mysqli_commit($conn);

    return ['success' => true];
}

function deleteLinkFolder($id) {
    global $conn;
    if ($id == 1) return ['success' => false, 'error' => 'Tidak bisa hapus root'];

    // Cek subfolder
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM link_folders WHERE parent_id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($result['total'] > 0) return ['success' => false, 'error' => 'Masih ada subfolder'];

    // Pindahin links ke parent
    $folder = getLinkFolderById($id);
    $parentId = $folder['parent_id'] ?? 1;
    $stmt = mysqli_prepare($conn, "UPDATE links SET folder_id=? WHERE folder_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $parentId, $id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM link_folders WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    return ['success' => true];
}

function getLinkFolderTree($parentId = 1, $excludeId = null) {
    $folders = getLinkSubfolders($parentId);
    $result = [];
    foreach ($folders as $folder) {
        if ($excludeId && $folder['id'] == $excludeId) continue;
        $node = [
            'id' => $folder['id'],
            'name' => $folder['name'],
            'icon' => $folder['icon'],
            'color' => $folder['color'],
            'children' => getLinkFolderTree($folder['id'], $excludeId)
        ];
        $result[] = $node;
    }
    return $result;
}

// ==================== LINK FUNCTIONS ====================

function getLinkCategories() {
    global $conn;
    $sql = "SELECT c.*, COUNT(l.id) as link_count 
            FROM link_categories c 
            LEFT JOIN links l ON c.id = l.category_id 
            GROUP BY c.id ORDER BY c.name";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getLinksByFolder($folderId, $search = '') {
    global $conn;
    $folderId = intval($folderId);
    $sql = "
        SELECT l.*, c.name as category_name, c.icon as category_icon, c.color as category_color
        FROM links l
        LEFT JOIN link_categories c ON l.category_id = c.id
        WHERE l.folder_id = ?
    ";
    if ($search) {
        $sql .= " AND (l.title LIKE ? OR l.url LIKE ? OR l.description LIKE ?)";
    }
    $sql .= " ORDER BY l.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($search) {
        $like = "%$search%";
        mysqli_stmt_bind_param($stmt, "isss", $folderId, $like, $like, $like);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $folderId);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function getLinkById($id) {
    global $conn;
    $id = intval($id);
    $sql = "SELECT l.*, c.name as category_name 
            FROM links l
            LEFT JOIN link_categories c ON l.category_id = c.id
            WHERE l.id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function createLink($data) {
    global $conn;
    $title = mysqli_real_escape_string($conn, $data['title']);
    $url   = mysqli_real_escape_string($conn, $data['url']);
    $folderId = !empty($data['folder_id']) ? intval($data['folder_id']) : 1;
    $cat   = !empty($data['category_id']) ? intval($data['category_id']) : "NULL";
    $desc  = !empty($data['description']) ? "'" . mysqli_real_escape_string($conn, $data['description']) . "'" : "NULL";

    $sql = "INSERT INTO links (folder_id, title, url, category_id, description, created_at)
            VALUES ($folderId, '$title', '$url', $cat, $desc, NOW())";
    return mysqli_query($conn, $sql);
}

function updateLink($id, $data) {
    global $conn;
    $id    = intval($id);
    $title = mysqli_real_escape_string($conn, $data['title']);
    $url   = mysqli_real_escape_string($conn, $data['url']);
    $folderId = !empty($data['folder_id']) ? intval($data['folder_id']) : 1;
    $cat   = !empty($data['category_id']) ? intval($data['category_id']) : "NULL";
    $desc  = !empty($data['description']) ? "'" . mysqli_real_escape_string($conn, $data['description']) . "'" : "NULL";

    $sql = "UPDATE links 
            SET folder_id=$folderId, title='$title', url='$url', category_id=$cat, description=$desc, updated_at=NOW()
            WHERE id=$id";
    return mysqli_query($conn, $sql);
}

function moveLink($id, $targetFolderId) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE links SET folder_id=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ii", $targetFolderId, $id);
    mysqli_stmt_execute($stmt);
    return ['success' => true];
}

function deleteLink($id) {
    global $conn;
    $id = intval($id);
    return mysqli_query($conn, "DELETE FROM links WHERE id=$id");
}

function incrementClickCount($id) {
    global $conn;
    $id = intval($id);
    return mysqli_query($conn, "UPDATE links SET click_count = click_count + 1 WHERE id=$id");
}

function createCategory($data) {
    global $conn;
    $name  = mysqli_real_escape_string($conn, $data['name']);
    $icon  = mysqli_real_escape_string($conn, $data['icon'] ?? 'fa-tag');
    $color = mysqli_real_escape_string($conn, $data['color'] ?? '#6366f1');
    $sql = "INSERT INTO link_categories (name, icon, color) VALUES ('$name', '$icon', '$color')";
    return mysqli_query($conn, $sql);
}

function getFavicon($url) {
    $domain = parse_url($url, PHP_URL_HOST);
    return $domain ? "https://www.google.com/s2/favicons?domain=$domain&sz=64" : null;
}

function getCategoryName($id, $categories) {
    foreach ($categories as $c) if ($c['id'] == $id) return $c['name'];
    return 'Umum';
}