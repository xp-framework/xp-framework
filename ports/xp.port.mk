# Makefile
#
# $Id$

BASE?=			../..
PHP?=			php
XPINSTALL=		${BASE}/xpi/install.php
XPFIRSTINSTALL=	${BASE}/xpi/firstinstall.php
XPT=			${BASE}/xpt/run.php
XSLPROC?=		sabcmd

# Default values for wrapper target
WRAPPER_SRC?=	wrapper
WRAPPER_XSL?=	$(BASE)/../skeleton/scriptlet/xml/workflow/generator/wrapper.xsl
WRAPPER_DTD?=	$(BASE)/../skeleton/scriptlet/xml/workflow/generator/wrapper.dtd

all:    usage

usage:
	@echo "Usage:"
	@echo "======"
	@echo "  make firstinstall.<installtarget>    - Prepare installation"
	@echo "  make install.<installtarget>         - Install <installtarget>"
	@echo "  make wrapper                         - Regenerate wrappers"

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

wrapper:
	@if [ -z "$(WRAPPER_PHP)" ]; then \
        echo "You must set the target for the generated wrapper files via the WRAPPER_PHP variable."; \
        exit 1; \
	else \
		if [ -z "$(file)" ] ; then \
			TARGETS="wrapper/*.iwrp"; \
        else \
			TARGETS="$(file)"; \
        fi; \
		for i in `ls -1 $$TARGETS | cut -d '.' -f 1` ; do \
			echo "--->" $$i; \
			xmllint -noout -dtdvalid $(WRAPPER_DTD) $$i.iwrp && \
    		xsltproc $(WRAPPER_XSL) $$i.iwrp > $(WRAPPER_PHP)/`basename $$i`Wrapper.class.php; \
		done \
	fi
