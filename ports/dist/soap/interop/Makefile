# Makefile for XP SOAP Interop Suite
#
# $Id$ 

.PHONY: report

XSLTPROC= `which xsltproc`
PHP= `which php || which php4`

log/%/servicetest.xml: etc/services.ini
	$(PHP) bin/interop.php

log/servicetests.xml: log/XP-SOAP/servicetest.xml
	$(PHP) bin/genreport.php
	
report: log/servicetests.xml
	$(XSLTPROC) xsl/report.xsl log/servicetests.xml
