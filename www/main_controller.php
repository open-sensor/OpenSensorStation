<?php
include 'rest_api/api_controller.php';

/* All HTTP requests are directed to this script by the web server.
It handles the HTTP request using an APIController object. */

/* If the aggregator is running at the moment, wait 5
seconds for it to finish before handling the request. */
while(isAggregatorRequestRunning()) {
	sleep(5);
}

$apiController = new APIController();
$apiController->handleRequest();
$apiController->sendResponse();
unset($apiController);

function isAggregatorRequestRunning() {
	return exec("ps | grep aggregator | grep -v grep | wc -l");
}
?>
