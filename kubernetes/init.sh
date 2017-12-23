#!/bin/bash

declare WORKDIR=$(cd $(dirname $0)/.. && pwd);

if [[ "x${DOCKER_MW_PATH}" == "x" ]]; then
  echo "You must specify DOCKER_MW_PATH env variable"
  exit 1;
fi

minikube mount ${DOCKER_MW_PATH}:/var/www/mediawiki &
