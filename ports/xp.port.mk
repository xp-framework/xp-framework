# Makefile
#
# $Id$

PHP=php -q -C
XPI=xpi/install.php
XPT=xpt/run.php

all:    usage

usage:
	@echo "Usage: make install"

install:
	${PHP} ${XPI}

test:
	${PHP} ${XPT}
