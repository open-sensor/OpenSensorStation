<?php
class SensorDictionary
{
	private $_dictionary = array("temp", "humid", "light", 
				"press", "magn", "sound", "carbdx");
				
	
	public function isSensorListValid(array $sensorlist) {
		for($i=0 ; $i<sizeof($sensorlist) ; $i++) {
			if($sensorlist[$i] == "" || $sensorlist[$i] == null) {
				return false;
			}
			else {
				$isSensorInDictionary = false;
				for($j=0 ; $j < sizeof($this->_dictionary) ; $j++) {
					if($sensorlist[$i] == $this->_dictionary[$j]) {
						$isSensorInDictionary = true;
					}
				}
			
				if($isSensorInDictionary == false) {
					return false;
				}
			}
		}
		return true;
	}
}
?>
