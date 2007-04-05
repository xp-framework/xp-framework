#!/bin/sh
#
# $Id$
#


# Parse command line options
OPTS=""
while getopts 'v' COMMAND_LINE_ARGUMENT ; do
  case "$COMMAND_LINE_ARGUMENT" in
    v)  OPTS="$OPTS -v"  ;;
    ?)  exit
  esac
done
shift $(($OPTIND - 1))

TAG=$(fetchTag $1)
[ -z $TAG ] && exit 1

cd "$REPOBASE"/tags/$TAG
svn status $OPTS
