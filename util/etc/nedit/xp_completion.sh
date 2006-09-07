#!/usr/local/bin/bash
#
# $Id$
#

#
# This is a bash-completion script for the XP Framework classes
#
# Place it somewhere in your homedir and source it from your .bashrc like this
#
# . ~/.nedit/xp_completion.sh
#
# Use nx to encapsulate "nedit for XP"

export SED_CMD=`which gsed sed 2>/dev/null | head -n1`

export PHP_INCLUDE_PATH=`php -r 'echo ini_get("include_path");'`


_xpclasses() {
  local CUR CP_PREFIX RESULTS DIR
  COMPREPLY=()

  CUR=${COMP_WORDS[$COMP_CWORD]}
  local IFS=':'
  CP_PREFIX=`echo $CUR | tr . /`
  for DIR in $PHP_INCLUDE_PATH; do
    IFS=" "
    RESULTS=$( \
      find $DIR/$CP_PREFIX* -type d ! -path '*CVS*' ! -path '*.svn*' ! -name '.*' -mindepth 0 -maxdepth 1 -exec echo {}. \; 2>/dev/null; \
      find $DIR/$CP_PREFIX* -type f -name '*.class.php' ! -path '*CVS*' ! -path '*.svn*' ! -name '.*' -mindepth 0 -maxdepth 1 2>/dev/null \
    )
    
    # echo "===> RESULTS= $RESULTS"
    FILTERED=$( echo $RESULTS | $SED_CMD -r "s#$DIR/?##g" | $SED_CMD -r "s#\.class\.php\$##g" | tr / . )
    
    unset IFS
    for ROW in ${FILTERED}; do
      if [ -n "$ROW" ]; then
        COMPREPLY[${#COMPREPLY[@]}]=$ROW
      fi
    done
  done
}

complete -o nospace -F _xpclasses nx
