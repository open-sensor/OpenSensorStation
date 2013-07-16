<?php

/*This class is used to instantiate the communication abstraction layer for the
serial forwarder service running on the base-station, and in extent the sensor itself
for gathering data. It is initially agnostic of the mote's available sensors, and 
performs an update request upon its construction. */
class InterfaceSf extends InterfaceTmote
{
	private $sensorListFileLocation = "../sensorlist.conf";
	// Initially passes the "sensorlist" command as the only available command,
	// then updates the list.
	function __construct() {
		// The only available command at first is "sensorlist".
		parent::__construct(9001, $commandlist = array("sensorlist"));
    	}

	/* Called by the DataReader constructor to populate the list of available sensor commands
	read from the sensorlist.conf file. If the file does not exist, it is created and
	this function is called again recursively to finally update the list. */
	public function readSensorList() {
		if(file_exists($this->sensorListFileLocation)) {
			$sensorListString = file_get_contents($this->sensorListFileLocation);
			$updatedList = explode(" ", $sensorListString);
			$this->_CommandList = $updatedList;
		}
		else {
			$this->updateCommandList();
			$this->readSensorList(); // Recursion until there is a valid sensorlist.conf
						// to update the list from.
		}
	}

	/* Called by readSensorList() to update the list of available commands
	in the sensorlist.conf file, by performing a request to the serial server 
	to get the list of available sensors on board and write them in the file. 
	Also called by sensorlist_updater.php, which is called by the accumulator script. */
	public function updateCommandList() {
		$invalidList = false;
		do {
			$invalidList = false;
			$listString = $this->queryServer("sensorlist");
			$updatedList = explode(" ", $listString);
			for($i=0 ; $i<sizeof($updatedList) ; $i++) {
				if($updatedList[$i] == "" || $updatedList[$i] == null) {
					$invalidList = true;
				}
			}
			usleep(50000);
		} while($invalidList == true);
		file_put_contents($this->sensorListFileLocation, $listString);
	}

	function __destruct() {
		parent::__destruct();
	}
}
?>
