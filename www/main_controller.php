<?php
include 'rest_api/api_controller.php';

Controller::main();

class Controller
{
	public static function main() {
		APIController::handleRequest();
		APIController::sendResponse();
	}
}
?>
