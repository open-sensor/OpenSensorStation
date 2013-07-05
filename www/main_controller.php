<?php
include 'rest_api/api_controller.php';

$apiController = new APIController();
$apiController->handleRequest();
$apiController->sendResponse();
?>
