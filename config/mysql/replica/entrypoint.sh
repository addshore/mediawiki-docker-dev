#!/bin/bash

# On Windows if we mount the file directly it will end up having 777 permissions and mysql won't read the config
# So instead mount to a temporary directory and copy from there, then chmoding to 0444
rm -rf /etc/mysql/conf.d/*.cnf
cp /mwdd-custom/replica.cnf /etc/mysql/conf.d/replica.cnf
chmod 0444 /etc/mysql/conf.d/replica.cnf

# Then execute the regular mysql / mariadb entrypoint
/usr/local/bin/docker-entrypoint.sh $@
