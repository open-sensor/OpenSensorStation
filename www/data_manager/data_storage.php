<?php

/*Responsible for access and handling of data persistent storage,
* including storing new data and retrieving all the available data. 
* author: Nikos Moumoulidis
*/
class DataStorage
{
	private $JSONFileLocation = "../data.json";

	/* Implements a good-enough efficiency algorithm for parsing through the JSON persistent storage
	* file and perform I/O operations on it. Takes into account paremeters such as: 
	* - does the file already exist or not.
	* - is there enough space on the disk or not (and recycle data if not).
	* while also performing JSON validation upon encoding/decoding it from/to PHP data structures,
	* using the '@' prefix to suppress errors/warnings and check on the validity of the encoding/decoding
	* operation. Invalid json objects within the just-read file data are removed as soon as found, and 
	* invalid just-encoded json objects are not inserted as soon as found, resulting in robust data 
	* validation.
	*/
	public function storeData(array $valuesArray, $enoughSpace) {

		// **************************************** The file exists ****************************************
		if(file_exists($this->JSONFileLocation)) {

			if($enoughSpace == 0) {
			// If there is no more space on the disk, batch read/overwrite of the
			// file is performed, so robust data validation is not an option.
				$allDataJSON = file_get_contents($this->JSONFileLocation);
				$allDataArray = json_decode($allDataJSON);
				unset($allDataJSON); //prevent memory leaks for large json.

				// We're out of disk space, remove the first data entry from the 
				// stored JSON file, so that we can add a new one at the end.
				array_shift($allDataArray);
				
				array_push($allDataArray, $valuesArray);
				$updatedDataJSON = json_encode($allDataArray);
				file_put_contents($this->JSONFileLocation, $updatedDataJSON);
				echo "No disk space available: Removing first data entry & adding new one...\n";
			}
			else {
				// Open the file with pointer at Start of file.
			$fHandle = fopen($this->JSONFileLocation, 'r+') or die("Can't open JSON file.\n");

			// Read the whole thing.
			$dataString = "";
			while (!feof($fHandle)) {
			   $dataString .= fgets($fHandle);
			}

			// Check if it is valid JSON by decoding it (the @ suppresses the error).
			$phpArrayOfArrays = @json_decode($dataString);

			$noDataInFile = false;

			// ==================== The stored JSON file was Unsuccessfully decoded =========================
			if($phpArrayOfArrays == false) {
				echo "Bad-JSON Error: error when decoding the JSON file data...\n";
				echo "Removing the last (invalid) JSON object.";
				$posOfLastJSONObjectStart = strrpos($dataString,"{");
				ftruncate($fHandle, $posOfLastJSONObjectStart-1);

				fseek($fHandle, 0, SEEK_END);
				if(ftell($fHandle) < 2) {
					$noDataInFile = true;
				}

				// Try to encode the new values array into JSON (the @ suppresses the error).
				$newJSONvalue = @json_encode($valuesArray);

				// ---------------- The new values array was unsuccessfully encoded into JSON... -------------
				if($newJSONvalue == false) {
					echo "Bad-JSON Error: error when encoding new values array to JSON...\n";
					if($noDataInFile == true) {
						fclose($fHandle);
						unlink($this->JSONFileLocation);
						echo "No data found in the file... Deleting the file...";
					}
					else {
						fwrite($fHandle, "]");
						fclose($fHandle);
						echo "Closing the file...";
					}
				}
				// ----------------- The new values array was successfully encoded into JSON... --------------
				else {
					if($noDataInFile == true) {
						ftruncate($fHandle, 0);
						fseek($fHandle, 0, SEEK_END);
						$newJSONvalue = "[" . $newJSONvalue . "]";
					}
					else {
						fseek($fHandle, 0, SEEK_END);
						$newJSONvalue = "," . $newJSONvalue . "]";
					}
					fwrite($fHandle, $newJSONvalue);
					fclose($fHandle);
					echo "Storing new JSON object value...";
				}
			}
			// ==================== The stored JSON file was successfully decoded =========================
			else {
				// Try to encode the new values array into JSON (the @ suppresses the error).
				$newJSONvalue = @json_encode($valuesArray);

				// If encoding failed, don't write the value to file, just close it...
				// ---------------- The new values array was unsuccessfully encoded into JSON... -------------
				if($newJSONvalue == false) {
						fclose($fHandle);
						echo "Bad-JSON Error: error when encoding new values array to JSON...\n";
						echo "Closing the file...";
				}
				// ----------------- The new values array was successfully encoded into JSON... --------------
				else {
					$posOfLastJSONObjectStart = strrpos($dataString,"]");
					ftruncate($fHandle, $posOfLastJSONObjectStart);
					fseek($fHandle, 0, SEEK_END);
					$newJSONvalue = "," . $newJSONvalue . "]";
					fwrite($fHandle, $newJSONvalue);
					fclose($fHandle);
					echo "Storing new JSON object value...";
				}
			}
			}
			
		} 
		// ******************* No file exists -> Create it with the new data *******************
		else { // Put the first array entry in the file.
			$newAllDataArray = array ($valuesArray);
			$tempJSONData = json_encode($newAllDataArray);
			file_put_contents($this->JSONFileLocation, $tempJSONData);
			echo "Created new storage file...\n";
			echo "Started storing newly gathered data...";
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
