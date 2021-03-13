<?php

require "vendor/autoload.php";
use \Firebase\JWT\JWT;

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
		setJwt('gooduser@test.com');
}

function setJwt($email) {

	$secret_key = "YOUR_SECRET_KEY";
	$issuer_claim = "THE_ISSUER"; // this can be the servername
	$audience_claim = "THE_AUDIENCE";
	$issuedat_claim = time(); // issued at
	$notbefore_claim = $issuedat_claim + 10; //not before in seconds
	$expire_claim = $issuedat_claim + 60; // expire time in seconds
	$token = array(
		"iss" => $issuer_claim,
		"aud" => $audience_claim,
		"iat" => $issuedat_claim,
		"nbf" => $notbefore_claim,
		"exp" => $expire_claim,
		"data" => array(
			"id" => '1',
			"firstname" => 'Good',
			"lastname" => 'User',
			"email" => $email
		));

	http_response_code(200);

	$jwt = JWT::encode($token, $secret_key);

	echo json_encode(
		array(
			"status" => "OK",
			"jwt" => $jwt,
			"email" => $email,
			"expireAt" => $expire_claim
		)
	);

}

?>

