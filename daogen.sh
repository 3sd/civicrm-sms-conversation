#!/bin/bash

# Variables
SCHEMADIR=SmsConversation
CIVICRM=~/sites/chainedsms.cividev/sites/all/modules/civicrm
EXTENSION=~/sites/chainedsms.cividev/sites/default/files/civicrm/ext/civicrm-sms-conversations

# Copy schema files
mkdir "$CIVICRM/xml/schema/$SCHEMADIR"
cp ./xml/schema/CRM/"$SCHEMADIR"/*.xml "$CIVICRM/xml/schema/$SCHEMADIR"

# TODO: Autocreate the $SCHEMADIR/files.xml
read -p 'Make sure you have added schema files to xml/Schema.xml'
$CIVICRM/bin/setup.sh -g
cp $CIVICRM/CRM/$SCHEMADIR/DAO/*.php $EXTENSION/CRM/$SCHEMADIR/DAO/

read -p "Copy the DROP TABLE query into auto_install.sql and auto_uninstall.sql."
read -p "Copy the CREATE TABLE query into auto_install.sql."

# Cleanup civi install
cd $CIVICRM
git clean -df && git checkout -- .
cd $EXTENSION
$CIVICRM/bin/setup.sh -g

# Make sure sql files are picked up
civix generate:upgrader
