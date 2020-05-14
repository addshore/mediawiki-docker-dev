#!/bin/bash
# Modified from https://tarunlalwani.com/post/mysql-master-slave-using-docker/

position_file=/mwdd-connector/master_position
file_file=/mwdd-connector/master_file

# Only save the data if the files don't already exist
# They might have been created during another container startup
if [ ! -e "$position_file" ]; then
    echo "Position file doesnt exist, can't start replication"
    exit 1
fi

echo "Waiting for mysql replica to start"
/mwdd-scripts/wait-for-it.sh  db-master:3306
/mwdd-scripts/wait-for-it.sh  db-replica:3306
# Double check
/mwdd-scripts/wait-for-it.sh  db-master:3306
/mwdd-scripts/wait-for-it.sh  db-replica:3306

echo "* Create replication user"

mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -AN -e 'STOP SLAVE;';
mysql --host db-replica -uroot -p$MYSQL_MASTER_PASSWORD -AN -e 'RESET SLAVE ALL;';

mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e "CREATE USER '$MYSQL_REPLICATION_USER'@'%';"
mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e "GRANT REPLICATION SLAVE ON *.* TO '$MYSQL_REPLICATION_USER'@'%' IDENTIFIED BY '$MYSQL_REPLICATION_PASSWORD';"
mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e 'flush privileges;'


echo "* Set MySQL01 as master on MySQL02"

# Grab the position that should have been set from the first step of db-configure when the master was created
MYSQL01_Position=$(<$position_file)
MYSQL01_File=$(<$file_file)

MASTER_IP=$(eval "getent hosts db-master|awk '{print \$1}'")
echo $MASTER_IP
mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -AN -e "CHANGE MASTER TO master_host='db-master', master_port=3306, \
        master_user='$MYSQL_REPLICATION_USER', master_password='$MYSQL_REPLICATION_PASSWORD', master_log_file='$MYSQL01_File', \
        master_log_pos=$MYSQL01_Position;"

echo "* Set MySQL02 as master on MySQL01"

MYSQL02_Position=$(eval "mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -e 'show master status \G' | grep Position | sed -n -e 's/^.*: //p'")
MYSQL02_File=$(eval "mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -e 'show master status \G'     | grep File     | sed -n -e 's/^.*: //p'")

REPLICA_IP=$(eval "getent hosts db-replica|awk '{print \$1}'")
echo $REPLICA_IP
mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e "CHANGE MASTER TO master_host='db-replica', master_port=3306, \
        master_user='$MYSQL_REPLICATION_USER', master_password='$MYSQL_REPLICATION_PASSWORD', master_log_file='$MYSQL02_File', \
        master_log_pos=$MYSQL02_Position;"

echo "* Start Replica on both Servers"
mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -AN -e "start slave;"

echo "Increase the max_connections to 1000"
mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e 'set GLOBAL max_connections=1000';
mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -AN -e 'set GLOBAL max_connections=1000';

mysql --host db-replica -uroot -p$MYSQL_MASTER_PASSWORD -e "show slave status \G"

echo "MySQL servers created!"
echo "--------------------"
echo
echo Variables available fo you :-
echo
echo MYSQL01_IP       : db-master
echo MYSQL02_IP       : db-replica
