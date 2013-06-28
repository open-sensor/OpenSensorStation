<?php
include 'request.php';

class RestMain
{
	private static $ValidBaseString = "senseapi";
	private static $ValidSecondStrings = array("data", "status", "location", "info");
	private static $ValidThirdStrings = array("temperature", "humidity", "light");

	private static $BaseString="";
	private static $SecondString="";
	private static $ThirdString="";
	private static $IsValidPath=false;
	private static $SizeOfPath=-1;
	
	public static function handleRequest()
	{
		self::handlePath();
		$method = $_SERVER["REQUEST_METHOD"];
	//	$request = new Request();
	//	$data = array();

		if($sizeOfPath == 2) {
		/*	if() {
				
			} */
		}
		else if ($sizeOfPath == 3){

		}
		else {
			$_IsValidPath = false;
		}
		// =========================================================

		if($method == "GET") {
			if(self::$BaseString == "senseapi" 
				&& self::$SecondString == ""
				&& self::$ThirdString == ""
				&& self::$SizeOfPath == ) {
				
			}
			else {
				// Invalid url
			}
		} 
		else if ($method == "PUT") {
			if(self::$BaseString == "senseapi" 
				&& self::$SecondString == "location" 
				&& self::$ThirdString != ""
				&& self::$SizeOfPath == 3) {
				// Valid put request for location.
				// Add this data to the Request class.
			}
			else {
				// Invalid url
			}
		}
		else {
			// Invalid request type.
		}		
	}

	private static function handlePath() {
		$uri = $_SERVER["REQUEST_URI"];
		$uri = parse_url($uri);
		$path = $uri["path"];

		// If the last char in the path is a slash "/", remove it.
		$lastCharInString = $path[strlen($path)-1];
		if($lastCharInString == "/") {
			$path[strlen($path)-1]="";
		}
		
		$pathStrings = explode("/", $path);
		array_shift($pathStrings); 	// Getting rid of the empty value generated
						// by the first '/' character in the url
		self::$SizeOfPath = sizeof($pathStrings);
		self::$BaseString = array_shift($pathStrings);
		self::$SecondString = array_shift($pathStrings);
		self::$ThirdString = array_shift($pathStrings);
	}

	public static function sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
		
	}

	public static function getStatusCodeMessage($status)
	{
	// these could be stored in a .ini file and loaded
	// via parse_ini_file()... however, this will suffice
	// for an example
	$codes = Array(
	    100 => 'Continue',
	    101 => 'Switching Protocols',
	    200 => 'OK',
	    201 => 'Created',
	    202 => 'Accepted',
	    203 => 'Non-Authoritative Information',
	    204 => 'No Content',
	    205 => 'Reset Content',
	    206 => 'Partial Content',
	    300 => 'Multiple Choices',
	    301 => 'Moved Permanently',
	    302 => 'Found',
	    303 => 'See Other',
	    304 => 'Not Modified',
	    305 => 'Use Proxy',
	    306 => '(Unused)',
	    307 => 'Temporary Redirect',
	    400 => 'Bad Request',
	    401 => 'Unauthorized',
	    402 => 'Payment Required',
	    403 => 'Forbidden',
	    404 => 'Not Found',
	    405 => 'Method Not Allowed',
	    406 => 'Not Acceptable',
	    407 => 'Proxy Authentication Required',
	    408 => 'Request Timeout',
	    409 => 'Conflict',
	    410 => 'Gone',
	    411 => 'Length Required',
	    412 => 'Precondition Failed',
	    413 => 'Request Entity Too Large',
	    414 => 'Request-URI Too Long',
	    415 => 'Unsupported Media Type',
	    416 => 'Requested Range Not Satisfiable',
	    417 => 'Expectation Failed',
	    500 => 'Internal Server Error',
	    501 => 'Not Implemented',
	    502 => 'Bad Gateway',
	    503 => 'Service Unavailable',
	    504 => 'Gateway Timeout',
	    505 => 'HTTP Version Not Supported'
	);

	return (isset($codes[$status])) ? $codes[$status] : '';
	}
}

?>
