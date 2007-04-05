##
# Makefile for the XP extension "sybase_api"
#
# $Id$

CFLAGS=-ggdb -O -Wall -I. -I/usr/local/include -L/usr/local/lib

test: sybase_api.o sybase_mm.o sybase_hash.o test.c
	$(CC) $(CFLAGS) -lct -lreadline test.c sybase_api.o sybase_mm.o sybase_hash.o -o test

clean:
	rm -f *.o *.core test
