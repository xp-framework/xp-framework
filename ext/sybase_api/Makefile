##
# Makefile for the XP extension "sybase_api"
#
# $Id$

CFLAGS=-ggdb -O -Wall -I. -I/usr/local/include -L/usr/local/lib

test: sybase_api.o sybase_mm.o test.c
	$(CC) $(CFLAGS) -lct test.c sybase_api.o sybase_mm.o -o test

clean:
	rm -f *.o *.core test
