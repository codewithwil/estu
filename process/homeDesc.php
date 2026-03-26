<?php
require_once '../functions/homeDesc.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'get':
        echo json_encode(getHomeContent());
        break;

    case 'save':
        $raw = file_get_contents("php://input");
        $input = json_decode($raw, true);

        if (!$input) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON'
            ]);
            exit;
        }

        $topLabel      = $input['topLabel'] ?? '';
        $mainTitle     = $input['mainTitle'] ?? '';
        $boldSubtitle  = $input['boldSubtitle'] ?? '';
        $lightSubtitle = $input['lightSubtitle'] ?? '';
        $ctaText       = $input['ctaText'] ?? '';

        $result = saveHomeContent(
            $topLabel,
            $mainTitle,
            $boldSubtitle,
            $lightSubtitle,
            $ctaText
        );

        echo json_encode([
            'success' => $result
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}