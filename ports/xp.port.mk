# Makefile
#
# $Id$

BASE?=			../..
PHP?=			php
XPINSTALL=		${BASE}/xpi/install.php
XPFIRSTINSTALL=	${BASE}/xpi/firstinstall.php
XPT=			${BASE}/xpt/run.php

all:    usage

usage:
	@echo "Usage:"
	@echo "======"
	@echo "  make firstinstall.<installtarget>    - Prepare installation"
	@echo "  make install.<installtarget>         - Install <installtarget>"

install.%:
	@echo "-------------------------------------------------------"
	${PHP} ${XPINSTALL} xpinstall/$*.xpi.ini -f $(file) $(flags)
	@echo "-------------------------------------------------------"

firstinstall.%:
	@echo "-------------------------------------------------------"
	$(PHP) $(XPFIRSTINSTALL) xpinstall/$*.xpi.ini
	@echo "-------------------------------------------------------"

test:
	${PHP} ${XPT}
