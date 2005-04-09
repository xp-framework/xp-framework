# Makefile for the uska.de website
#
# $Id$ 

BASE=../../..

include ../../../xp.port.mk

dbclasses:
	for i in `ls -1 doc/dbxml/*.xml`; do \
		FILE=`basename $$i`; \
        CLASS=`echo $$FILE | sed -E 's/(.+)\.[a-z]+$$/\1/g'`; \
		sabcmd ../../databases/util/classgen/data/xp.php.xsl $$i > ../../../classes/de/uska/db/$$CLASS.class.php ; \
	done
