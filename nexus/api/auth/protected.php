<?php

require "vendor/autoload.php";
use \Firebase\JWT\JWT;

require_once("../Util.php");

$secret_key = Utilities::getJwtSecret();
$jwt = $decoded = null;

if(isset($_COOKIE['NTA_TOKEN'])) {
    $jwt = $_COOKIE['NTA_TOKEN'];
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        echo json_encode(array(
            "message" => "Access granted to a protected member resource. Forwarding you to...",
            "token" => $decoded
        ));
        exit();
    } catch (Exception $e) {
        /*
         * Things that could happen: token is expired; token is not yet valid, malformed JSON, what else?
         */
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied. Redirect to login.",
            "error" => $e->getMessage()
        ));
        exit();
    }

} else {
    echo json_encode(array(
        "message" => "Access denied. Redirect to login."
    ));
    exit();
}

?>

