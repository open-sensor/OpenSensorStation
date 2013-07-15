<?php
include 'sensor_interface/interface_tmote.php';
include 'data_storage.php';

// Used as an "enum" to identify the type of server.
abstract class ServerType
{
	const SERIAL = 0;
	const COMMAND = 1;
}

/* This class is used by the aggregation script, and the REST-styled api respectively, 
providing them data access and management in a structured way, abstracting away the data 
communication and storage details performed by the server interface and data storage classes. */
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

	// Is used by the REST-api for dynamic command recognition.
	public function getSerialCommandList() {
		return $this->_SerialServer->getCommandList();
	}

	public function getSensorList() {
		return json_encode($this->_SerialServer->getCommandList());
	}

	/* Reads a single value from the server specified, acting as a single point of
	data reading from the REST-styled API. The value types can be standard:
	'location', 'set location *', 'datetime', 'status', or they can be from the
	list of available sensors, e.g.: 'humid', 'light', 'temp'. */
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

	// Used by the aggregator.php script to get request sensor data and contextual data,
	// and structure them in the form of, e.g.: { datetime, location, temp, humid, light }.
	public function readAllValues() {
		// Read contextual data (date/time and location).
		$dateTime = $this->queryServerDateTime();
		$location = $this->queryServerLocation();
		$data = array("datetime" => $dateTime, "location" => $location);

		// Read actual data from all the available sensors.
		$listOfCommands = $this->_SerialServer->getCommandList();
		foreach ($listOfCommands as $command) {
			$value = $this->_SerialServer->queryServer($command);
			$data[$command] = $value;
		}
		$this->_ValuesArray = $data;
	}

	// Gets all the stored data in JSON format.
	public function getAllData() {
		return $this->_DataStorage->getAllData();
	}

	// Deletes the all the persistently stored data in JSON format.
	public function deleteAllData() {
		$this->_DataStorage->deleteAllData();
	}

	// Set the sensor's location.
	public function setServerLocation($newlocation) {
		$this->_CommandServer->queryServer("set location ".$newlocation);
	}

	// Abstracts away the extraction of location from the "status" output of the command server.
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

	// Get date and time of the system (base-station) in an appropriate format.
	private function queryServerDateTime() {
		return date("d-m-Y h:i:s", time());
	}

	// Stores the data persistently on the base-station using a DataStorage object.
	public function storeAllValues($enoughSpace) {
		$this->_DataStorage->storeData($this->_ValuesArray, $enoughSpace);
	}

	public function getDataStorage() {
		return $this->_DataStorage;
	}

	function __destruct() {
		unset($this->_SerialServer);
		unset($this->_CommandServer);
		unset($this->_DataStorage);
	}
}
?>
