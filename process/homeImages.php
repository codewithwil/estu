<?php
require_once '../functions/homeImages.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$input = json_decode(file_get_contents("php://input"), true);
if (!$action && isset($input['action'])) {
    $action = $input['action'];
    $_POST = $input;
}

switch ($action) {

    case 'get':
        echo json_encode(getHomeSliders());
        break;

    case 'create':
        $title = $_POST['title'] ?? '';
        $file  = $_FILES['image'] ?? null;

        echo json_encode([
            'success' => createHomeSlider($title, $file)
        ]);
        break;

    case 'delete':
        deleteHomeSlider($_POST['id']);
        echo json_encode(['success' => true]);
        break;

    case 'reorder':
        $orders = $input['orders'] ?? [];
        reorderHomeSlider($orders);
        echo json_encode(['success' => true]);
        break;
}