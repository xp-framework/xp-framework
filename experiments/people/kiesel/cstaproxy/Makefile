##
# Makefile for the XP extension "sybase_api"
#
# $Id$

CFLAGS=-ggdb -O -Wall -I. -I/usr/local/include -L/usr/local/lib

default: cstaproxy

cstaproxy: cstaproxy.c csta_error.c csta_network.c
	$(CC) $(CFLAGS) csta_error.c csta_network.c cstaproxy.c -o cstaproxy

clean:
	rm -f *.o *.core test
