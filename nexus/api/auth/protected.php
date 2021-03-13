<?php

require "vendor/autoload.php";
use \Firebase\JWT\JWT;

$secret_key = "YOUR_SECRET_KEY";
$jwt = null;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];

if($jwt) {
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        echo json_encode(array(
            "message" => "Access granted:"
        ));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}

?>

