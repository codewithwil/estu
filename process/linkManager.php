<?php
require_once __DIR__ . '/../functions/linkManager.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

function res($data = [], $success = true) {
    echo json_encode(['success' => $success, 'data' => $data]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($action) {

        // --- FOLDER ACTIONS ---
        case 'listFolders':
            $parentId = $_GET['parent_id'] ?? 1;
            res(getLinkSubfolders($parentId));
            break;

        case 'getFolderTree':
            res(getLinkFolderTree(1, $_GET['exclude'] ?? null));
            break;

        case 'createFolder':
            $parentId = !empty($input['parent_id']) ? intval($input['parent_id']) : null;

            $result = createLinkFolder(
                $input['name'],
                $parentId,
                $input['icon'] ?? 'fa-folder',
                $input['color'] ?? '#eab308'
            );
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'Folder berhasil dibuat', 'id' => $result['id']]);
            break;

        case 'renameFolder':
            if (empty($input['id']) || empty($input['name'])) throw new Exception('Data tidak lengkap');
            $result = renameLinkFolder($input['id'], $input['name']);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'Folder berhasil direname']);
            break;

        case 'moveFolder':
            if (empty($input['id']) || !isset($input['target_id'])) throw new Exception('Data tidak lengkap');
            $result = moveLinkFolder($input['id'], $input['target_id']);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'Folder berhasil dipindah']);
            break;

        case 'deleteFolder':
            if (empty($input['id'])) throw new Exception('ID folder wajib diisi');
            $result = deleteLinkFolder($input['id']);
            if (!$result['success']) throw new Exception($result['error']);
            res(['message' => 'Folder berhasil dihapus']);
            break;

        // --- LINK ACTIONS ---
        case 'list':
            $folderId = $_GET['folder_id'] ?? ($_GET['category_id'] ?? 1);
            $search = $_GET['search'] ?? '';
            res(getLinksByFolder($folderId, $search));
            break;

        case 'get':
            $link = getLinkById($_GET['id'] ?? 0);
            if (!$link) throw new Exception('Link tidak ditemukan');
            res($link);
            break;

        case 'create':
            if (empty($input['title']) || empty($input['url'])) {
                throw new Exception('Judul dan URL wajib diisi');
            }
            createLink($input);
            res(['message' => 'Link berhasil dibuat']);
            break;

        case 'update':
            if (empty($input['id'])) throw new Exception('ID link wajib diisi');
            if (empty($input['title']) || empty($input['url'])) {
                throw new Exception('Judul dan URL wajib diisi');
            }
            updateLink($input['id'], $input);
            res(['message' => 'Link berhasil diupdate']);
            break;

        case 'moveLink':
            if (empty($input['id']) || !isset($input['target_folder_id'])) throw new Exception('Data tidak lengkap');
            moveLink($input['id'], $input['target_folder_id']);
            res(['message' => 'Link berhasil dipindah']);
            break;

        case 'delete':
            if (empty($input['id'])) throw new Exception('ID link wajib diisi');
            deleteLink($input['id']);
            res(['message' => 'Link berhasil dihapus']);
            break;

        case 'click':
            $id = $_GET['id'] ?? 0;
            if (!$id) throw new Exception('ID link wajib diisi');
            incrementClickCount($id);
            res(['message' => 'Click updated']);
            break;

        case 'createCategory':
            if (empty($input['name'])) throw new Exception('Nama kategori wajib diisi');
            createCategory($input);
            res(['message' => 'Kategori berhasil dibuat']);
            break;

        default:
            throw new Exception('Action tidak valid: ' . $action);
    }

} catch (Exception $e) {
    res(['error' => $e->getMessage()], false);
}