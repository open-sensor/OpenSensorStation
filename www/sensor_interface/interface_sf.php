<?php
include 'sensorlist.php';

// Tmote Serial Forwarder Server Interface class.
class InterfaceSf extends InterfaceTmote
{
	private $_SensorList = null;

	function __construct() {
		$this->_SensorList = new SensorListGatherer();
		$list = $this->_SensorList->getSensorList();

		// The only available command at first is "sensorlist".
		parent::__construct(9001, $list);
    	}

	function __destruct() {
		unset($this->_SensorList);
		parent::__destruct();
	}
}
?>
