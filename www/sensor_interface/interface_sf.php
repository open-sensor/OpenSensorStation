<?php

/*This class is used to instantiate the communication abstraction layer for the
serial forwarder service running on the base-station, and in extent the sensor itself
for gathering data. It is initially agnostic of the mote's available sensors, and 
performs an update request upon its construction. */
class InterfaceSf extends InterfaceTmote
{
	// Initially passes the "sensorlist" command as the only available command,
	// then updates the list.
	function __construct() {
		// The only available command at first is "sensorlist".
		parent::__construct(9001, $commandlist = array("sensorlist"));
		$this->updateCommandList();
    	}

	/*Called in the constructor to update the list of available commands, 
	by performing a request to the serial server to get the list of 
	available sensors on board. */
	private function updateCommandList() {
		$listString = $this->queryServer("sensorlist");
		$this->_CommandList = null;
		$updatedList = explode(" ", $listString);
		$this->_CommandList = $updatedList;
	}

	function __destruct() {
		parent::__destruct();
	}
}
?>
