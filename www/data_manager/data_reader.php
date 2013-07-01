<?php
include 'sensor_interface/interface_tmote.php';
include 'data_storage.php';

abstract class ServerType
{
	const SERIAL = 0;
	const COMMAND = 1;
}

class DataReader
{
	private $_ValuesArray;

	// Reads a single value from the server specified.
	public function getSingleValue($valueType, $srvType) {
		if ($srvType == ServerType::SERIAL) {
			$serialServer = new InterfaceSf();
			return $serialServer->queryServer($valueType);
		}
		else if ($srvType == ServerType::COMMAND) {
			$commandServer = new InterfaceCmd();
			if($valueType == "location") {
				return $this->queryServerLocation();
			}
			if($valueType == "date/time") {
				return $this->queryServerDateTime();
			}
			return $commandServer->queryServer($valueType);
		}
		else {
			echo "\n Error: Invalid server type specified.";
		}
	}
	
	// Gets all the stored data in JSON format.
	public function getAllData() {
		return DataStorage::getAllData();
	}

	// Public usable function, reading all the real-time values.
	public function readAllValues() {
		$serialServer = new InterfaceSf();
		$temperature = $serialServer->queryServer("temp");
		$humidity = $serialServer->queryServer("humid");
		$light = $serialServer->queryServer("light");
		$location = $this->queryServerLocation();
		$dateTime = $this->queryServerDateTime();

		// Create array with all data values.
		$data = array( "date_time" => $dateTime, "location" => $location, 
			"temperature" => $temperature, "light" => $light, "humidity" => $humidity );
		$data = $this->trimData($data);
		$this->_ValuesArray = $data;
	}

	// Get date and time in an appropriate format.
	private function queryServerDateTime() {
		return date("d/m/Y h:i:s", time());
	}

	// Set the sensor's location.
	public function setServerLocation($newlocation) {
		$commandServer = new InterfaceCmd();
		$commandServer->queryServer("set location ".$newlocation);
	}

	// Abstracts away the extraction of location from the status server reading.
	private function queryServerLocation() {
		// Read the mote's status from the command server.
		$commandServer = new InterfaceCmd();
		$status = $commandServer->queryServer("status");

		// The location needs to be extracted from the status output.
		// Find the index position of the "Location:" line in the status output string.
		if (strpos($status,'Location:') !== false) {
	    		$index = strpos($status,'Location:');
			$index += 10;		// Add 10 to the index, which is the length of "Location: "
						// in order to arrive to the index where the location data
						// starts within the status output string.
		}
		// The location is the last piece of data from the status output, and this is why retrieve
		// the subtstring from the previously identified index and until the end of the string of the status.
		$location = substr($status, $index);
		return $location;
	}

	// Remove \r \n and other such characters.
	private function trimData(array $data) {
		$data["date_time"] = trim($data["date_time"]);
		$data["location"] = trim($data["location"]);
		$data["temperature"] = trim($data["temperature"]);
		$data["light"] = trim($data["light"]);
		$data["humidity"] = trim($data["humidity"]);
		return $data;
	}

	// Stores the data persistently on the base-station 
	// using a DataStorage object.
	public function storeAllValues($enoughSpace) {
		$dataStorage = new DataStorage();
		$dataStorage->storeData($this->_ValuesArray, $enoughSpace);
	}
}
?>
