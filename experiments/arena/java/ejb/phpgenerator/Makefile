##
# Makefile for generating PHP interfaces from EJB
#
# $Id$

TOOLS_JAR=/usr/local/jdk1.4.2/lib/tools.jar
JAVADOC=javadoc
JAVAC=javac
XSLPROC=xsltproc

all:
	@echo "Usage: $(MAKE) generate from=<path_to_ejb>"

clean:
	@echo "===> Cleaning..."
	-rm EjbToXMLDoclet.class *.gen.xml *.class.php

generate:			EjbToXMLDoclet.class
	@FILE=`$(JAVADOC) -doclet EjbToXMLDoclet -docletpath . -classpath $(TOOLS_JAR) $(from) 2>/dev/null \
	  | grep ^@@ \
	  | cut -d= -f2` ; \
	$(XSLPROC) xp.php.xsl $${FILE}.gen.xml > $${FILE}Remote.class.php ; \
	echo "===> $${FILE}Remote.class.php written"

EjbToXMLDoclet.class:	 EjbToXMLDoclet.java
	$(JAVAC) -classpath $(TOOLS_JAR) EjbToXMLDoclet.java
