<?php
require_once '../functions/services.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'get':
        echo json_encode(getServices());
        break;

    case 'save':
        $input = json_decode(file_get_contents('php://input'), true);

        if (saveServices($input)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}