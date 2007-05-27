#!/bin/sh

SELF=$(realpath $0)
BASE=$(dirname $SELF)

. $BASE/common.inc.sh

for i in "$@"; do
  IFS=$_XP_PATH_SEPARATOR;
  for DIR in $_XP_INCLUDE_PATH; do
    FILENAME=`echo $i | tr . /`.class.php
    if [ -f "$DIR/$FILENAME" ]; then
      echo $(unixpath "$DIR/$FILENAME");
      exit 0;
    fi
    
    SAPINAME=`echo $i | tr . /`.sapi.php
    if [ -f "$DIR/sapi/$SAPINAME" ]; then
      echo $(unixpath "$DIR/sapi/$SAPINAME");
      exit 0;
    fi
  done
    
  echo "Could not find '$i'"
done

exit 1;
