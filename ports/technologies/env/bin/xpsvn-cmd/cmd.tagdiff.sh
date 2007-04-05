#!/bin/sh
#
# $Id$
#


TAG=$(fetchTag $1)
[ -z $TAG ] && exit 1

cd "$REPOBASE"/tags/$TAG
svn diff
