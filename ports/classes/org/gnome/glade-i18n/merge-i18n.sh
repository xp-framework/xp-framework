#!/bin/sh

if [ 2 != $# ]; then
  echo "Usage: $0 file.glade language"
  exit
fi

if [ ! -f $1 ] ; then
  echo "$1 is not a file"
fi

TMP="/tmp/"`basename $1`

echo "===> Building `basename $1` in `dirname $1` for language $2"
cat $1 | sed -e 's/\<\?xml version="1.0"\?\>/<?xml version="1.0" encoding="iso-8859-1" ?>/g' > $TMP

SOURCES=`echo $1|sed -e 's/\.glade$//g'`
echo "---> Using `basename $SOURCES.i18n.xml`, results will go to `basename $SOURCES.$2.glade`"

sabcmd "`dirname $0`/merge-i18n.xsl" $TMP "$SOURCES.$2.glade" \$language=$2 \$sources=$SOURCES || {
  echo "*** Build error, stop"
  rm $TMP
  exit 
}

rm $TMP
echo "---> Build complete. The following file has been created:"
ls -l "$SOURCES.$2.glade"
echo "===> Done"
