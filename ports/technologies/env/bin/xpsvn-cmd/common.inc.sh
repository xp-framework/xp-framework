#!/bin/sh
#
# $Id$
#

repositoryBase () {
  local $BASE
  BASE=$(realpath . | ${SED} -r "s#/(trunk|tags|branches)(/)?.*##")
  
  if [ -z "$BASE" ]; then
    echo "Unable to determine repository base. Do you have a proper checkout?" >&2;
    return 1;
  fi
  
  if [ ! -e "$BASE"/trunk -o ! -e "$BASE"/tags ]; then
    echo "trunk or tags directory does not exist, this is a prerequisite." >&2;
    return 1;
  fi
  
  echo $BASE
}

repositoryUrl () {
  local REPO=$1
  svn info $1 | grep '^URL:' | cut -d ' ' -f 2 | ${SED} -r "s#/(trunk|tags|branches)(/)?.*##"
}

fetchTarget() {
  local TARGET=$1
  
  if [ -z $TARGET ]; then
    TARGET="."
  fi
  
  local REAL=$(realpath $TARGET)
  
  if [ ! -e "$REAL" ]; then
    echo "Invalid target specified: $TARGET" >&2;
    return 1;
  fi
  
  echo $REAL;
}

fetchTag() {
  local TAG=$1
  
  if [ -z $TAG -o ! -e "$REPOBASE"/tags/$TAG ]; then
    echo "Invalid tag name specified: $TAG" >&2;
    return 1;
  fi
  
  echo $TAG
  return 0
}

fetchFileRevision() {
  local FILE=$1
  
  svn info --xml "$FILE" | grep revision | tail -n1 | cut -d '"' -f2
}

relativeTarget () {
  local TARGET=$1
  
  echo $TARGET | ${SED} -r "s#$REPOBASE/trunk/##"
}


# Find out suitable sed executable
# Tip for FreeBSD users: install /usr/ports/textproc/gsed
SED=$(which gsed sed 2>/dev/null | head -n 1)

# Initially parse command line to find global
# options
while getopts 'vdr:' COMMAND_LINE_ARGUMENT ; do
  case "$COMMAND_LINE_ARGUMENT" in
    v)  VERBOSE="yes";;
    d)  DEBUG="yes";;
    r)  REPOBASE=$(realpath $OPTARG);;
    ?)  exit
  esac
done
shift $(($OPTIND - 1))

[ -z "$REPOBASE" ] && REPOBASE=$(repositoryBase)
[ -z "$REPOBASE" -o ! -e "$REPOBASE" ] && {
  echo "!!! Repository base not found or not specified: '$REPOBASE'";
  exit 1;
}

[ -z "$REPOURL" ] && REPOURL=$(repositoryUrl)
[ -z "$REPOURL" ] && {
  echo "!!! Could not determine repository URL.";
  exit 1;
}

[ "$VERBOSE" = "yes" ] && { 
  echo "===> Global repository information:"
  echo "---> Repository base: $REPOBASE"
  echo "---> Repository url: $REPOURL"
  echo
}

# Reset options indicator for further scans
OPTIND=1
