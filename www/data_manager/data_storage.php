<?php
	
class DataStorage
{
	private static $JSONFileLocation = "../data.json";

	public function storeData(array $valuesArray) {
		if(file_exists(self::$JSONFileLocation)) {
			$allDataJSON = file_get_contents(self::$JSONFileLocation);
			$allDataArray = json_decode($allDataJSON);
			unset($allDataJSON); //prevent memory leaks for large json.

			array_push($allDataArray, $valuesArray);
			$updatedDataJSON = json_encode($allDataArray);
			file_put_contents(self::$JSONFileLocation, $updatedDataJSON);
			echo $updatedDataJSON;
		}
		else { // Put the first array entry in the file.
			$newAllDataArray = array ($valuesArray);
			$tempJSONData = json_encode($newAllDataArray);
			file_put_contents(self::$JSONFileLocation, $tempJSONData);
		}
	}
}
?>
