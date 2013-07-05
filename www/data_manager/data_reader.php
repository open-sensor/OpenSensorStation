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
	private $_ValuesArray = null;
	private $_SerialServer = null;
	private $_CommandServer = null;
	private $_DataStorage = null;

	function __construct() {
		$this->_SerialServer = new InterfaceSf();
		$this->_CommandServer = new InterfaceCmd();
		$this->_DataStorage = new DataStorage();
    	}

	// To be used by the REST-api for dynamic command recognition.
	public function getSerialCommandList() {
		return $this->_SerialServer->getCommandList();
	}

	// Reads a single value from the server specified, acting as a single point of
	// data reading from the REST-styled API. The value types can be any standard:
	// 'location', 'set location *', 'datetime', 'status', or they can be from the
	// list of available sensors, e.g.: 'humid', 'light', 'temp'.
	public function getSingleValue($valueType, $srvType) {
		if ($srvType == ServerType::SERIAL) {
			return $this->_SerialServer->queryServer($valueType);
		}
		else if ($srvType == ServerType::COMMAND) {
			if($valueType == "location") {
				return $this->queryServerLocation();
			}
			else if($valueType == "datetime") {
				return $this->queryServerDateTime();
			}
			else if ($valueType == "status"){
				return $this->_CommandServer->queryServer($valueType);
			}
		}
		else {
			echo "\n Error: Invalid server type specified.";
		}
	}

	// Gets all the stored data in JSON format.
	public function getAllData() {
		return $this->_DataStorage->getAllData();
	}

	// Used by the aggregator.php script to get real-time readings.
	public function readAllValues() {
		// Read contextual data (date/time and location).
		$dateTime = $this->queryServerDateTime();
		$location = $this->queryServerLocation();
		$data = array("datetime" => $dateTime, "location" => $location);

		// Read actual data from all the available sensors.
		$listOfCommands = $this->_SerialServer->getCommandList();
		foreach ($listOfCommands as $command) {
			$value = $this->_SerialServer->queryServer($command);
			// Handling bug caused by unresolved memory leak.
			if($value == "temp humid light") {
				$value = "";
			}
			$value = trim($value);

			$data[$command] = $value;
		}
		$this->_ValuesArray = $data;
	}

	// Set the sensor's location.
	public function setServerLocation($newlocation) {
		$this->_CommandServer->queryServer("set location ".$newlocation);
	}

	// Abstracts away the extraction of location from the status server reading.
	private function queryServerLocation() {
		// Read the mote's status from the command server.
		$status = $this->_CommandServer->queryServer("status");

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

	// Get date and time in an appropriate format.
	private function queryServerDateTime() {
		return date("d-m-Y h:i:s", time());
	}

	// Stores the data persistently on the base-station 
	// using a DataStorage object.
	public function storeAllValues($enoughSpace) {
		$this->_DataStorage->storeData($this->_ValuesArray, $enoughSpace);
	}

	function __destruct() {
		unset($this->_SerialServer);
		unset($this->_CommandServer);
		unset($this->_DataStorage);
	}
}
?>
