#!/bin/sh

##
# CVS Commit notifier
#
# This shall be called from CVSROOT/loginfo in the following way:
# ALL     (cat | sh /home/cvs/bin/notify.sh %{Vvs} xp@php3.de)
# 
# $Id$

FILE="/tmp/loginfo_`md5 -q -s "$1"`"
cat > ${FILE}
php -q `dirname $0`/xp_notify.php ${FILE} $2 "$1" 1>/tmp/cvs_notify.log 2>/tmp/cvs_notify.err &
