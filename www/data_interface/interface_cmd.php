<?php

// Tmote Command Server Interface class.
class InterfaceCmd extends InterfaceTmote
{
	public function __construct() {
		$commandlist = array("help", "status", "set location");
        	parent::__construct("10001", $commandlist);
    	}
}

?>
