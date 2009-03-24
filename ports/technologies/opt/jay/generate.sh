#!/bin/sh
## 
# Generates a parser from a given skeleton and grammar
#
# $Id$

MYSELF=$(realpath "$0")

# Show usage
[ "" = "$1" ] && {
  CMD=$(basename "$0")
  echo "Usage: $CMD <grammar> <skeleton-name> [<parser-name>] [<package-name>] > [output]"
  echo "- Simple......: $CMD sql.jay php5 SQL > SQLParser.class.php"
  echo "- Qualified...: $CMD sql.jay php5 SQL rdbms.parser > SQLParser.class.php"
  exit 1;
}

# Detect executable
DIRNAME=$(dirname "$MYSELF")
WINDOWS=`env | grep OS | grep -i WINDOWS | wc -l`
if [ 0 -lt $WINDOWS ] ; then
  JAY_BIN=phpJay.exe
else
  JAY_BIN=phpJay
fi

[ ! -x "$DIRNAME/$JAY_BIN" ] && {
  echo "*** Jay binary '$DIRNAME/$JAY_BIN' not found, be sure to have it created.";
  exit 1;
}

# Parse arguments
GRAMMAR=$1
[ ! -e "$GRAMMAR" ] && {
  echo "*** No such grammar '$GRAMMAR'";
  exit 1;
}

SKELETON=$2
[ ! -e "$DIRNAME/skel/$SKELETON.skl" ] && {
  echo "*** No such skeleton '$DIRNAME/skel/$SKELETON.skl'";
  exit 1;
}

PACKAGEN="$4"
if [ ! -z "$PACKAGEN" ] ; then
  PACKAGEL="`echo $4 | sed -e 's/\./·/g'`·"
  PACKAGES="\$package= '$PACKAGEN';"
  PACKAGEN="$PACKAGEN."
fi

# Run
"$DIRNAME/$JAY_BIN" -cv < "$DIRNAME/skel/$SKELETON.skl" $GRAMMAR \
  | sed -e 's/{%NAME%}/'$3'/g' \
  | sed -e 's/{%PACKAGEL%}/'$PACKAGEL'/g' \
  | sed -e 's/{%PACKAGEN%}/'$PACKAGEN'/g' \
  | sed -e 's/{%PACKAGES%}/'"$PACKAGES"'/g'
