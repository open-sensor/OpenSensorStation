#!/bin/ash
while true
do
	# Get the available space left in the usb partition in MB unit.
	availableSpace= df -m /dev/sda1 | sed -n 2p | awk '{print $4}'
	
	# update the sensor list
	php-fcgi www/sensorlist_updater.php
	sleep 5

	# If the available space is less than 5MB
	if [$availableSpace < 5]; then
		php-fcgi www/aggregator.php enoughSpace=0
	else
		php-fcgi www/aggregator.php enoughSpace=1
	fi
	
	# sleep for 10 minutes
	sleep 5
done
