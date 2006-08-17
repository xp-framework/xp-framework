#!/bin/sh

echo "===> Copying overrides"
for i in `find override -name '*.class.php' ! -path '*svn*'` ; do
  target=`echo $i | sed -e 's/^override/skeleton2/g'`
  echo "---> $i -> $target"
  cp $i $target
done
