# Makefile
#
# $Id$

PHP=        php -q -C
PORTSDIR!=  echo ${.CURDIR} | sed -E 's(.*/ports)/(.+)$$\1g'
DISTDIR=    ${PORTSDIR}/dist

.PHONY:	build

all:    usage

usage:
	@echo "Usage: make dist"
	@echo "       - Make distribution"
	@echo ""
	@echo "       make clean"
	@echo "       - Clean port directory"
	@echo ""
	@echo "       make req DIR=<directory>"
	@echo "       - Get required files for a collection directory"
	@echo ""   

build:
	@${PHP} ${PORTSDIR}/build.php ${.CURDIR}

dist:	build
	cd ${.CURDIR}/build/ ; tar cvfz port.tar.gz *
    
clean:
	-rm -rf ${.CURDIR}/build

req:
	find ${DIR} -name '*.class.php' | ${PHP} ${PORTSDIR}/req.php
