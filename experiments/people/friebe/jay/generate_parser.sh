#!/bin/sh
# $Id$

SYSTEM=`env | grep OS | grep -i WINDOWS | wc -l`

JAY_BIN=./phpJay

[ 0 -lt $SYSTEM ] && {
  JAY_BIN=./phpJay.exe
}
$JAY_BIN -cv -g _PHP_PARSER < xp-skeleton.in XP2.jay > Parser.php

