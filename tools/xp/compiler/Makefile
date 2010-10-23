##
# Makefile to generate parsers from grammar files
#
# $Id$

GENSH=../../../ports/technologies/opt/jay/generate.sh

all: syntax/xp/Parser.class.php syntax/php/Parser.class.php

syntax/xp/Parser.class.php: grammar/xp.jay
	sh $(GENSH) grammar/xp.jay php5 "" xp.compiler.syntax.xp > $@

syntax/php/Parser.class.php: grammar/php.jay
	sh $(GENSH) grammar/php.jay php5 "" xp.compiler.syntax.php > $@

