#! /bin/bash

if [ "$(ps -p `cat /tmp/phud.lock` | wc -l)" -gt 1 ]; then
	echo "Phud already running"
	exit 0
else
	echo "Starting run"
	echo $$ > /tmp/phud.lock
	/usr/bin/php /home/dan/phud-git/game.php
	rm /tmp/phud.lock
	exit 0
fi
