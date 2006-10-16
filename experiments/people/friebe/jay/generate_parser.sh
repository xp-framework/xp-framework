#!/bin/sh
# $Id$

SYSTEM=`env | grep OS | grep -i WINDOWS | wc -l`
JAY_BIN=./parser_generator/phpJay

[ 0 -lt $SYSTEM ] && {
  JAY_BIN=./parser_generator/phpJay.exe
}

[ ! -x $JAY_BIN ] && {
  echo "Jay binary not found, be sure to have it created.";
  echo "Example: cd parser_generator && make && cd ..";
  exit 1;
}

$JAY_BIN -cv < parser_generator/skeleton.xp parser_generator/XP2.jay | sed -e 's/{%NAME%}//g' > net/xp_framework/tools/vm/Parser.class.php
