<?php
include 'interface_tmote.php';

$cliReader = new CliReader();
$cliReader->readServerLocation();
$cliReader->readSerialServer();
$cliReader->encodeToJSON();
//$cliReader->showSingleReading();
$cliReader->showSingleJSONArray();

class CliReader
{
	private $_SerialServer;
	private $_CommandServer;
	private $_DateTime;
	private $_Location;
	private $_Temperature;
	private $_Light;
	private $_Humidity;
	private $_Status;
	private $_SingleJSONArray;

	public function __construct() {
		$this->_DateTime = date("d/m/Y h:i:s", time());
    	}

	public function encodeToJSON() {
		$reading[] = array( "date_time" => $this->_DateTime, "location" => $this->_Location, 
			"temperature" => $this->_Temperature, "light" => $this->_Light, "humidity" => $this->_Humidity );
		$this->_SingleJSONArray = json_encode($reading);
	}

	public function readServerLocation() {
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

	// Read data from Serial Forwarder Server.
	public function readSerialServer() {
		$this->_SerialServer = new InterfaceSf();
		$this->_Temperature = $this->_SerialServer->readSensor("temp");
		$this->_Humidity = $this->_SerialServer->readSensor("humid");
		$this->_Light = $this->_SerialServer->readSensor("light");

		$this->trimData();
	}

	// Remove \r \n and other such characters.
	private function trimData() {
		$this->_DateTime = trim($this->_DateTime);
		$this->_Location = trim($this->_Location);
		$this->_Temperature = trim($this->_Temperature);
		$this->_Light = trim($this->_Light);
		$this->_Humidity = trim($this->_Humidity);
	}

	public function showSingleReading() {
		echo "Date/Time: ".$this->_DateTime."\n\n";
		echo "Location: ".$this->_Location."\n";
		echo "Temperature (Celsius): ".$this->_Temperature."\n";
		echo "Light (Lux): ".$this->_Light."\n";
		echo "Humidity (%): ".$this->_Humidity."\n";
	}

	public function showSingleJSONArray() {
		echo $this->_SingleJSONArray;
	}
}
?>
