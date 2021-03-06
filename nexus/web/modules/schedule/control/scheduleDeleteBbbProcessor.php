<?php

session_start();

require_once("../../../src/framework/Util.php");
require_once(Utilities::getSrcRoot() . "/schedule/Event.php");

// TODO - put authorization checker, session checker, error handling, etc. in a central place. These should go at the top of every processor.

if (!Utilities::isSessionValid()) {
	header("location:" . Utilities::getHttpPath() . "/nexus.php");
	exit(0);
}
							
$result = validateInput($_GET);

if (count($result['error']) > 0) {
	print_r($result);
	exit(0);
}

/* ====================================================

Use only clean input beyond this point (i.e. $clean[])

======================================================= */

$meetingType = Utilities::getMeetingTypes();

$result = Event::deleteEventBbb($result['clean']['meeting-uuid']);

if ((session_status() === PHP_SESSION_ACTIVE) && isset($_SESSION['nexusContext'])) {
 switch($_SESSION['nexusContext']) {
 		case "NWM":
			header("location:" . Utilities::getHttpPath() . "/nexus.php");
 			break;
 		case "ADV":
			header("location:" . Utilities::getHttpPath() . "/nexus.php");
 			break;
 		case "PUB":
 			header("location:" . Utilities::getPluginPath() . "/publicSuite.php?oid=" . $_SESSION['orgUid'] . "&context=calendar&confirm=" . $result['clean']['meeting-contact']);
 			break;
 		default: 			
 			header("location:" . Utilities::getPluginPath() . "/publicSuite.php?oid=" . $_SESSION['orgUid'] . "&context=calendar&confirm=" . $result['clean']['meeting-contact']);
 	}
} else {
 	header("location:" . Utilities::getPluginPath() . "/publicSuite.php?oid=" . $_SESSION['orgUid']);
 	exit(0);
}	

function validateInput($input) {
	$result = array('clean' => array(), 'error' => array());
	
	// MEETING UUID
	if (isset($input['uuid']) && Utilities::validateUuid($input['uuid'])) {
		$result['clean']['meeting-uuid'] = $input['uuid'];
	} else {
		$result['error']['meeting-uuid'] = "error";
	}

 	return $result;
}

?>