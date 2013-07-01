<?php
include 'data_manager/data_reader.php';

class APIController
{
	private static $Status = 200;
	private static $Body = "";
	private static $ContentType = "text/html";

	public static function handleRequest()
	{
		$var1 = $_GET["var1"];
		$var2 = $_GET["var2"];
		$method = $_SERVER["REQUEST_METHOD"];
		$accept = $_SERVER["HTTP_ACCEPT"];

		if(!self::isFirstVarValid($var1)) {
			self::$Status = 404;
			return;
		}
		
		$dataReader = new DataReader();
		if($method == "GET") {
			if ($var1 == "data") {
				switch($var2)
				{
				case "temp":
					self::$Body = $dataReader->getSingleValue("temp", ServerType::SERIAL);
					break;
				case "humid":
					self::$Body = $dataReader->getSingleValue("humid", ServerType::SERIAL);
					break;
				case "light":
					self::$Body = $dataReader->getSingleValue("light", ServerType::SERIAL);
					break;
				case "":
					if($accept == "application/json") {
						self::$Body = $dataReader->getAllData();
					}
					else {
						self::$Status = 406;
					}
					break;
				default:
					self::$Status = 404;
				}
			}
			else if ($var1 == "location" && $var2 == "") {
				self::$Body = $dataReader->getSingleValue("location", ServerType::COMMAND);
			}
			else if($var1 == "status" && $var2 == "") {
				self::$Body = $dataReader->getSingleValue("status", ServerType::COMMAND);
			}
			else if($var1 == "info" && $var2 == "") {
				self::$Status = 501;
				// To-be-implemented: get info about available sensors.
			}
			else {
				self::$Status = 404;
			}
		}
		else if ($method == "PUT") {
			if ($var1 == "location" && $var2 == "") {
				$locationString = file_get_contents('php://input');
				$dataReader->setServerLocation($locationString);
			}
			else {
				self::$Status = 404;
			}
		}
		else {
			self::$Status = 405;
		}
	}

	// Check if the first variable is one of the valid/expected.
	private static function isFirstVarValid($var1) {
		$varList = array("data", "status", "location", "info");
		$isFirstVarValid = false;
		for ($i = 0 ; $i < count($varList) ; $i++) {
			if($var1 == $varList[$i]) {
				$isFirstVarValid = true;
			}
		}
		return $isFirstVarValid;
	}

	public static function sendResponse()
	{
		$status_header = "HTTP/1.1 " . self::$Status . " " . self::getStatusCodeMessage(self::$Status);
		// set the status
		header($status_header);
		// set the content type
		header("Content-type: " . self::$ContentType);

		// pages with body are easy
		if(self::$Body != "") {
			// send the body
			echo self::$Body;
			exit;
		}
		// we need to create the body if none is passed
		else {
			$msg ="";
			switch (self::$Status)
			{
			case 404:
				$msg = "The requested resource does not exist.";
				break;
			case 405:
				$msg = "The requested method is not allowed.";
				break;
			case 406:
				$msg = "Accepting only 'application/json' media type for this resource.";
				break;
			case 501:
				$msg = "The requested service is not implemented yet.";
				break;
			}
			self::$Body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
				    <html>
					<head>
					    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					    <title>' . self::$Status . ' ' . self::getStatusCodeMessage(self::$Status) . '</title>
					</head>
					<body>
					    <h1>' . self::getStatusCodeMessage(self::$Status) . '</h1>
					    <p>' . $msg . '</p>
					</body>
				    </html>';
			echo self::$Body;
			exit;
		}
	}

	private static function getStatusCodeMessage($status)
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
}

?>
