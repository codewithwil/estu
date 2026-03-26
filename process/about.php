<?php
require_once '../functions/about.php';

$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {

    case 'get':
        echo json_encode(getAbout());
        break;

    case 'save':
        $data = [
            'sectionLabel' => $_POST['sectionLabel'],
            'titleLine1'   => $_POST['titleLine1'],
            'titleLine2'   => $_POST['titleLine2'],
            'paragraph1'   => $_POST['paragraph1'],
            'paragraph2'   => $_POST['paragraph2'],
            'stats'        => json_decode($_POST['stats'], true)
        ];

        $file = $_FILES['image'] ?? null;

        if (saveAbout($data, $file)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
}