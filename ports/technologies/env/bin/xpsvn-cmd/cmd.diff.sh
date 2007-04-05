#!/bin/sh
#
# $Id$
#


# Parse command line options
while getopts 'vN' COMMAND_LINE_ARGUMENT ; do
  case "$COMMAND_LINE_ARGUMENT" in
    v)  DIFF_VERBOSE="yes"  ;;
    N)  DIFF_NEWFILES="yes" ;;
    ?)  exit
  esac
done
shift $(($OPTIND - 1))

TAG=$(fetchTag $1)
[ -z $TAG ] && exit 1
shift 1

while [ ! -z $1 ]; do

  TARGET=$(fetchTarget $1)
  [ -z $TARGET ] && exit 1

  RELTARGET=$(relativeTarget $TARGET)

  [ $VERBOSE ] && echo "Diffing $RELTARGET against $TAG"

  # First, check on existance
  if [ ! -e "$REPOBASE"/tags/$TAG/$RELTARGET ]; then
    echo "A      $RELTARGET";
    PARENT=$(dirname "$REPOBASE"/tags/$TAG/$RELTARGET)
    if [ ! -e $PARENT ]; then
      echo "!      $PARENT (nonexistant)";
    fi
  fi

  # If target is a single file, assume verbose diff
  if [ -f $TARGET ]; then
    DIFF_VERBOSE="yes";
  fi

  DIFF_OPTIONS='-urbB --exclude=.svn --exclude=CVS --exclude="*.#*"'
  if [ "yes" != "$DIFF_VERBOSE"  ]; then DIFF_OPTIONS="$DIFF_OPTIONS --brief"; fi
  if [ "yes"  = "$DIFF_NEWFILES" ]; then DIFF_OPTIONS="$DIFF_OPTIONS -N"; fi

  diff -I '\$Id' -I '\$Revision' $DIFF_OPTIONS "$REPOBASE"/tags/$TAG/$RELTARGET "$REPOBASE"/trunk/$RELTARGET 2>&1 | \
    grep -v '^diff' | \
    ${SED} "s#: #/#" | \
    ${SED} "s#^Only in "$REPOBASE"/tags/$TAG/"$RELTARGET"/#D      #g" | \
    ${SED} "s#^Only in ${REPOBASE}/trunk/"$RELTARGET"/#A      #g" | \
    ${SED} -r "s#Files "$REPOBASE"/tags/$TAG/"$RELTARGET"/([^ ]+) and ([^ ]+) differ#M      \\1#g" | \
    ${SED} "s#^--- ${REPOBASE}/tags/#--- #g" | \
    ${SED} "s#^+++ ${REPOBASE}/trunk/#+++ trunk/#g"

  shift 1
done
