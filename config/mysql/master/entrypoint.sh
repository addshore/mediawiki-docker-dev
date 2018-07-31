#!/bin/bash

rm -rf /etc/mysql/conf.d/*.cnf
cp /tmp/mwdd/master.cnf /etc/mysql/conf.d/master.cnf
chmod 0444 /etc/mysql/conf.d/master.cnf

/usr/local/bin/docker-entrypoint.sh $@
