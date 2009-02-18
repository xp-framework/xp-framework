#!/bin/sh

SELF=$(realpath $0)
BASE=$(dirname $SELF)
PROJECT_PATH=`realpath "$2"`

. $BASE/common.inc.sh

# Search for src/ dir in current project
while [ -n "$PROJECT_PATH" ]; do
  if [ -d "$PROJECT_PATH/src" ]; then _XP_INCLUDE_PATH="$_XP_INCLUDE_PATH$_XP_PATH_SEPARATOR$PROJECT_PATH/src"; fi
  PROJECT_PATH=$(dirname $PROJECT_PATH);
  if [ "/" = "$PROJECT_PATH" ]; then PROJECT_PATH=""; fi
done

for i in "$1"; do
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
