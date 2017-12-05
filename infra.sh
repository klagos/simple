#!/bin/bash

IMAGEN=egob/simple:v1.5
#IMAGEN=arkhotec/aloja

if [[ "$(docker images -q ${IMAGEN} 2> /dev/null)" == "" ]]; then
  # do something
	docker pull $IMAGEN
fi

#Construir la mauqina a partir de aca.

USER="-uroot -paloja123"
DBNAME=simple-db


if [ ! -f ~/.simple ]; then

   docker exec -t $DBNAME mysql $USER -e "create database IF NOT EXISTS simple"

   docker exec -t $DBNAME mysql $USER -e "grant all privileges on simple.* to 'simple'@'%' identified by 'simple' with grant option; flush privileges"

   docker exec -t $DBNAME mysql $USER simple < sql/estructura.sql

   docker exec -t $DBNAME mysql $USER simple < sql/datos.sql

   touch ~/.simple

fi


CODE_PATH=$('pwd')
CONTAINER_PATH=/var/www/simple

docker run -d -i -t --name simple-of \ 
       --hostname simpleof --link mysqlb:db \
	-v $CODE_PATH:$CONTAINER_PATH -p 9001:9000 egob/simple:v1.5 /bin/bash


