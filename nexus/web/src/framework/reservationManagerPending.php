<?php

// TODO This file is only temporary - refactor and delete soon

session_start();

require_once("Util.php");
require_once(Utilities::getSrcRoot() . "/schedule/Event.php");

// TODO - this check causes JSON object parse to be thrown from javascript -- better to tokenize it anyway
//if (Utilities::is_ajax()) {
	if (Utilities::isSessionValid()) {
		// This pulls future pendings for session public group
		$data = Event::getFutureEvents($_SESSION['pgpk'], $_SESSION['timezone'], $_SESSION['uidpk'], "3");
		header('Content-Type: application/json');			
		echo json_encode($data);
	} else {
		header("location:" . Utilities::getHttpPath() . "/nexus.php");
		exit(0);		
	}			
//}	

?>

