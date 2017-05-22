<?php

session_start();

require_once("../../../src/framework/Util.php");
require_once(Utilities::getSrcRoot() . "/organization/Organization.php");

// TODO - put authorization checker, session checker, error handling, etc. in a central place. These should go at the top of every processor.

if (!Utilities::isSessionValid()) {
	header("location:" . Utilities::getHttpPath() . "/nexus.php");
	exit(0);
}
									
// Following works, but only populates form values that HAVE a value. Empties do not copy over.
$result = validateOrganization($_POST);

if (count($result['error']) > 0) {
	header('Content-Type: application/json');			
	echo json_encode($result);	
 	exit(0);
}

/* ====================================================

Use only clean input beyond this point (i.e. $clean[])

======================================================= */

$return = array();
if (isset($result['clean']['org-id'])) {
	// TODO - not checking for admin role on edit scenario
	$thisOrg = Organization::getOrganizationById($result['clean']['org-id']);
	if (pg_num_rows($thisOrg) == 1) {
		Organization::addOrganizationProgram($result['clean']['org-id']);
	}
}

$return['org-name'] = $result['clean']['org-name'];
$return['org-id'] = $result['clean']['org-id'];

if ((session_status() === PHP_SESSION_ACTIVE) && isset($_SESSION['nexusContext'])) {
 switch($_SESSION['nexusContext']) {
 		case "ADV":
			header('Content-Type: application/json');			
			echo json_encode($return);	
 			break;
 		default: 			
 	}
}

function validateOrganization($input) {
	$result = array('clean' => array(), 'error' => array());

	if (isset($input['org-name']) && strlen($input['org-name']) > 0) {
		$result['clean']['org-name'] = $input['org-name'];
	} else {
		$result['error']['org-name'] = "error";
	}

	if (isset($input['org-id']) && Utilities::validateNetworkIdFormat($input['org-id'])) {
		$result['clean']['org-id'] = $input['org-id'];
	} else {
		$result['error']['org-id'] = "error";
	}

	$result['clean']['name'] = $input['name'];
	$result['clean']['description'] = $input['description'];
	$result['clean']['eligibility'] = $input['eligibility'];
	$result['clean']['services'] =$input['services'];
	$result['clean']['involvement'] =$input['involvement'];
	$result['clean']['partner_interest'] = $input['partner_interest'];
	$result['clean']['partner_kind'] = $input['partner_kind'];

 	return $result;
}

?>