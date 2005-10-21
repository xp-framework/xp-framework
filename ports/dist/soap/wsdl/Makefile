# 
# Makefile to generate classes from a WSDL
#
# $Id$

.PHONY: fetch generate

all: usage

fetch:
	wget -O - $(URL) > wsdl.xml

generate: fetch
	php generate.php $(WSDL_XSL} wsdl.xml 
