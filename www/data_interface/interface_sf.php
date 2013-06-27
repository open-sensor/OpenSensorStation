<?php

// Tmote Serial Forwarder Server Interface class.
class InterfaceSf extends InterfaceTmote
{
	public function __construct() {
		$commandlist = array("temp", "humid", "light");
        	parent::__construct("9001", $commandlist);
    	}
}

?>
