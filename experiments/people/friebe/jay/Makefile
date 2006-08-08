##
# Makefile
#
# $Id$

PHP?=php

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
