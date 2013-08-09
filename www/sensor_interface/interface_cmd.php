<?php

/**
* This class is used to instantiate the communication abstraction layer for the
* command service running on the base-station, which is used for accessing contextual 
* information, like the device's status, location, and also setting(updating) its location. 
* author: Nikos Moumoulidis
*/
class InterfaceCmd extends InterfaceTmote
{
	// Set Location Command Length (SLCL) to be used for identifying the use 
	// of this command in the parent class.
	protected static $SLCL;

	/*The only acceptable commands passed to the parent constructor are
	"status" and "set location", as per the command service. Getting the location 
	is extracted in the data_manager layer from the "status" command output. */
	function __construct() {
        	parent::__construct(10001, $commandlist = array("status", "set location"));
		self::$SLCL = strlen("set location ");
    	}

	function __destruct() {
		parent::__destruct();
	}
}

?>
