#!/bin/sh
# $Id$

FILE=$1

PHP=/usr/local/bin/php
PERL=/usr/bin/perl
XMLLINT=/usr/local/bin/xmllint

if [ `basename $FILE` != `basename -s php $FILE` ]; then
  $PHP -l $FILE
  exit
fi

if [ `basename $FILE` != `basename -s pl $FILE` ]; then
  $PERL -w -c $FILE
  exit
fi

if [ `basename $FILE` != `basename -s xsl $FILE` ]; then
  $XMLLINT $FILE 1>/dev/null
  exit
fi

if [ `basename $FILE` != `basename -s xml $FILE` ]; then
  $XMLLINT $FILE 1>/dev/null
  exit
fi

