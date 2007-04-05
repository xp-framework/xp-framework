#!/bin/sh
#
# $Id$
#

TAG=$1

# Make sure tag does not yet exist
[ -z $TAG ] && exit 1;
[ -d "$REPOBASE"/tags/$TAG ] && {
  echo "A tag with the name $TAG already exists.";
  exit 1;
}

# Now create...
echo "===> Creating directory structure for tag $TAG ..."
svn mkdir "$REPOBASE"/tags/$TAG
