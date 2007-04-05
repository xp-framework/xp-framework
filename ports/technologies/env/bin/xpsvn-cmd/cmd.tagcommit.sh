#!/bin/sh
#
# $Id$
#


TAG=$(fetchTag $1)
[ -z $TAG ] && exit 1

cd "$REPOBASE"/tags/$TAG

echo -n "===> Current status in " ; pwd

svn status

echo
read -p "Do you really want to merge this (y/n)? " desc

if [ "$desc" = "y" ]; then
  svn ci -m '- MFT'
fi
