#!/bin/sh
# $Id$

SYSTEM=`env | grep OS | grep -i WINDOWS | wc -l`

JAY_BIN=../jay/parser_generator/phpJay

[ 0 -lt $SYSTEM ] && {
  JAY_BIN=../jay/parser_generator/phpJay.exe
}

[ ! -x $JAY_BIN ] && {
  echo "Jay binary not found, be sure to have it created.";
  echo "Example: cd parser_generator && make && cd ..";
  exit 1;
}

$JAY_BIN -cv -g _PHP_PARSER < ../jay/parser_generator/skeleton.xp FQL.jay | sed -e 's/{%NAME%}/FQL/g' >  FQLParser.class.php

