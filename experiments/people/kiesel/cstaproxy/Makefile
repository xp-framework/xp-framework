##
# Makefile for the XP extension "sybase_api"
#
# $Id$

CFLAGS=-ggdb -O -Wall -I. -I/usr/local/include -L/usr/local/lib

default: cstaproxy

cstaproxy: cstaproxy.c csta_error.c csta_network.c csta_connection.c
	$(CC) $(CFLAGS) csta_error.c csta_network.c csta_connection.c cstaproxy.c -o cstaproxy

clean:
	rm -f *.o *.core cstaproxy
