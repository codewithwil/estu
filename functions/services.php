<?php
require_once __DIR__ . '/../config/database.php';

function getServices() {
    global $conn;

    $section = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM services_section LIMIT 1")
    );

    if (!$section) {
        return null;
    }

    $itemsRes = mysqli_query($conn, "
        SELECT * FROM services_items 
        WHERE section_id = {$section['id']}
        ORDER BY sort_order ASC
    ");

    $services = [];
    while ($row = mysqli_fetch_assoc($itemsRes)) {
        $services[] = [
            'id' => (int)$row['id'],
            'icon' => $row['icon'],
            'title' => $row['title'],
            'description' => $row['description'],
            'isWide' => (bool)$row['is_wide']
        ];
    }

    return [
        'titleLine1' => $section['title_line1'],
        'titleLine2' => $section['title_line2'],
        'sectionDesc' => $section['section_desc'],
        'services' => $services
    ];
}

function saveServices($data) {
    global $conn;

    mysqli_begin_transaction($conn);

    try {

        $title1 = mysqli_real_escape_string($conn, $data['titleLine1']);
        $title2 = mysqli_real_escape_string($conn, $data['titleLine2']);
        $desc   = mysqli_real_escape_string($conn, $data['sectionDesc']);
        $old    = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT id FROM services_section LIMIT 1")
        );

        if ($old) {
            $sectionId = $old['id'];

            mysqli_query($conn, "
                UPDATE services_section SET
                title_line1='$title1',
                title_line2='$title2',
                section_desc='$desc'
                WHERE id=$sectionId
            ");

            mysqli_query($conn, "DELETE FROM services_items WHERE section_id=$sectionId");

        } else {
            mysqli_query($conn, "
                INSERT INTO services_section (title_line1, title_line2, section_desc)
                VALUES ('$title1', '$title2', '$desc')
            ");

            $sectionId = mysqli_insert_id($conn);
        }

        $order = 1;
        foreach ($data['services'] as $service) {

            $icon = mysqli_real_escape_string($conn, $service['icon']);
            $title = mysqli_real_escape_string($conn, $service['title']);
            $descItem = mysqli_real_escape_string($conn, $service['description']);
            $isWide = !empty($service['isWide']) ? 1 : 0;

            mysqli_query($conn, "
                INSERT INTO services_items
                (section_id, icon, title, description, is_wide, sort_order)
                VALUES
                ($sectionId, '$icon', '$title', '$descItem', $isWide, $order)
            ");

            $order++;
        }

        mysqli_commit($conn);
        return true;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}