<?php
include 'data_manager/data_reader.php';

$dataReader = new DataReader();
$dataReader->readAllValues();
$dataReader->storeAllValues();
?>
