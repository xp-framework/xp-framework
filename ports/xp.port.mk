# Makefile
#
# $Id$

BASE?=			../..
PHP?=			php
XPINSTALL=		${BASE}/technologies/env/bin/install/install.php
XPFIRSTINSTALL=	${BASE}/technologies/env/bin/install/firstinstall.php
XPT=			${BASE}/unittest/run.php
XSLPROC?=		xsltproc
XPCLI?=			xpcli

# Default values for wrapper target
WRAPPER_SRC?=	wrapper
WRAPPER_XSL?=	$(BASE)/../skeleton/scriptlet/xml/workflow/generator/wrapper.xsl
HANDLER_XSL?=	$(BASE)/../skeleton/scriptlet/xml/workflow/generator/handler.xsl
WRAPPER_DTD?=	$(BASE)/../skeleton/scriptlet/xml/workflow/generator/wrapper.dtd

# Default values for skeleton path
XPCLASSPATH?=	$(BASE)/classes

# Variables for database class generation
DBDIR?=			conf/db
DBBASE?=		${XPCLASSPATH}


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
			HANDLER=`cat $$i.iwrp | grep '<handler class=' | cut -d '"' -f 2` ; \
			HANDLERFILE=`echo $$HANDLER | tr . /`.class.php ; \
			if [ -n "$$HANDLER" -a ! -f $(XPCLASSPATH)/$$HANDLERFILE ]; then \
				echo "---> $$HANDLER"; \
                xsltproc $(HANDLER_XSL) $$i.iwrp > $(XPCLASSPATH)/$$HANDLERFILE ; \
			fi \
		done \
	fi

database: database.god database.gen
database.god:
	@for i in `ls -1 $(DBDIR)`; do \
	  echo "===> Generating XML files for $$i"; \
	  $(XPCLI) net.xp_framework.db.generator.DataSetCreator -c $(DBDIR)/$$i/config.ini -O $(DBDIR); \
	done;

database.gen:
	@for j in `ls -1 $(DBDIR)`; do \
	  echo "===> Generating database classes for $$j"; \
		for i in `find $(DBDIR)/$$j/tables -name *.xml`; do \
			$(XPCLI) net.xp_framework.db.generator.DataSetCreator -c $(DBDIR)/$$j/config.ini -X $$i -O ${DBBASE}; \
	        done; \
	done;
