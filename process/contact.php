<?php
require_once '../functions/contact.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'get':
        echo json_encode(getContact());
    break;

    case 'update':
        $data = [
            'id' => $_POST['id'],
            'title_line' => $_POST['titleLine'],
            'section_desc' => $_POST['sectionDesc'],
            'whatsapp_number' => $_POST['whatsAppNumber'],
            'whatsapp_note' => $_POST['whatsAppNote'],
            'email' => $_POST['email'],
            'operating_hours' => $_POST['operatingHours'],
            'location' => $_POST['location'],
            'whatsapp_button_text' => $_POST['whatsAppButtonText'],
            'why_quote' => $_POST['whyQuote'],
            'brand_name' => $_POST['brandName'],
            'brand_tagline' => $_POST['brandTagline'],
            'copyright_text' => $_POST['copyrightText'],
            'why_choose' => json_decode($_POST['whyChoose'], true)
        ];

        updateContact($data);

        echo json_encode(['success' => true]);
    break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}