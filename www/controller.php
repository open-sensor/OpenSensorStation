<?php
include 'rest_api/rest_main.php';

Controller::main();

class Controller
{
	public static function main() {
		echo "This is 'controller.php', serving as the 'index' page! ";
		// Call to REST api's "processRequest()"
		//$restMain = new RestMain();
		RestMain::handleRequest();
		// Switch/If statement for HTTP methods 
		// to perform the appropriate actions
	
	}
}
?>
