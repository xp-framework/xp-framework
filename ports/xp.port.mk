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
	@echo "===> Compressing"
	@cd ${.CURDIR}/build/ ; for i in `ls -1 *.cca` ; do echo "---> $$i" ; gzip $$i ; done
	@echo "===> Copying"
	@cp -R ${.CURDIR}/build/* ${PORTSDIR}/dist

clean:
	@echo "===> Cleaning"
	-rm -rf ${.CURDIR}/build

req:
	find ${DIR} -name '*.class.php' | ${PHP} ${PORTSDIR}/req.php
