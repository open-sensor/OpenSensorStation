#!/bin/ash
while true
do
	# Get the available space left in the usb partition in MB unit.
	availableSpace= df -m /dev/sda1 | sed -n 2p | awk '{print $4}'

	# If the available space is less than 5MB
	if [$availableSpace < 5]; then
		rm data.json
	else
		php-cgi "www/data_reader.php"
	fi
	# sleep for 5 minutes
	sleep 300
done
