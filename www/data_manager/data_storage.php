<?php

/*Responsible for access and handling of data persistent storage,
including storing new data and retrieving all the available data. */
class DataStorage
{
	private $JSONFileLocation = "../data.json";

	/*Decodes the json data read from the file on the disk, pushes the newly read
	value at the end of the object array, encodes the updated array into json,
	and finally overwrites the file with the updated json-formatted data. */
	public function storeData(array $valuesArray, $enoughSpace) {
		if(file_exists($this->JSONFileLocation)) {
			$allDataJSON = file_get_contents($this->JSONFileLocation);
			$allDataArray = json_decode($allDataJSON);
			unset($allDataJSON); //prevent memory leaks for large json.

			// We're out of disk space, remove the first data entry from the 
			// stored JSON file, so that we can add a new one at the end.
			if($enoughSpace == 0) {
				array_shift($allDataArray);
			}
			array_push($allDataArray, $valuesArray);
			$updatedDataJSON = json_encode($allDataArray);
			file_put_contents($this->JSONFileLocation, $updatedDataJSON);
			echo json_encode($valuesArray);
		}
		else { // Put the first array entry in the file.
			$newAllDataArray = array ($valuesArray);
			$tempJSONData = json_encode($newAllDataArray);
			file_put_contents($this->JSONFileLocation, $tempJSONData);
			echo "Created new storage file...\n";
			echo "Started toring newly gathered data...";
		}
	}

	public function fileExists() {
		if(file_exists($this->JSONFileLocation)) {
			return true;
		}
		return false;
	}

	// Get all the persistently stored data in JSON format.
	public function getAllData() {
		if(file_exists($this->JSONFileLocation)) {
			$allDataJSON = file_get_contents($this->JSONFileLocation);
			return $allDataJSON;
		}
		else {
			echo "\n Error: Data file does not exist.";
		}
	}

	// Delete the file with all the persistently stored data in JSON format.
	public function deleteAllData() {
		if(file_exists($this->JSONFileLocation)) {
			unlink($this->JSONFileLocation);
		}
		else {
			echo "\n Error: Data file does not exist.";
		}
	}

	// Get the name of the file that stores the data.
	public function getJSONFileName() {
		if(file_exists($this->JSONFileLocation)) {
			return $this->JSONFileLocation;
		}
		else {
			echo "\n Error: Data file does not exist.";
		}
	}
}
?>
