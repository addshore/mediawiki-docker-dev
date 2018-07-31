#!/bin/bash

rm -rf /etc/mysql/conf.d/*.cnf
cp /tmp/mwdd/slave.cnf /etc/mysql/conf.d/slave.cnf
chmod 0444 /etc/mysql/conf.d/slave.cnf

/usr/local/bin/docker-entrypoint.sh $@
