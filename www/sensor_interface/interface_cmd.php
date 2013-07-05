<?php

// Tmote Command Server Interface class.
class InterfaceCmd extends InterfaceTmote
{
	// Set Location Command Length (SLCL) to be used for identifying the use 
	// of this command in the parent class.
	protected static $SLCL;

	function __construct() {
        	parent::__construct(10001, $commandlist = array("status", "set location"));
		self::$SLCL = strlen("set location ");
    	}

	function __destruct() {
		parent::__destruct();
	}
}

?>
