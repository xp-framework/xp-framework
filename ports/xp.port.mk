# Makefile
#
# $Id$

PHP=        php -q -C
PORTSDIR!=  echo ${.CURDIR} | sed -E 's(.*/ports)/(.+)$$\1g'

all:    usage

usage:
	@echo "Usage: make dist   - Make distribution"
	@echo "       make clean  - Clean"
            
dist:
	@${PHP} ${PORTSDIR}/dist.php ${.CURDIR} 

clean:
	@-rm -rf ${.CURDIR}/build
