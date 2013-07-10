<?php
include 'data_manager/data_reader.php';

/* This class is responsible for handling all the HTTP requests to the server at large,
validating them, performing the appropriate actions by using a DataReader object, and 
finally forming and sending appropriate HTTP responses back to the client. 
It validates the REST-styled URI of requests, as described by the appropriate URI list specification. */
class APIController
{
	private $_Status = 200;
	private $_Body = "";
	private $_ContentType = "text/html";

	private $_DataReader = null;
	private $_FirstVarList = array("data", "status", "location", "datetime");

	function __construct() {
		$this->_DataReader = new DataReader();
    	}

	// Check if the first variable is one of the valid/expected, as described in the
	// appropriate URI list specification.
	private function isFirstVarValid($var1) {
		foreach ($this->_FirstVarList as $command) {
			if($var1 == $command) {
				return true;
			}
		}
		return false;
	}

	// Check if the second variable is one of the valid/expected, as described in the
	// appropriate URI list specification.
	private function isSecondVarValid($var2) {
		$secondVarList = $this->_DataReader->getSerialCommandList();
		$secondVarList[] = "";
		foreach ($secondVarList as $command) {
			if($var2 == $command) {
				return true;
			}
		}
		return false;
	}

	/* The main function responsible for handling the HTTP request. It checks the HTTP request method, 
	and the GET variables passed over, in order to identify the status of the upcoming reponse,
	and form the response's body (based on data retrieved by the DataReader object, or appropriate 
	error message in the case of invalid request.) */
	public function handleRequest()
	{
		$var1 = $_GET["var1"];
		$var2 = $_GET["var2"];
		$method = $_SERVER["REQUEST_METHOD"];
		$accept = $_SERVER["HTTP_ACCEPT"];

		// Check if the variables given in the form of URI are valid resources.
		if(!$this->isFirstVarValid($var1)) {
			$this->_Status = 404;
			return;
		}
		if(!$this->isSecondVarValid($var2)) {
			$this->_Status = 404;
			return;
		}

		if($method == "GET") {
			if ($var1 == "data") {
				// If the second variable requested is empty, the client has requested all
				// the stored data, which he must specify to be in JSON format.
				if ($var2 == "") {
					if($accept == "application/json") {
						$this->_Body = $this->_DataReader->getAllData();
						// We also need to delete the persistent storage file to free
						// the limited disk space for data storage aggregation.
						$this->_DataReader->deleteAllData();
					}
					else {
						$this->_Status = 406;
					}
				}
				else {
					$this->_Body = $this->_DataReader->getSingleValue($var2, ServerType::SERIAL);
				}
			}
			else {	// If the client did not request sensor-reading data, we must make sure
				// that the second variable is empty.
				if($var2 == "") {
					$this->_Body = $this->_DataReader->getSingleValue($var1, ServerType::COMMAND);
				}
				else {
					$this->_Status = 404;
				}
			}
		}
		else if ($method == "PUT") {
			// The only acceptable HTTP PUT request is for updating the location.
			if ($var1 == "location" && $var2 == "") {
				$locationString = file_get_contents('php://input');
				$this->_DataReader->setServerLocation($locationString);
			}
			else {
				$this->_Status = 405;
			}
		}
		else {
			$this->_Status = 405;
		}

		// If a GET request was correct but the server did not return data,
		// we must notify the client that the service is unavailable.
		if($method == "GET" && $this->_Status == 200 && $this->_Body == "") {
			$this->_Status = 503;
		}

		// Handling bug caused when the aggregator script is performing 
		// sensor requests at the same time this rest-api request is being handled.
		if($this->_Body == "temp humid light") {
			$this->_Body = "";
			$this->_Status = 503;
		}
	}


	// Sets up and sends the reponse headers and body based on the results of the 
	// handleRequest() method. Status codes are translated into user-friendly messages to
	// inform the client what went wrong in detail.
	public function sendResponse()
	{
		$status_header = "HTTP/1.1 " . $this->_Status . " " . $this->getStatusCodeMsg($this->_Status);
		// set the status
		header($status_header);
		// set the content type
		header("Content-type: " . $this->_ContentType);

		// If we have a body already, it means that data is being passed.
		if($this->_Body != "") {
			echo $this->_Body;
			exit;
		}
		// We create an HTML body if none is passed, and set the appropriate error message.
		else {
			$msg ="";
			switch ($this->_Status)
			{
			case 404:
				$msg = "The requested resource does not exist.";
				break;
			case 405:
				$msg = "The requested method is not allowed.";
				break;
			case 406:
				$msg = "Accepting only 'application/json' MIME type for this resource.";
				break;
			case 503:
				$msg = "The sensor station is unavailable at this time.";
				break;
			}
			$this->_Body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
				    <html>
					<head>
					    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					    <title>' . $this->_Status . ' ' . $this->getStatusCodeMsg($this->_Status) . '</title>
					</head>
					<body>
					    <h1>' . $this->getStatusCodeMsg($this->_Status) . '</h1>
					    <p>' . $msg . '</p>
					</body>
				    </html>';
			echo $this->_Body;
			exit;
		}
	}

	// Utility method for interpreting HTTP response status codes into their meanings.
	private function getStatusCodeMsg($status)
	{
		$codes = Array(
		    200 => 'OK',
		    404 => 'Not Found',
		    405 => 'Method Not Allowed',
		    406 => 'Not Acceptable',
		    500 => 'Internal Server Error',
		    501 => 'Not Implemented',
		    503 => 'Service Unavailable'
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}

	function __destruct() {
		unset($this->_DataReader);
    	}
}

?>
