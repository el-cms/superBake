#!/bin/sh
cache=app/tmp/cache/
logs=app/tmp/logs

sudo find $cache -type f -print0 | xargs -r -0 rm
echo "Cache files removed"

sudo rm $logs/error.log
sudo rm $logs/debug.log
echo "Logs removed"

sudo touch $logs/error.log
sudo touch $logs/debug.log
echo "empty logs created"

sudo chown www-data $logs/*.log
echo "Permissions on logs given to apache webserver"
