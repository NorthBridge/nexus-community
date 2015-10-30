<?php

session_start();

require_once("Util.php");
require_once(Utilities::getSrcRoot() . "/schedule/Event.php");

// TODO - this check causes JSON object parse to be thrown from javascript -- better to tokenize it anyway
//if (Utilities::is_ajax()) {
	if (Utilities::isSessionValid()) {
		// TODO - this will break if user has > 1 group
		$data = Event::getFutureEvents(array_keys($_SESSION['groups'])[0], $_SESSION['timezone'], $_SESSION['uidpk']);
		header('Content-Type: application/json');			
		echo json_encode($data);
	} else {
		header("location:" . Utilities::getHttpPath() . "/index.php");
		exit(0);		
	}			
//}	

?>

