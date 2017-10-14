#!/usr/bin/env bash

CALLEDPATH=`dirname $0`

# Convert to an absolute path if necessary
case "$CALLEDPATH" in
  .*)
    CALLEDPATH="$PWD/$CALLEDPATH"
    ;;
esac

if [ ! -f "$CALLEDPATH/setup.conf" ]; then
  echo
  echo "Missing configuration file. Please copy $CALLEDPATH/setup.conf.txt to $CALLEDPATH/setup.conf and edit it."
  exit 1
fi

source "$CALLEDPATH/setup.conf"

cp $CIVIROOT/xml/schema/Schema.xml $CIVIROOT/xml/schema/Schema.xml.backup

# append Volunteer schema to core schema
sed -i 's#</database>##' "$CIVIROOT/xml/schema/Schema.xml"
grep "<xi:include" "$EXTROOT/xml/schema/Schema.xml" >> "$CIVIROOT/xml/schema/Schema.xml"
echo "</database>" >> "$CIVIROOT/xml/schema/Schema.xml"

if [ ! -e "$CIVIROOT/xml/schema/$SCHEMADIR" ] ; then
  ln -s $EXTROOT/xml/schema/$SCHEMADIR $CIVIROOT/xml/schema/$SCHEMADIR
fi
cd $CIVIROOT/xml
php GenCode.php
# (There may be extra arguments to pass into GenCode.php; not sure)

# Create extension DAO directory
if [ ! -d "$EXTROOT/CRM/$SCHEMADIR/DAO/" ]; then
  mkdir -p "$EXTROOT/CRM/$SCHEMADIR/DAO/"
fi
# Copy generated DAO files to extension DAO directory
cp -f $CIVIROOT/CRM/$SCHEMADIR/DAO/* $EXTROOT/CRM/$SCHEMADIR/DAO/
mv $CIVIROOT/xml/schema/Schema.xml.backup $CIVIROOT/xml/schema/Schema.xml

# Create empty sql files if they don't already exist
if [ ! -f "$EXTROOT/sql/auto_install.sql" ]; then
  touch "$EXTROOT/sql/auto_install.sql"
fi
if [ ! -f "$EXTROOT/sql/auto_uninstall.sql" ]; then
  touch "$EXTROOT/sql/auto_uninstall.sql"
fi

# FIXME: Automatically copy SQL from civicrm.sql to auto_*.sql

echo
echo "Copy the DROP TABLE query from $CIVIROOT/sql/civicrm.sql into:"
echo "$EXTROOT/sql/auto_install.sql"
echo "$EXTROOT/sql/auto_uninstall.sql"
read -p " - Press Enter when Done - "

echo
echo "Copy the CREATE TABLE query from $CIVIROOT/sql/civicrm.sql into:"
echo "$EXTROOT/sql/auto_install.sql"
read -p " - Press Enter when Done - "

## Cleanup
unlink $CIVIROOT/xml/schema/$SCHEMADIR
rm -rf $CIVIROOT/CRM/$SCHEMADIR

# Cleanup civi install
cd $CIVICRM
git clean -df && git checkout -- .
cd $CIVIROOT/xml
php GenCode.php
cd $EXTROOT

# Make sure sql files are picked up
civix generate:upgrader
