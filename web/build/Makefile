##
# Makefile for XP website
# 
# $Id$

PHP=php
collections=../etc/make/collections.ini

.PHONY: cache

all: parse collections

cache:
	test -e cache || mkdir cache cache/sapi.php cache/class.php

parse: cache
	$(PHP) make.php $(collections)

collections:
	$(PHP) collections.php $(collections) > collections.xml
