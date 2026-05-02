#!/bin/bash

LOG_FILE="/tmp/orion_deploy.log"
DATE=$(date '+%Y-%m-%d_%H:%M:%S')

ALIAS=$1
FOLDER=$2
DOMINIO=$3
REPOSITORIO=$4
RAMA=$5
DB_NAME=$6
DB_USERNAME=$7
DB_PASSWORD=$8

echo "[$DATE] INICIANDO DEPLOY DE SITIO '$DOMINIO'" >> $LOG_FILE
echo "Args:" >> $LOG_FILE
echo "Alias: $ALIAS" >> $LOG_FILE
echo "Folder: $FOLDER" >> $LOG_FILE
echo "Dominio: $DOMINIO" >> $LOG_FILE
echo "Repositorio: $REPOSITORIO" >> $LOG_FILE
echo "Rama: $RAMA" >> $LOG_FILE
echo "DB Name: $DB_NAME" >> $LOG_FILE
echo "DB Username: $DB_USERNAME" >> $LOG_FILE
echo "DB Password: $DB_PASSWORD" >> $LOG_FILE

ESPERA=$(( ( RANDOM % 11 ) + 10 ))
echo "Simulando proceso de red... esperando $ESPERA segundos." >> $LOG_FILE
sleep $ESPERA
echo "[$DATE] DEPLOY FINALIZADO" >> $LOG_FILE
