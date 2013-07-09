<?php
include 'interface_sf.php';
include 'interface_cmd.php';

abstract class InterfaceTmote
{
	protected $_Carriage_and_newline = "\r\n";
	protected $_Host="127.0.0.1";
	protected $_Port = null;
	protected $_CommandList = null;

	function __construct($port, array $commandlist) {
		$this->_Port = $port;
		$this->_CommandList = $commandlist;
    	}

	protected function updateCommandList() {
		$listString = $this->queryServer("sensorlist");
		$updatedList = explode(" ", $listString);
		$this->_CommandList = $updatedList;
	}

	public function queryServer($command) {
		if(!$this->isValidCommand($command)) {
			return;
		}
		else {
			$command = $command.$this->_Carriage_and_newline;
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("\n From Client: Could not create socket \n");
			$result = socket_connect($socket, $this->_Host, $this->_Port) or die("\n From Client: Unable to connect to server \n");
			socket_write($socket, $command, strlen($command)) or die("\n From Client: Unable to send data to server \n");
			$result = socket_read ($socket, 1024) or die("\n From client: Could not read response from server \n");
			socket_close($socket);
			$result = trim($result);
			usleep(100000); // Important to sleep between requests or else the
					// base-station and mote will not be able to handle it.
			return $result;
		}
	}

	public function getCommandList() {
		return $this->_CommandList;
	}

	public function isValidCommand($command) {
		// Check if the command field is empty.
		if($command == "") {
			echo "\n Error: No command specified. Available options include:";
			$this->listCommands();
			return false;
		}
		
		// Check if the command given is valid for the specified server.
		for ($i=0 ; $i < sizeof($this->_CommandList) ; $i++) {
			if($command == $this->_CommandList[$i]) {
				return true;
			}
		}

		// Check if the command is issued to the "Command Server" and 
		// starts with the string "set location " (which is valid).
		if(get_called_class() == "InterfaceCmd") {
			$setLocationCommandLength = InterfaceCmd::$SLCL;
			$tempString = substr($command, 0, $setLocationCommandLength);
			if($tempString == "set location ") {
				return true;
			}
		}

		// Reaching this line means command is invalid.
		echo "\n Error: Invalid command. Available options include:";
		$this->listCommands();
		return false;
	}

	private function listCommands() {
		for ($i=0 ; $i < sizeof($this->_CommandList) ; $i++) {
			echo " '".$this->_CommandList[$i]."' ";
		}
		echo "\n";
	}

	function __destruct() {
		unset($this->_CommandList);
	}
}

?>
