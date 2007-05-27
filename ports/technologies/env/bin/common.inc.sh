#!/bin/sh
# common.inc.sh
#
# This file provides a base for all scripts in env/bin that automatically
# adjusts itself to the operating system used and the environment
# it is executed in.

unixpath () {
  local FILE=$1
  
  if [ "Windows_NT" = "$OS" ]; then
    FILE=`cygpath -u "$FILE"`
  fi
  
  echo $FILE;
}

# Initialize XP_PHP_INI with default value
if [ -z $XP_PHP_INI ]; then
  XP_PHP_INI="/etc/php/php5-cli/php.ini"
fi

if [ -z $_XP_INCLUDE_PATH ]; then
  if [ ! -r $XP_PHP_INI ]; then
    echo "Cannot find php.ini, please provide path by the environment variable \$XP_PHP_INI";
    exit 1;
  fi
  
  export _XP_INCLUDE_PATH=`grep ^include_path "$XP_PHP_INI" | cut -d '"' -f 2`
fi

SELF=`realpath "$0"`
_XP_IDE_DIR=`dirname "$SELF"`
_XP_PATH_SEPARATOR=':'

if [ "Windows_NT" = "$OS" ]; then
  _XP_PATH_SEPARATOR=';'
fi
