##
# Makefile
#
# $Id$

PHP?=php
SKELETON_PATH?=../../../../skeleton/

all: test

net/xp_framework/tools/vm/Parser.class.php: parser_generator/XP2.jay
	@echo "===> Generating Parser class from XP2.jay"
	sh generate_parser.sh

generator:
	cd parser_generator && $(MAKE)

generate: generator net/xp_framework/tools/vm/Parser.class.php 

test: generate
	$(PHP) ../../../../util/tests/run.php unittest.ini
	$(PHP) ../../../../util/tests/run.php php5-emit/unittest.ini

clean:
	@echo "===> Cleaning"
	cd parser_generator && $(MAKE) clean
	rm net/xp_framework/tools/vm/Parser.class.php

php5-emit/skeleton:
	mkdir php5-emit/skeleton

xpclass:
	@-$(PHP) migrate.php `echo $(name) | sed -e 's#$(SKELETON_PATH)##g' | sed -e 's#.class.php$$##g' | tr / .` --output=php5-emit/skeleton;

convert:  php5-emit/skeleton
	for i in `find $(SKELETON_PATH) -name '*.class.php' ! -path "*.svn*"` ; do \
	  $(MAKE) xpclass name=$$i; \
	done
