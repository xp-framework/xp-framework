#!/bin/sh
# $Id$

PHP=`which php || which php4`
PERL=`which perl`
XMLLINT=`which xmllint`

EXTENSION=`echo ${1##*.}`

case $EXTENSION in
  php | inc | php3 | php4)
    $PHP -l $1
    ;;
  
  pl | pm)
    $PERL -w -c $1
    ;;

  xml | xsl)
    $XMLLINT --noout $1 && echo `basename $1`" syntax OK"
    ;;

  *)
    echo "No syntax checker available for $EXTENSION"
    exit 1
    ;;

esac

exit $?
