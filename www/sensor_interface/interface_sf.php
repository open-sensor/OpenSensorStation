<?php

// Tmote Serial Forwarder Server Interface class.
class InterfaceSf extends InterfaceTmote
{
	function __construct() {
		// The only available command at first is "sensorlist".
		parent::__construct(9001, $commandlist = array("sensorlist"));
		$this->updateCommandList();
    	}

	function __destruct() {
		unset($this->_SensorList);
		parent::__destruct();
	}
}
?>
