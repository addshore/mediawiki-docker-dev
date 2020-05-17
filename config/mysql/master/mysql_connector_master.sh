#!/bin/bash
# Split from https://tarunlalwani.com/post/mysql-master-slave-using-docker/
# This file grabs the position that we will want to start replication at and stores it in a file.
# This data is then used by the replica to start replicating

position_file=/mwdd-connector/master_position
file_file=/mwdd-connector/master_file

# Only save the data if the files don't already exist
# They might have been created during another container startup
if [ -e "$position_file" ]; then
    echo "Position file already exists"
    exit 0
fi

echo "Waiting for mysql master to start"
/wait-for-it.sh  db-master:3306
# Wait and double check
sleep 1
/wait-for-it.sh  db-master:3306

echo "* Get the binlog file and position"
MYSQL01_Position=$(eval "mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -e 'show master status \G' | grep Position | sed -n -e 's/^.*: //p'")
MYSQL01_File=$(eval "mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -e 'show master status \G'     | grep File     | sed -n -e 's/^.*: //p'")

echo "* Saving data to files"

echo $MYSQL01_Position > $position_file
echo $MYSQL01_File > $file_file
