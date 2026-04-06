<?php
require_once __DIR__ . '/../config/database.php';

define('PORTFOLIO_UPLOAD_DIR', __DIR__ . '/../assets/images/portfolio/');
define('PORTFOLIO_UPLOAD_URL', '/estu/assets/images/portfolio/');

function getPortfolios() {
    global $conn;

    $query = "
        SELECT p.*, f.filename, f.filepath 
        FROM portfolios p
        LEFT JOIN files f ON p.file_id = f.id
        ORDER BY p.created_at DESC
    ";

    $result = mysqli_query($conn, $query);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

function uploadFile($file) {
    global $conn;

    $uploadDir = __DIR__ . '/../assets/images/portfolio/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'portofolio_' . time() . '_' . uniqid() . '.' . $ext;
    $path = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {

        $filenameSafe = mysqli_real_escape_string($conn, $filename);

        mysqli_query($conn, "
            INSERT INTO files (filename, filepath)
            VALUES ('$filenameSafe', '/estu/assets/images/portfolio/$filenameSafe')
        ");

        return mysqli_insert_id($conn); 
    }

    return null;
}



function createPortfolio( 
    $title,
    $category,
    $client,
    $year,
    $description,
    $location,
    $guests,
    $services,
    $tags,
    $file
){
    global $conn;

    if (empty($file['tmp_name'])) {
        die('Gambar wajib diupload');
    }

    $fileId = uploadFile($file);

    if (!$fileId) {
        die('Upload gagal');
    }

    // sanitize
    $title = mysqli_real_escape_string($conn, $title);
    $category = mysqli_real_escape_string($conn, $category);
    $client = mysqli_real_escape_string($conn, $client);
    $year = mysqli_real_escape_string($conn, $year);
    $description = mysqli_real_escape_string($conn, $description);
    $location = mysqli_real_escape_string($conn, $location);
    $guests = mysqli_real_escape_string($conn, $guests);
    $services = mysqli_real_escape_string($conn, $services);
    $tags = mysqli_real_escape_string($conn, $tags);

    $query = "INSERT INTO portfolios 
    (title, category, client, year, description, location, guests, services, tags, file_id)
    VALUES 
    ('$title','$category','$client','$year','$description','$location','$guests','$services','$tags',$fileId)";

    return mysqli_query($conn, $query);
}
function updatePortfolio($id, $title, $category, $client, $year, $description, $location, $guests, $services, $tags, $file) {
    global $conn;

    $id = (int)$id;

    $title       = mysqli_real_escape_string($conn, $title);
    $category    = mysqli_real_escape_string($conn, $category);
    $client      = mysqli_real_escape_string($conn, $client);
    $year        = mysqli_real_escape_string($conn, $year);
    $description = mysqli_real_escape_string($conn, $description);
    $location    = mysqli_real_escape_string($conn, $location);
    $guests      = mysqli_real_escape_string($conn, $guests);
    $services    = mysqli_real_escape_string($conn, $services);
    $tags        = mysqli_real_escape_string($conn, $tags);

    $fileUpdate = '';

    if (!empty($file['tmp_name'])) {

        $old = mysqli_fetch_assoc(
            mysqli_query($conn, "
                SELECT f.id, f.filepath 
                FROM portfolios p
                LEFT JOIN files f ON p.file_id = f.id
                WHERE p.id = $id
            ")
        );

        $newFileId = uploadFile($file);

        if ($newFileId) {
            $fileUpdate = ", file_id = $newFileId";
            if ($old && !empty($old['filepath'])) {
                $oldPath = __DIR__ . '/..' . str_replace('/estu', '', $old['filepath']);

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }

                mysqli_query($conn, "DELETE FROM files WHERE id=" . (int)$old['id']);
            }
        }
    }

    $query = "UPDATE portfolios 
              SET title='$title',
                  category='$category',
                  client='$client',
                  year='$year',
                  description='$description',
                  location='$location',
                  guests='$guests',
                  services='$services',
                  tags='$tags'
                  $fileUpdate
              WHERE id=$id";

    return mysqli_query($conn, $query);
}

function deletePortfolio($id) {
    global $conn;

    $id = (int)$id;

    $data = mysqli_fetch_assoc(
        mysqli_query($conn, "
            SELECT f.id, filepath 
            FROM portfolios p
            LEFT JOIN files f ON p.file_id = f.id
            WHERE p.id=$id
        ")
    );

    // 1. Hapus portfolio dulu
    mysqli_query($conn, "DELETE FROM portfolios WHERE id=$id");

    // 2. Baru hapus file
    if ($data && !empty($data['filepath'])) {
        $filePath = __DIR__ . '/..' . str_replace('/estu', '', $data['filepath']);

        if (file_exists($filePath)) unlink($filePath);

        mysqli_query($conn, "DELETE FROM files WHERE id=" . (int)$data['id']);
    }

    return true;
}