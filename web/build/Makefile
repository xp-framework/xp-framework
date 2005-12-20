##
# Makefile for XP website
# 
# $Id$

PHP=php -dinclude_path=../../skeleton -dmemory_limit=16M
collections=../etc/make/collections.ini

.PHONY: cache

all: parse collections inheritance

cache:
	test -e cache || mkdir cache
    
clean:
	@rm collections.xml inheritance.xml
	@rm -rf cache

parse: cache
	$(PHP) make.php $(collections)

collections:
	$(PHP) collections.php $(collections) > collections.tmp.xml
	mv collections.tmp.xml collections.xml

inheritance:
	$(PHP) inheritance.php $(collections) > inheritance.tmp.xml
	mv inheritance.tmp.xml inheritance.xml
