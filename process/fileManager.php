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
        case 'files/callback':
            handleOnlyOfficeCallback();
            exit;

        default:
            throw new Exception('Endpoint not found: ' . $path);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}