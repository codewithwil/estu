<?php
require_once '../functions/portofolio.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'get':
        echo json_encode(getPortfolios());
        break;

    case 'create':
        $title       = $_POST['title'];
        $category    = $_POST['category'];
        $client      = $_POST['client'];
        $year        = $_POST['year'];
        $description = $_POST['description'];
        $location   = $_POST['location'];
        $guests     = $_POST['guests'];
        $services   = $_POST['services'];
        $tags       = $_POST['tags'];
        $file        = $_FILES['image'] ?? null;
        if (createPortfolio($title, $category, $client, $year, $description, $location, $guests, $services, $tags, $file)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    break;

    case 'update':
        $id          = $_POST['id'];
        $title       = $_POST['title'];
        $category    = $_POST['category'];
        $client      = $_POST['client'];
        $year        = $_POST['year'];
        $description = $_POST['description'];
        $location   = $_POST['location'];
        $guests     = $_POST['guests'];
        $services   = $_POST['services'];
        $tags       = $_POST['tags'];
        $file        = $_FILES['image'] ?? null;
        updatePortfolio($id, $title, $category, $client, $year, $description, $location, $guests, $services, $tags, $file);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = $_POST['id'];

        deletePortfolio($id);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}