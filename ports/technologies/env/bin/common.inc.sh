#!/bin/sh

if [ -z $_XP_INCLUDE_PATH ]; then
  export _XP_INCLUDE_PATH=`grep ^include_path /etc/php/cli-php5/php.ini | cut -d '"' -f 2`
fi

_XP_IDE_DIR=$(dirname $0)
