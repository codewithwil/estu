<?php
require_once __DIR__ . '/../config/database.php';

define('CLIENTS_UPLOAD_DIR', __DIR__ . '/../assets/images/client/');
define('CLIENTS_UPLOAD_URL', '/estu/assets/images/client/');

function getClients() {
    global $conn;

    $result = mysqli_query($conn, "SELECT * FROM clients ORDER BY id DESC");

    $clients = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $clients[] = $row;
    }

    return $clients;
}

function uploadClientLogo($file) {
    if (!is_dir(CLIENTS_UPLOAD_DIR)) {
        mkdir(CLIENTS_UPLOAD_DIR, 0755, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        return false; 
    }

    if ($file['size'] > $maxSize) {
        return false; 
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'client_' . time() . '_' . uniqid() . '.' . $extension;
    
    $destination = CLIENTS_UPLOAD_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename; 
    }

    return false;
}

function createClient($name, $logoFile, $since) {
    global $conn;

    $logoFilename = '';
    if (!empty($logoFile['tmp_name'])) {
        $uploaded = uploadClientLogo($logoFile);
        if ($uploaded) {
            $logoFilename = $uploaded;
        }
    }

    $name  = mysqli_real_escape_string($conn, $name);
    $since = mysqli_real_escape_string($conn, $since);

    $query = "INSERT INTO clients (name, logo, since) 
              VALUES ('$name', '$logoFilename', '$since')";

    return mysqli_query($conn, $query);
}

function updateClient($id, $name, $since, $newLogoFile = null) {
    global $conn;

    $id    = (int)$id;
    $name  = mysqli_real_escape_string($conn, $name);
    $since = mysqli_real_escape_string($conn, $since);

    $logoUpdate = '';
    if (!empty($newLogoFile['tmp_name'])) {
        $uploaded = uploadClientLogo($newLogoFile);
        if ($uploaded) {
            $oldClient = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT logo FROM clients WHERE id=$id")
            );
            if ($oldClient && !empty($oldClient['logo'])) {
                $oldPath = CLIENTS_UPLOAD_DIR . $oldClient['logo'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $logoFilename = mysqli_real_escape_string($conn, $uploaded);
            $logoUpdate = ", logo='$logoFilename'";
        }
    }

    $query = "UPDATE clients 
              SET name='$name', since='$since'$logoUpdate
              WHERE id=$id";

    return mysqli_query($conn, $query);
}

function deleteClient($id) {
    global $conn;

    $id = (int)$id;

    $client = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT logo FROM clients WHERE id=$id")
    );
    
    if ($client && !empty($client['logo'])) {
        $logoPath = CLIENTS_UPLOAD_DIR . $client['logo'];
        if (file_exists($logoPath)) {
            unlink($logoPath);
        }
    }

    return mysqli_query($conn, "DELETE FROM clients WHERE id=$id");
}

function getClientLogoUrl($filename) {
    if (empty($filename)) {
        return '/assets/images/default-client.png'; 
    }
    return CLIENTS_UPLOAD_URL . $filename;
}
?>