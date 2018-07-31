#!/bin/bash

# On Windows if we mount the file directly it will end up having 777 permissions and mysql won't read the config
# So instead mount to a temporary directory and copy rom there, then chmoding to 0444
rm -rf /etc/mysql/conf.d/*.cnf
cp /tmp/mwdd/slave.cnf /etc/mysql/conf.d/slave.cnf
chmod 0444 /etc/mysql/conf.d/slave.cnf

# Then execute the regular mysql / mariadb entrypoint
/usr/local/bin/docker-entrypoint.sh $@
