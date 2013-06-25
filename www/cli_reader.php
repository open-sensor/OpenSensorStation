<?php
include 'interface_tmote.php';

	$serverType = $_GET["srv"];
	$command = $_GET["cmd"];
	$server;

	if($serverType == "cmdServer") {
		$server = new InterfaceCmd();
		$result = $server->readSensor($command);
		echo $result;
	}
	else if($serverType == "serialServer") {
		$server = new InterfaceSf();
		$result = $server->readSensor($command);
		echo $result;
	}
	else if($serverType == "") {
		echo "Error: No server type specified. Available options include: 'cmdServer' and 'serialServer'.";
	}
	else {
		echo "Error: Invalid server type. Available options include: 'cmdServer' and 'serialServer'.";
	}
?>
