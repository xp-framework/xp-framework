#!/bin/sh

if [ 2 -gt $# ]; then
  echo "Usage: $0 file.glade language [language [language [...]]]"
  exit
fi

if [ ! -f $1 ] ; then
  echo "$1 is not a file"
fi

TMP="/tmp/"`basename $1`
TMPLANG=$TMP".in"
echo "===> Building `basename $1` in `dirname $1`"

echo '<?xml version="1.0" encoding="iso-8859-1" ?>' > $TMPLANG
echo '<languages>' >> $TMPLANG
echo '  <language name="C"/>' >> $TMPLANG
for i in $* ; do 
  if [ "$1" != "$i" ] ; then
    echo "     >> Language $i"
    echo '  <language name="'$i'"/>' >> $TMPLANG
  fi
done
echo '</languages>' >> $TMPLANG

cat $1 | sed -e 's/\<\?xml version="1.0"\?\>/<?xml version="1.0" encoding="iso-8859-1" ?>/g' > $TMP

SOURCES=`echo $1|sed -e 's/\.glade$//g'`
echo "---> Extracting, results will go to `basename $SOURCES.i18n.xml`"

sabcmd "`dirname $0`/make-i18n.xsl" $TMP "$SOURCES.i18n.xml" \$glade=`basename $1` \$sources=$TMPLANG || {
  echo "*** Build error, stop"
  rm $TMP
  rm $TMPLANG
  exit 
}

rm $TMP
rm $TMPLANG
echo "---> Extraction complete. The following file has been created:"
ls -l "$SOURCES.i18n.xml"
echo "===> Done"
