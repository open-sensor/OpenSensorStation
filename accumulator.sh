#!/bin/ash

# Script that was added to the linux init scripts after the web server that
# performs data aggregation by calling php fast-cgi scripts every 10 minutes. 
# Includes a disk space check to pass as parameter to the php script to 
# perform the data aggregation in a recycling way. It also updates the cached
# sensor list file that is used for dynamic identification of available sensors.
while true
do
	# Get the available space left in the usb partition in MB unit.
	availableSpace= df -m /dev/sda1 | sed -n 2p | awk '{print $4}'
	
	# update the sensor list
	php-fcgi /srv/www/sensorlist_updater.php
	sleep 5

	# If the available space is less than 5MB
	if [$availableSpace < 5]; then
		php-fcgi /srv/www/aggregator.php enoughSpace=0
	else
		php-fcgi /srv/www/aggregator.php enoughSpace=1
	fi
	
	# sleep for 10 minutes
	sleep 595
done
