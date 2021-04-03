<?php

require "vendor/autoload.php";
use \Firebase\JWT\JWT;

require_once("../Util.php");
require_once(Utilities::getSrcRoot() . "/user/User.php");

$cleanUsername = '';
$isAuthenticated = false;

if (isset($_GET['username'])) {
    $cleanUsername = $_GET['username'];
}

if (isset($_POST['username'])) {
    $cleanUsername = $_POST['username'];
}

if ($cleanUsername == 'gooduser' && (isset($_GET['password']) || isset($_POST['password']))) {
	// replace with real lookup
	$isAuthenticated = true;
}

if($isAuthenticated){
	$cookie_options = array (
		'expires' => time() + 60*60*24*30,
		'path' => '/dev',
		'domain' => 'northbridgetech.org', // leading dot for compatibility or use subdomain
		'secure' => true,
		'httponly' => true,
		'samesite' => 'Strict' // None || Lax  || Strict
	);
	setcookie(
		'NTA_TOKEN',
		getJwt('gooduser@test.com'),
		$cookie_options
	);
	header("Location: protected.php");
	exit();
} else {
	header('Content-Type: application/json');
	echo json_encode(array('status' => 'ERROR'));
}

function getJwt($email) {

	$secret_key = Utilities::getJwtSecret();
	$issuer_claim = "THE_ISSUER"; // this can be the servername
	$audience_claim = "THE_AUDIENCE";
	$issuedat_claim = time(); // issued at
	$notbefore_claim = $issuedat_claim; //not before in seconds
	$expire_claim = $issuedat_claim + 360; // expire time in seconds
	$token = array(
		"iss" => $issuer_claim,
		"aud" => $audience_claim,
		"iat" => $issuedat_claim,
		"nbf" => $notbefore_claim,
		"exp" => $expire_claim,
		"data" => array(
			"id" => '1',
			"authorizations" => array(
				"nexus" => "admin",
				"crm" => "member",
				"api" => "user"
			)
		));

	$jwt = JWT::encode($token, $secret_key);

	return $jwt;

}


?>

