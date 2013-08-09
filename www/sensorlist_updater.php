<?php
include 'sensor_interface/interface_tmote.php';

/**
* Called by the accumulator.sh script, uses an InterfaceSf object
* to update the cached sensor list file.
* author: Nikos Moumoulidis
*/
$serialServerInterface = new InterfaceSf();
$serialServerInterface->updateCommandList();
unset($serialServerInterface);
?>
