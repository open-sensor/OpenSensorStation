<?php
include 'data_manager/data_reader.php';

/*
This php script is used by the accumulator.sh shell script for 
performing requests to all the available sensors, gathering contextual data,
structuring them appropriatelly and storing them persistently.
*/

$enoughSpace = $_GET["enoughSpace"];
// if enoughSpace=1: there is disk space for more data
// if enoughSpace=0: there is no disk space left
$dataReader = new DataReader();
$dataReader->readAllValues();
$dataReader->storeAllValues($enoughSpace);
unset($dataReader);
?>
