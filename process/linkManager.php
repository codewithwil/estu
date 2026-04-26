<?php
require_once __DIR__ . '/../functions/linkManager.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

function res($data = [], $success = true) {
    echo json_encode([
        'success' => $success,
        'data' => $data
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {

    switch ($action) {

        case 'list':
            res(getAllLinks($_GET['category_id'] ?? null));
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
            if (empty($input['name'])) {
                throw new Exception('Nama kategori wajib diisi');
            }

            createCategory($input);
            res(['message' => 'Kategori berhasil dibuat']);
            break;

        default:
            throw new Exception('Action tidak valid');
    }

} catch (Exception $e) {
    res(['error' => $e->getMessage()], false);
}