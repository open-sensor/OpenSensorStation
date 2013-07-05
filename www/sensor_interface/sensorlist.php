<?php

class SensorListGatherer {

	private $_Host= "127.0.0.1";
	private $_Port = 9001;

	//Get the list of sensors.
	public function getSensorList() {
		$command = "sensorlist\r\n";
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("\n From Client: Could not create socket \n");
		$result = socket_connect($socket, $this->_Host, $this->_Port) or die("\n From Client: Unable to connect to server \n");
		socket_write($socket, $command, strlen($command)) or die("\n From Client: Unable to send data to server \n");
		$result = socket_read ($socket, 1024) or die("\n From client: Could not read response from server \n");
		socket_close($socket);
		$result = trim($result);

		$arrayList = $this->transformToArray($result);
		return $arrayList;
	}

	private function transformToArray($list) {
		return explode(" ", $list);
	}
}
?>
