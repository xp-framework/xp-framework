#!/bin/sh

FILE=/tmp/loginfo_`md5 -q -s $1`
read MESSAGE
echo ${MESSAGE} > ${FILE}
php -q xp_notify.php ${FILE} $2
