#!/bin/sh
#
# $Id$
#


TAG=$(fetchTag $1)
[ -z $TAG ] && exit 1

echo "---> Starting temporary shell, use 'exit' to return where you came from..."
cd  "$REPOBASE"/tags/$TAG && $SHELL
