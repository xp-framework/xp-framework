##
# Makefile
#
# $Id$

PHP?=php

parser_generator/phpJay.exe:
	cd parser_generator && make && cd ..

net/xp_framework/tools/vm/Parser.class.php:
	sh generate_parser.sh

test: net/xp_framework/tools/vm/Parser.class.php parser_generator/phpJay.exe
	$(PHP) ../../../../util/tests/run.php php5-emit/unittest.ini

clean:
	rm parser_generator/phpJay.exe net/xp_framework/tools/vm/Parser.class.php
