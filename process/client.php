<?php
require_once '../functions/client.php';

$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {

    case 'get':
        echo json_encode(getClients());
        break;

    case 'create':
        $name  = $_POST['name'];
        $since = $_POST['since'];
        $file  = $_FILES['logo'] ?? null;

        if (createClient($name, $file, $since)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    break;

    case 'update':
        $id    = $_POST['id'];
        $name  = $_POST['name'];
        $since = $_POST['since'];

        updateClient($id, $name, $since);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = $_POST['id'];

        deleteClient($id);
        echo json_encode(['success' => true]);
        break;
}