##
# Makefile for the XP extension "sybase_api"
#
# $Id$

CFLAGS=-ggdb -O -Wall -I. -I/usr/local/include -L/usr/local/lib
OBJECTS=csta_error.o csta_network.o csta_connection.o auth_sybmetha.o sybase_api/sybase_api.o sybase_api/sybase_mm.o sybase_api/sybase_hash.o

default: cstaproxy

cstaproxy: $(OBJECTS) cstaproxy.c 
	$(CC) $(CFLAGS) -lct $(OBJECTS) cstaproxy.c -o cstaproxy

clean:
	rm -f *.o *.core cstaproxy
