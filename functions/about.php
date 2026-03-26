<?php
require_once __DIR__ . '/../config/database.php';

define('ABOUT_UPLOAD_DIR', __DIR__ . '/../assets/images/about/');
define('ABOUT_UPLOAD_URL', '/estu/assets/images/about/');

function getAbout() {
    global $conn;

    $about = mysqli_fetch_assoc(
        mysqli_query($conn, "
            SELECT a.*, f.filepath 
            FROM about_sections a
            LEFT JOIN files f ON a.file_id = f.id
            LIMIT 1
        ")
    );

    $statsRes = mysqli_query($conn, "SELECT * FROM about_stats WHERE about_id=" . $about['id']);

    $stats = [];
    while ($row = mysqli_fetch_assoc($statsRes)) {
        $stats[] = $row;
    }

    return [
        'sectionLabel' => $about['section_label'],
        'titleLine1'   => $about['title_line1'],
        'titleLine2'   => $about['title_line2'],
        'paragraph1'   => $about['paragraph1'],
        'paragraph2'   => $about['paragraph2'],
        'image'        => $about['filepath'] ?? null,
        'stats'        => $stats
    ];
}

function uploadFileAndSave($file) {
    global $conn;

    if (!is_dir(ABOUT_UPLOAD_DIR)) {
        mkdir(ABOUT_UPLOAD_DIR, 0755, true);
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return null;

    if ($file['size'] > 5 * 1024 * 1024) return null;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = 'about_' . time() . '_' . uniqid() . '.' . $ext;

    $path = ABOUT_UPLOAD_DIR . $name;
    $url  = ABOUT_UPLOAD_URL . $name;

    if (move_uploaded_file($file['tmp_name'], $path)) {

        mysqli_query($conn, "
            INSERT INTO files (filename, filepath)
            VALUES ('$name', '$url')
        ");

        return mysqli_insert_id($conn); 
    }

    return null;
}

function saveAbout($data, $file) {
    global $conn;

    $sectionLabel   = mysqli_real_escape_string($conn, $data['sectionLabel']);
    $title1         = mysqli_real_escape_string($conn, $data['titleLine1']);
    $title2         = mysqli_real_escape_string($conn, $data['titleLine2']);
    $p1             = mysqli_real_escape_string($conn, $data['paragraph1']);
    $p2             = mysqli_real_escape_string($conn, $data['paragraph2']);

    $old        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_sections LIMIT 1"));
    $id         = $old['id'];
    $oldFile    = null;

    if ($old['file_id']) {
        $oldFile = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT * FROM files WHERE id=" . $old['file_id'])
        );
    }

    $fileId = $old['file_id'];

    if (!empty($file['tmp_name'])) {
        $newFileId = uploadFileAndSave($file);

        if ($newFileId) {

            if ($oldFile) {

                $oldPath = __DIR__ . '/../' . str_replace('/estu/', '', $oldFile['filepath']);

                if (file_exists($oldPath)) {
                    unlink($oldPath); 
                }

                mysqli_query($conn, "DELETE FROM files WHERE id=" . $oldFile['id']);
            }

            $fileId = $newFileId;
        }
    }

    mysqli_query($conn, "
        UPDATE about_sections SET
        section_label='$sectionLabel',
        title_line1='$title1',
        title_line2='$title2',
        paragraph1='$p1',
        paragraph2='$p2',
        file_id=" . ($fileId ? $fileId : "NULL") . "
        WHERE id=$id
    ");
    
    mysqli_query($conn, "DELETE FROM about_stats WHERE about_id=$id");

    foreach ($data['stats'] as $stat) {
        $num = mysqli_real_escape_string($conn, $stat['number']);
        $suf = mysqli_real_escape_string($conn, $stat['suffix']);
        $lab = mysqli_real_escape_string($conn, $stat['label']);

        mysqli_query($conn, "
            INSERT INTO about_stats (about_id, number, suffix, label)
            VALUES ($id, '$num', '$suf', '$lab')
        ");
    }

    return true;
}

