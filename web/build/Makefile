##
# Makefile for XP website
# 
# $Id$

PHP=php -dinclude_path=../../skeleton
collections=../etc/make/collections.ini

.PHONY: cache

all: parse collections inheritance

cache:
	test -e cache || mkdir cache

parse: cache
	$(PHP) make.php $(collections)

collections:
	$(PHP) collections.php $(collections) > collections.tmp.xml
	mv collections.tmp.xml collections.xml

inheritance:
	$(PHP) inheritance.php $(collections) > inheritance.tmp.xml
	mv inheritance.tmp.xml inheritance.xml
