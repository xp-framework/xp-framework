#!/bin/sh
#
# $Id$
#

recursiveCreateEmpty() {
  local DIR=$1
  
  if [ ! -d $(dirname $DIR) ]; then
    recursiveCreateEmpty $(dirname $DIR);
  fi
  
  svn mkdir $DIR
  return;
}

fetchFileURL() {
  local FILE=$1
  
  svn info "$FILE" | grep '^URL:' | cut -d ' ' -f 2
}

recursiveCopy() {
  local SOURCEBASE=$1
  local TARGETBASE=$2
  local ITEM=$3

  [ $DEBUG ] && echo "---> Checking $ITEM"
  
  if [ ! -e "$TARGETBASE"/$ITEM ]; then
  
    # New, so "svn copy" from repository (URL -> WC)
    [ $DEBUG ] && echo "svn copy "$REPOROOT"/trunk/$ITEM "$TARGETBASE"/$ITEM";
    [ "yes" = $ONLY_EXISTING ] && {
      echo "Skipping $ITEM, because it does not exist in tag";
      return;
    }
    
    PARENT_DIR=$(dirname "$TARGETBASE"/$ITEM);
    [ "no" = $CREATE_EMPTY_DIR ] && [ ! -d "$PARENT_DIR" ] && {
      echo "Skipping $ITEM, because the parent does not exist";
      echo "You can change this behaviour with the -c switch!";
      return;
    }
    
    # Create empty parent directory
    [ ! -d "$PARENT_DIR" ] && {
      recursiveCreateEmpty "$PARENT_DIR"
    }
    
    # If source and target are in the same repository, svn copy the file
    if [ "$REPOURL" = $(repositoryUrl "$SOURCEBASE"/$ITEM) ]; then
      [ $DEBUG ] && echo "svn copy $(fetchFileURL "$REPOBASE"/trunk/$ITEM) "$TARGETBASE"/$ITEM";
      svn copy $(fetchFileURL "$REPOBASE"/trunk/$ITEM) "$TARGETBASE"/$ITEM;
    else
      [ $DEBUG ] && echo "svn export $(fetchFileURL "$REPOBASE"/trunk/$ITEM) "$TARGETBASE"/$ITEM";
      svn export $(fetchFileURL "$REPOBASE"/trunk/$ITEM) "$TARGETBASE"/$ITEM;
      svn add "$TARGETBASE"/$ITEM;
    fi
    
    # otherwise svn export it into the target
    return;
  fi
  
  if [ -f "$TARGETBASE"/$ITEM ]; then
  
    # Update by `svn cat`, which print the file without local modifications
    [ $DEBUG ] && echo "svn cat "$SOURCEBASE"/$ITEM > "$TARGETBASE"/$ITEM";
    svn cat "$SOURCEBASE"/$ITEM > "$TARGETBASE"/$ITEM;
    return;
  fi
  
  if [ -d "$TARGETBASE"/$ITEM ]; then
  
    # Recursively copy directory
    for i in $(ls -1 "$SOURCEBASE"/$ITEM); do
      recursiveCopy "$SOURCEBASE" "$TARGETBASE" ""$ITEM"/$i";
    done
  fi
}

# Parse command line options
ONLY_EXISTING="no"
CREATE_EMPTY_DIR="no"
while getopts 'uc' COMMAND_LINE_ARGUMENT ; do
  case "$COMMAND_LINE_ARGUMENT" in
    u)  ONLY_EXISTING="yes"     ;;
    c)  CREATE_EMPTY_DIR="yes"  ;; 
    ?)  exit
  esac
done
shift $(($OPTIND - 1))


TAG=$(fetchTag $1)
[ -z $TAG ] && exit 1
shift 1

while [ ! -z $1 ]; do
  TARGET=$(fetchTarget $1)
  [ -z "$TARGET" ] && exit 1

  RELTARGET=$(relativeTarget "$TARGET")
  recursiveCopy "$REPOBASE"/trunk "$REPOBASE"/tags/$TAG $RELTARGET
  
  shift 1
done

cd "$REPOBASE"/tags/$TAG && svn status
