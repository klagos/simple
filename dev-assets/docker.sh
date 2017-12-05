#!/bin/bash

docker rm simple

if [ $? == 0 ];
then
    echo "no existe aún la maquina"	
fi


docker run -i -d -t --name simple -p 80:80  -v /home/simple/workspace/SIMPLE:/var/www/html  arkhotech/simple:1.1
