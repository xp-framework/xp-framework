##
# Makefile
#
# $Id$

all:	usage

usage:
	@echo "$(MAKE) run - compiles and runs the test script"

compile:
	@javac Test.java

run:	compile
	@CLASSPATH="." java Test
