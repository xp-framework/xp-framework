#!/bin/sh

FILE="/tmp/loginfo_`md5 -q -s $1`"
echo $FILE
exit 1
read MESSAGE
echo ${MESSAGE} > ${FILE}
php -q `dirname $0`/xp_notify.php ${FILE} $2
