<?php
require_once __DIR__ . '/../config/database.php';

define('SLIDER_UPLOAD_DIR', __DIR__ . '/../assets/images/hero/');
define('SLIDER_UPLOAD_URL', '/estu/assets/images/hero/');


// =========================
// GET DATA (JOIN)
// =========================
function getHomeSliders() {
    global $conn;

    $query = "SELECT hs.id, hs.title, hs.position, hs.created_at,
                     f.filename, f.filepath, f.size
              FROM home_images hs
              JOIN files f ON hs.file_id = f.id
              ORDER BY hs.position ASC";

    $result = mysqli_query($conn, $query);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}


// =========================
// UPLOAD FILE
// =========================
function uploadSliderImage($file) {

    if (!is_dir(SLIDER_UPLOAD_DIR)) {
        mkdir(SLIDER_UPLOAD_DIR, 0755, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) return false;
    if ($file['size'] > $maxSize) return false;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'slider_' . time() . '_' . uniqid() . '.' . $ext;

    $destination = SLIDER_UPLOAD_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }

    return false;
}


// =========================
// CREATE
// =========================
function createHomeSlider($title, $file) {
    global $conn;

    if (empty($file['tmp_name'])) return false;

    $uploaded = uploadSliderImage($file);
    if (!$uploaded) return false;

    $filepath = SLIDER_UPLOAD_URL . $uploaded;
    $mime     = $file['type'];
    $size     = $file['size'];

    // insert files
    $queryFile = "INSERT INTO files (filename, filepath, mime_type, size)
                  VALUES ('$uploaded', '$filepath', '$mime', $size)";
    mysqli_query($conn, $queryFile);

    $file_id = mysqli_insert_id($conn);

    // ambil posisi terakhir
    $res = mysqli_query($conn, "SELECT MAX(position) as max_pos FROM home_images");
    $row = mysqli_fetch_assoc($res);
    $position = $row['max_pos'] ? $row['max_pos'] + 1 : 1;

    $title = mysqli_real_escape_string($conn, $title);

    // insert slider
    $querySlider = "INSERT INTO home_images (file_id, title, position)
                    VALUES ($file_id, '$title', $position)";

    return mysqli_query($conn, $querySlider);
}


// =========================
// DELETE
// =========================
function deleteHomeSlider($id) {
    global $conn;

    $id = (int)$id;

    $res = mysqli_query($conn, "
        SELECT f.id, f.filename
        FROM home_images hs
        JOIN files f ON hs.file_id = f.id
        WHERE hs.id = $id
    ");

    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $path = SLIDER_UPLOAD_DIR . $row['filename'];

        if (file_exists($path)) {
            unlink($path);
        }

        mysqli_query($conn, "DELETE FROM home_images WHERE id = $id");
        mysqli_query($conn, "DELETE FROM files WHERE id = " . $row['id']);
    }

    return true;
}


// =========================
// UPDATE (REPLACE IMAGE)
// =========================
function updateHomeSlider($id, $title, $newFile = null) {
    global $conn;

    $id = (int)$id;
    $title = mysqli_real_escape_string($conn, $title);

    $fileUpdate = '';

    if (!empty($newFile['tmp_name'])) {

        $uploaded = uploadSliderImage($newFile);
        if ($uploaded) {

            $res = mysqli_query($conn, "
                SELECT f.id, f.filename
                FROM home_images hs
                JOIN files f ON hs.file_id = f.id
                WHERE hs.id = $id
            ");

            $old = mysqli_fetch_assoc($res);

            if ($old && !empty($old['filename'])) {
                $oldPath = SLIDER_UPLOAD_DIR . $old['filename'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $filepath = SLIDER_UPLOAD_URL . $uploaded;
            $mime     = $newFile['type'];
            $size     = $newFile['size'];

            mysqli_query($conn, "
                UPDATE files 
                SET filename='$uploaded', filepath='$filepath', mime_type='$mime', size=$size
                WHERE id = " . $old['id']
            );
        }
    }

    return mysqli_query($conn, "
        UPDATE home_images 
        SET title='$title'
        WHERE id=$id
    ");
}


// =========================
// REORDER
// =========================
function reorderHomeSlider($orders) {
    global $conn;

    foreach ($orders as $id => $pos) {
        $id = (int)$id;
        $pos = (int)$pos;

        mysqli_query($conn, "
            UPDATE home_images 
            SET position=$pos 
            WHERE id=$id
        ");
    }

    return true;
}