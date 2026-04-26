<?php
require_once __DIR__ . '/../config/database.php';

function getLinkCategories() {
    global $conn;

    $sql = "
        SELECT c.*, COUNT(l.id) as link_count 
        FROM link_categories c 
        LEFT JOIN links l ON c.id = l.category_id 
        GROUP BY c.id 
        ORDER BY c.name
    ";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getAllLinks($categoryId = null) {
    global $conn;

    $sql = "
        SELECT l.*, c.name as category_name, c.icon as category_icon, c.color as category_color
        FROM links l
        LEFT JOIN link_categories c ON l.category_id = c.id
        WHERE 1=1
    ";

    if ($categoryId) {
        $sql .= " AND l.category_id = " . intval($categoryId);
    }

    $sql .= " ORDER BY l.created_at DESC";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function createCategory($data) {
    global $conn;

    $name  = mysqli_real_escape_string($conn, $data['name']);
    $icon  = mysqli_real_escape_string($conn, $data['icon']);
    $color = mysqli_real_escape_string($conn, $data['color']);

    $sql = "
        INSERT INTO link_categories (name, icon, color)
        VALUES ('$name', '$icon', '$color')
    ";

    return mysqli_query($conn, $sql);
}

function getLinkById($id) {
    global $conn;

    $id = intval($id);
    $sql = "
        SELECT l.*, c.name as category_name 
        FROM links l
        LEFT JOIN link_categories c ON l.category_id = c.id
        WHERE l.id = $id
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function createLink($data) {
    global $conn;

    $title = mysqli_real_escape_string($conn, $data['title']);
    $url   = mysqli_real_escape_string($conn, $data['url']);
    $cat   = !empty($data['category_id']) ? intval($data['category_id']) : "NULL";
    $desc  = !empty($data['description']) ? "'" . mysqli_real_escape_string($conn, $data['description']) . "'" : "NULL";

    $sql = "
        INSERT INTO links (title, url, category_id, description, created_at)
        VALUES ('$title', '$url', $cat, $desc, NOW())
    ";

    return mysqli_query($conn, $sql);
}

function updateLink($id, $data) {
    global $conn;

    $id    = intval($id);
    $title = mysqli_real_escape_string($conn, $data['title']);
    $url   = mysqli_real_escape_string($conn, $data['url']);
    $cat   = !empty($data['category_id']) ? intval($data['category_id']) : "NULL";
    $desc  = !empty($data['description']) ? "'" . mysqli_real_escape_string($conn, $data['description']) . "'" : "NULL";

    $sql = "
        UPDATE links 
        SET title='$title', url='$url', category_id=$cat, description=$desc, updated_at=NOW()
        WHERE id=$id
    ";

    return mysqli_query($conn, $sql);
}

function deleteLink($id) {
    global $conn;
    $id = intval($id);

    return mysqli_query($conn, "DELETE FROM links WHERE id=$id");
}

function incrementClickCount($id) {
    global $conn;
    $id = intval($id);

    return mysqli_query($conn, "UPDATE links SET click_count = click_count + 1 WHERE id=$id");
}