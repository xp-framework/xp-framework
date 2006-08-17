#!/bin/sh

PHPFIVE_BINARY=`which php5 || which php`
PHPFIVE_ARGS="-dinclude_path=.:./skeleton2 -dmagic_quotes_gpc=0"
PHPFIVE_MIN_VER="5.1.0"

$PHPFIVE_BINARY -r "version_compare(phpversion(), '$PHPFIVE_MIN_VER', 'ge') || exit(1);"
if [ 0 != $? ] ; then
  echo "*** You must have at least PHP "$PHPFIVE_MIN_VER
  echo "*** Your php binary "$PHPFIVE_BINARY" reports the following:"
  echo '========================================================================'
  $PHPFIVE_BINARY -v
  echo '========================================================================'
  exit 1
fi

echo -n 'Unittest run output for ' 
date
echo '========================================================================'

for i in `ls -1 ../../../util/tests/*.ini` ; do \
  $PHPFIVE_BINARY $PHPFIVE_ARGS run.php $i
done

echo '========================================================================'
