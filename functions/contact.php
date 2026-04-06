<?php
require_once __DIR__ . '/../config/database.php';

function getContact() {
    global $conn;

    $contact = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM contact LIMIT 1")
    );

    if (!$contact) return null;

    $contactId = $contact['id'];

    $whyResult = mysqli_query($conn, "
        SELECT content FROM contact_why 
        WHERE contact_id = $contactId 
        ORDER BY sort_order ASC
    ");

    $whyList = [];
    while ($row = mysqli_fetch_assoc($whyResult)) {
        $whyList[] = $row['content'];
    }

    $contact['why_choose'] = $whyList;

    return $contact;
}

function updateContact($data) {
    global $conn;

    $id = (int)$data['id'];

    $query = "
        UPDATE contact SET
        title_line = '".mysqli_real_escape_string($conn, $data['title_line'])."',
        section_desc = '".mysqli_real_escape_string($conn, $data['section_desc'])."',
        whatsapp_number = '".mysqli_real_escape_string($conn, $data['whatsapp_number'])."',
        whatsapp_note = '".mysqli_real_escape_string($conn, $data['whatsapp_note'])."',
        email = '".mysqli_real_escape_string($conn, $data['email'])."',
        operating_hours = '".mysqli_real_escape_string($conn, $data['operating_hours'])."',
        location = '".mysqli_real_escape_string($conn, $data['location'])."',
        whatsapp_button_text = '".mysqli_real_escape_string($conn, $data['whatsapp_button_text'])."',
        why_quote = '".mysqli_real_escape_string($conn, $data['why_quote'])."',
        brand_name = '".mysqli_real_escape_string($conn, $data['brand_name'])."',
        brand_tagline = '".mysqli_real_escape_string($conn, $data['brand_tagline'])."',
        copyright_text = '".mysqli_real_escape_string($conn, $data['copyright_text'])."'
        WHERE id = $id
    ";

    mysqli_query($conn, $query);

    // ===== UPDATE WHY CHOOSE =====
    mysqli_query($conn, "DELETE FROM contact_why WHERE contact_id = $id");

    if (!empty($data['why_choose'])) {
        foreach ($data['why_choose'] as $index => $item) {
            $content = mysqli_real_escape_string($conn, $item);
            $sort = $index + 1;

            mysqli_query($conn, "
                INSERT INTO contact_why (contact_id, content, sort_order)
                VALUES ($id, '$content', $sort)
            ");
        }
    }

    return true;
}