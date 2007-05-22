#!/bin/sh

BASE=$(dirname $0)

. $BASE/common.inc.sh

for i in "$@"; do
  IFS=':'
  for DIR in $PHP_INCLUDE_PATH; do
    FILENAME=`echo $i | tr . /`.class.php
    if [ -f $DIR/$FILENAME ]; then
      echo $DIR/$FILENAME;
      exit 0;
    fi
    
    SAPINAME=`echo $i | tr . /`.sapi.php
    if [ -f $DIR/sapi/$SAPINAME ]; then
      echo $DIR/sapi/$SAPINAME;
      exit 0;
    fi
  done
done

exit 1;
