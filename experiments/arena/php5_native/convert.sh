#!/bin/sh

SKELETON_PATH=`realpath ../../../skeleton`

for i in `find $SKELETON_PATH -name '*.class.php' ! -path '*.svn*'`; do
  RELATIVE=`echo $i | sed -e 's#^.*/skeleton/##g'`
  RELPATH=`dirname $RELATIVE`
  RELNAME=`basename $RELATIVE`
  
  CLASSNAME=`echo $i | sed -e 's#^.*/skeleton/##g' | sed -e 's#\.class\.php##' | tr / .`
  
  if [ -f skeleton2/$RELPATH/$RELNAME ]; then
    continue;
  fi
  mkdir -p skeleton2/$RELPATH
  echo "$CLASSNAME => skeleton2/$RELPATH/$RELNAME"
  
  php migrate.php $CLASSNAME > skeleton2/$RELPATH/$RELNAME
  if [ 0 -ne $? ]; then 
    exit; 
  fi
done
