<?php
include 'sensor_interface/interface_tmote.php';

	$serverType = $_GET["srv"];
	$command = $_GET["cmd"];

	if($serverType == "cmdServer") {
		$commandServer = new InterfaceCmd();
		echo $commandServer->queryServer($command);
	}
	else if ($serverType == "sfServer") {
		$serialForwarderServer = new InterfaceSf();
		$serialForwarderServer->updateCommandList();
		echo $serialForwarderServer->queryServer($command);
	}
	else if ($serverType == "") {
		echo "\n Error: No server type specified. Available options include: 'cmdServer' 'sfServer'. \n";
	}
	else {
		echo "\n Error: Invalid server type. Available options include: 'cmdServer' 'sfServer'. \n";
	}
?>
