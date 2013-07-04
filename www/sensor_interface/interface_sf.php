<?php

// Tmote Serial Forwarder Server Interface class.
class InterfaceSf extends InterfaceTmote
{
	public function __construct() {
		// The only available command at first is "sensorlist".
		$commandlist = array("sensorlist");
		parent::__construct("9001", $commandlist);
    	}

	public function updateCommandList() {
		// "sensorlist" is issued to the mote using the parent class' "queryServer()"
		// to get the available sensors in the form of space separated values,
		// which then are turned into an array, replacing the old command list
		// (which only included "sensorlist"), with the list of commands
		// for the available sensors on the mote.
		$newlist = $this->queryServer("sensorlist");
		$newlist = trim($newlist);
		$updatedCommandList = explode(" ", $newlist);
		$this->_CommandList = $updatedCommandList;	
	}
}
?>
