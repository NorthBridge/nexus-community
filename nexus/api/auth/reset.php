<?php

$cleanUsername = '';

if (isset($_GET['username'])) {
    $cleanUsername = $_GET['username'];
}

if (isset($_POST['username'])) {
    $cleanUsername = $_POST['username'];
}

if ($cleanUsername == 'baduser') {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'ERROR'));
} else {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'OK'));
}


?>

