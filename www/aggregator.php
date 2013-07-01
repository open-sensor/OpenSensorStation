<?php
include 'data_manager/data_reader.php';

$enoughSpace = $_GET["enoughSpace"];
// if enoughSpace=1: there is disk space for more data
// if enoughSpace=0: there is no disk space left
$dataReader = new DataReader();
$dataReader->readAllValues();
$dataReader->storeAllValues($enoughSpace);
?>
