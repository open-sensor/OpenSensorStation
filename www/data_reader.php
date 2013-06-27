<?php
include 'data_interface/interface_tmote.php';

$dataReader = new DataReader();
$dataReader->getReading();
//$dataReader->displayCurrentReading();

class DataReader
{
	private $_SerialServer;
	private $_CommandServer;

	private $_DateTime;
	private $_Location;
	private $_Temperature;
	private $_Light;
	private $_Humidity;
	private $_Status;

	private $nonAssocArray;
	private static $JSONFileLocation = "../data.json";

	public function __construct() {
		$this->_DateTime = date("d/m/Y h:i:s", time());
    	}

	// Master-function handling a single reading by calling the private functions appropriately.
	public function getReading() {
		$this->querySerialServer();
		$this->queryServerLocation();
		$this->trimData();
		$this->storeToJSON();
	}

	// Read data from Serial Forwarder Server.
	private function querySerialServer() {
		$this->_SerialServer = new InterfaceSf();
		$this->_Temperature = $this->_SerialServer->readSensor("temp");
		$this->_Humidity = $this->_SerialServer->readSensor("humid");
		$this->_Light = $this->_SerialServer->readSensor("light");
	}

	private function queryServerLocation() {
		// Read the mote's status from the command server.
		$this->_CommandServer = new InterfaceCmd();
		$this->_Status = $this->_CommandServer->readSensor("status");

		// The location needs to be extracted from the status output.
		// Find the index position of the "Location:" line in the status output string.
		if (strpos($this->_Status,'Location:') !== false) {
	    		$index = strpos($this->_Status,'Location:');
			$index += 10;		// Add 10 to the index, which is the length of "Location: "
						// in order to arrive to the index where the location data
						// starts within the status output string.
		}
		// The location is the last piece of data from the status output, and this is why retrieve
		// the subtstring from the previously identified index and until the end of the string of the status.
		$this->_Location = substr($this->_Status, $index);
	}

	// Remove \r \n and other such characters.
	private function trimData() {
		$this->_DateTime = trim($this->_DateTime);
		$this->_Location = trim($this->_Location);
		$this->_Temperature = trim($this->_Temperature);
		$this->_Light = trim($this->_Light);
		$this->_Humidity = trim($this->_Humidity);
	}

	private function storeToJSON() {
		// Create single data entry array.
		$reading = array( "date_time" => $this->_DateTime, "location" => $this->_Location, 
			"temperature" => $this->_Temperature, "light" => $this->_Light, "humidity" => $this->_Humidity );

		// Read the persistent .json file, decode it into a PHP array, append the new array reading,
		// encode the newly updated array into json format, and finally write the data back to the json file.
		if(file_exists(self::$JSONFileLocation)) {
			$allDataJSON = file_get_contents(self::$JSONFileLocation);
			$allDataArray = json_decode($allDataJSON);
			unset($allDataJSON); //prevent memory leaks for large json.

			array_push($allDataArray, $reading);
			$updatedDataJSON = json_encode($allDataArray);
			file_put_contents(self::$JSONFileLocation, $updatedDataJSON);
			echo $updatedDataJSON;
		}
		else { // Put the first array entry in the file.
			$newAllDataArray = array ($reading);
			$tempJSONData = json_encode($newAllDataArray);
			file_put_contents(self::$JSONFileLocation, $tempJSONData);
		}
	}

	public function displayCurrentReading() {
		echo "Date/Time: ".$this->_DateTime."\n\n";
		echo "Location: ".$this->_Location."\n";
		echo "Temperature (Celsius): ".$this->_Temperature."\n";
		echo "Light (Lux): ".$this->_Light."\n";
		echo "Humidity (%): ".$this->_Humidity."\n";
	}
}
?>
