#!/bin/sh
#
# $Id$
#


# Initially parse command line to find global
# options
while getopts 'v' COMMAND_LINE_ARGUMENT ; do
  case "$COMMAND_LINE_ARGUMENT" in
    v)  VERBOSE="yes"  ;;
    ?)  exit
  esac
done
shift $(($OPTIND - 1))

RETCODE=1
while [ ! -z $1 ]; do
  TARGET=$1
  if [ -z $TARGET ]; then
    TARGET=".";
  fi

  TARGET=$(fetchTarget $TARGET)
  [ -z $TARGET ] && exit 1;

  RELTARGET=$(relativeTarget $TARGET)
  [ -z $RELTARGET ] && exit 1;

  [ $VERBOSE ] && echo "Searching file $TARGET..."

  for i in `ls "$REPOBASE"/tags/`; do
    if [ -d "$REPOBASE"/tags/$i ]; then
      if [ -e "$REPOBASE"/tags/$i/$RELTARGET ]; then
        rev=$(fetchFileRevision "$REPOBASE"/tags/$i/$RELTARGET)
        echo "$i (revision $rev)";
        RETCODE=0
      fi
    fi
  done
  
  shift 1
done

exit $RETCODE
