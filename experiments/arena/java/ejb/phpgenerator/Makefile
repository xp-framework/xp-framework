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
	rm EjbToXMLDoclet.class *.gen.xml

generate:			EjbToXMLDoclet.class $(TOOLS_JAR)
	FILE=`$(JAVADOC) -doclet EjbToXMLDoclet -docletpath . -classpath $(TOOLS_JAR) $(from) 2>/dev/null \
	  | grep ^@@ \
	  | cut -d= -f2` ; \
	$(XSLPROC) xp.php.xsl $$FILE
    
EjbToXMLDoclet.class:	 EjbToXMLDoclet.java $(TOOLS_JAR)
	$(JAVAC) -classpath $(TOOLS_JAR) EjbToXMLDoclet.java
