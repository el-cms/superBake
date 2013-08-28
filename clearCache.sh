#!/bin/sh
cache=app/tmp/cache/
logs=app/tmp/logs

clear

echo "deleting chache files"
sudo find $cache -type f -print0 | xargs -r -0 rm

echo "resetting logs"
sudo rm $logs/error.log
sudo rm $logs/debug.log

sudo touch $logs/error.log
sudo touch $logs/debug.log

sudo chown www-data $logs/*.log