<?php
require_once __DIR__ . '/../config/database.php';

function getHomeContent() {
    global $conn;

    $result = mysqli_query($conn, "SELECT * FROM home_description LIMIT 1");

    if ($row = mysqli_fetch_assoc($result)) {
        return [
            'topLabel'      => $row['top_label'] ?? '',
            'mainTitle'     => $row['main_title'] ?? '',
            'boldSubtitle'  => $row['bold_subtitle'] ?? '',
            'lightSubtitle' => $row['light_subtitle'] ?? '',
            'ctaText'       => $row['cta_text'] ?? '',
        ];
    }

    return [
        'topLabel' => '',
        'mainTitle' => '',
        'boldSubtitle' => '',
        'lightSubtitle' => '',
        'ctaText' => ''
    ];
}

function saveHomeContent($topLabel, $mainTitle, $boldSubtitle, $lightSubtitle, $ctaText) {
    global $conn;

    $topLabel      = mysqli_real_escape_string($conn, $topLabel);
    $mainTitle     = mysqli_real_escape_string($conn, $mainTitle);
    $boldSubtitle  = mysqli_real_escape_string($conn, $boldSubtitle);
    $lightSubtitle = mysqli_real_escape_string($conn, $lightSubtitle);
    $ctaText       = mysqli_real_escape_string($conn, $ctaText);

    $check = mysqli_query($conn, "SELECT id FROM home_description LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        $query = "UPDATE home_description SET 
            top_label='$topLabel',
            main_title='$mainTitle',
            bold_subtitle='$boldSubtitle',
            light_subtitle='$lightSubtitle',
            cta_text='$ctaText'
            LIMIT 1";
    } else {
        $query = "INSERT INTO home_description 
            (topLabel, mainTitle, boldSubtitle, lightSubtitle, ctaText)
            VALUES
            ('$topLabel', '$mainTitle', '$boldSubtitle', '$lightSubtitle', '$ctaText')";
    }

    return mysqli_query($conn, $query);
}