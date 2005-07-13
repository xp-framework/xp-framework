##
# Makefile for generating PHP interfaces from EJB
#
# $Id: Makefile 4942 2005-04-04 16:36:50Z friebe $

JBOSS_LIBS=/home/gelli/jboss-4.0.2/client/jbossall-client.jar
JAVA=java
JAVAC=javac
XSLPROC=xsltproc

all:
	@echo "Usage: $(MAKE) generate class=<ejb_classname> [path=<path_to_class>]"

clean:
	@echo "===> Cleaning..."
	-rm EjbObject2XML.class *.class.php

generate:			EjbObject2XML.class
	$(JAVA) -classpath ./:$(JBOSS_LIBS):$(path) EjbObject2XML $(class) 

EjbObject2XML.class:	 EjbObject2XML.java
	$(JAVAC) EjbObject2XML.java
