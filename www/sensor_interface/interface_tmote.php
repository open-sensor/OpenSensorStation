<?php
include 'interface_sf.php';
include 'interface_cmd.php';

/**
* This class is responsible for socket-based access to the services that provide communication with
* the sensor device. It serves as a communication interface/abstraction layer for performing a single 
* request to the sensor device and receiving its response. It is abstract, and the functionality is
* used by the instantiations of its 2 subclasses, each of which represent the 2 services for accessing
* mote data and other information respectively. 
* author: Nikos Moumoulidis
*/
abstract class InterfaceTmote
{
	protected $_Carriage_and_newline = "\r\n";
	protected $_Host="127.0.0.1";
	protected $_Port = null;
	protected $_CommandList = null;

	// Depending on the type of service we need access to, different port and
	// list of commands is passed to the constructor.
	function __construct($port, array $commandlist) {
		$this->_Port = $port;
		$this->_CommandList = $commandlist;
    	}

	/*Provides the core functionality of the class, a single socket connection for a single request,
	and returning the result. Note that the waiting(usleep()) after closing the socket is used as
	a safeguard between possible repeated requests, since the base station and sensor device will 
	not be able to handle them. */
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

	// Validates the command passed for request.
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
