##
# Makefile
#
# $Id$

PHP=$(shell grep ^php5= .environment | cut -d '=' -f 2)
SKELETON_PATH?=../../../../skeleton/
PORTS_PATH?=../../../../ports/classes/

all: usage

.environment:
	@echo "*** Please run configure to create the environment!"
	@exit 1

usage:
	@echo "- $(MAKE) test"
	@echo "  Runs all tests from unittest.ini and php5-emit/unittest.ini"

net/xp_framework/tools/vm/Parser.class.php: grammar/XP2.jay
	@echo "===> Generating Parser class from XP2.jay"
	../../../../ports/technologies/opt/jay/generate.sh grammar/XP2.jay php5 "" > net/xp_framework/tools/vm/Parser.class.php

generate: net/xp_framework/tools/vm/Parser.class.php 

test: generate .environment
	$(PHP) ../../../../ports/unittest/run.php unittest.ini
	$(PHP) ../../../../ports/unittest/run.php php5-emit/unittest.ini

xp-test: generate .environment
	@for i in `ls -1 php5-emit/xp-test/*.ini` ; do ./xp unittest.xp $$i ; done

clean:
	@echo "===> Cleaning"
	cd parser_generator && $(MAKE) clean
	rm net/xp_framework/tools/vm/Parser.class.php

php5-emit/skeleton:
	mkdir -p php5-emit/skeleton

php5-emit/ports/classes:
	mkdir -p php5-emit/ports/classes

xpclass: .environment
	@-$(PHP) migrate.php `echo $(name) | sed -e 's#$(path)##g' | sed -e 's#.class.php$$##g' | tr / .` --output=$(out);

convert-skeleton:  php5-emit/skeleton
	@-$(PHP) migrate.php --scan=$(SKELETON_PATH) --output=php5-emit/skeleton

convert-ports: php5-emit/ports/classes 
	@-$(PHP) migrate.php --scan=$(PORTS_PATH) --output=php5-emit/ports/classes

convert: convert-skeleton convert-ports
	rm php5-emit/skeleton/lang/Object.xp    # Builtin
