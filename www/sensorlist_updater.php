<?php
include 'sensor_interface/interface_tmote.php';

$serialServerInterface = new InterfaceSf();
$serialServerInterface->updateCommandList();
unset($serialServerInterface);
?>
