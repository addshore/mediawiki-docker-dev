#!/bin/bash
# Modified from http://tarunlalwani.com/post/mysql-master-replica-using-docker/

echo "Waiting for mysql to start"
# Give 60 seconds for master and replica to start
# sleep 60
/tmp/wait-for-it.sh  db-master:3306
/tmp/wait-for-it.sh  db-replica:3306
# Double check
/tmp/wait-for-it.sh  db-master:3306
/tmp/wait-for-it.sh  db-replica:3306

echo "Create MySQL Servers (master / replica repl)"
echo "-----------------"


echo "* Create replication user"

mysql --host db-replica -uroot -p$MYSQL_REPLICA_PASSWORD -AN -e 'STOP SLAVE;';
mysql --host db-replica -uroot -p$MYSQL_MASTER_PASSWORD -AN -e 'RESET SLAVE ALL;';

mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e "CREATE USER '$MYSQL_REPLICATION_USER'@'%';"
mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e "GRANT REPLICATION SLAVE ON *.* TO '$MYSQL_REPLICATION_USER'@'%' IDENTIFIED BY '$MYSQL_REPLICATION_PASSWORD';"
mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -AN -e 'flush privileges;'


echo "* Set MySQL01 as master on MySQL02"

MYSQL01_Position=$(eval "mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -e 'show master status \G' | grep Position | sed -n -e 's/^.*: //p'")
MYSQL01_File=$(eval "mysql --host db-master -uroot -p$MYSQL_MASTER_PASSWORD -e 'show master status \G'     | grep File     | sed -n -e 's/^.*: //p'")
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
