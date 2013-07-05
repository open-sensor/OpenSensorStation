<?php
	
class DataStorage
{
	private $JSONFileLocation = "../data.json";

	// Encodes the data reading into JSON format and appends it to the 
	// rest of the data stored persistently in a JSON file.
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
			echo $updatedDataJSON;
		}
		else { // Put the first array entry in the file.
			$newAllDataArray = array ($valuesArray);
			$tempJSONData = json_encode($newAllDataArray);
			file_put_contents($this->JSONFileLocation, $tempJSONData);
		}
	}

	// Get stored data in JSON format.
	public function getAllData() {
		if(file_exists($this->JSONFileLocation)) {
			$allDataJSON = file_get_contents($this->JSONFileLocation);
			return $allDataJSON;
		}
		else {
			echo "\n Error: Data file does not exist.";
		}
	}

	// Get the file name that stores the data.
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
