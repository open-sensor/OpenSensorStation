<?php

// Tmote Command Server Interface class.
class InterfaceCmd extends InterfaceTmote
{
	// Set Location Command Length (SLCL) to be used for identifying the use 
	// of this command in the parent class.
	protected static $SLCL;

	public function __construct() {
		$commandlist = array("status", "set location");
        	parent::__construct("10001", $commandlist);
		self::$SLCL = strlen("set location ");
    	}
}

?>
