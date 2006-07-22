#!/bin/sh

echo -n 'Unittest run output for ' 
date
echo '========================================================================'

for i in `ls -1 ../../../util/tests/*.ini` ; do \
  php run.php $i
done

echo '========================================================================'
