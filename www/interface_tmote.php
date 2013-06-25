<?php
include 'interface_sf.php';
include 'interface_cmd.php';

abstract class InterfaceTmote
{
//	protected static $NEWLINE = "\r\n";
//	does not work ^^
	protected static $HOST="127.0.0.1";
	protected $_Port;
	protected $_CommandList;

	public function __construct($port, array $commandlist) {
		$this->_Port = $port;
		$this->_CommandList = $commandlist;
    	}

	public function readSensor($command) {
		if(!$this->isValidCommand($command)) {
			return;
		}
		/* TO-FIX: 
		PHP_EOL does not work -> 
		without \r\n, the serial forward server is left open ->
		maximum connections (8) are reached.
		*/
		$command = $command.PHP_EOL;
		$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("From Client: Could not create socket");
		$result = socket_connect($socket, self::$HOST, $this->_Port) or die("From Client: Unable to connect to server");
		socket_write($socket, $command, strlen($command)) or die("From Client: Unable to send data to server");
		$result = socket_read ($socket, 1024) or die("From client: Could not read response from server ");
	//	socket_shutdown($socket, 2);
		socket_close($socket);
		$result = trim($result);
		echo "Result Received... ";
		return $result;
	}

	private function isValidCommand($command) {
		// Check if the command field is empty.
		if($command == "") {
			echo "Error: No command specified. Available options include:";
			$this->listCommands();
			return false;
		}

		// Check if the command given is valid for the specified server.
		for ($i=0 ; $i < sizeof($this->_CommandList) ; $i++) {
			if($command == $this->_CommandList[$i]) {
				return true;
			}
		}
		echo "Error: Invalid command. Available options include:";
		$this->listCommands();
		return false;
	}

	private function listCommands() {
		for ($i=0 ; $i < sizeof($this->_CommandList) ; $i++) {
			echo " '".$this->_CommandList[$i]."' ";
		}
		echo PHP_EOL;
	}
}

?>
