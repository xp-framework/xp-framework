##
# Makefile for the XP extension "sybase_api"
#
# $Id$

CFLAGS=-ggdb -O -Wall -I. -I/usr/local/include -L/usr/local/lib
OBJECTS=csta_error.o csta_network.o csta_connection.o  csta_filter.o auth_sybmetha.o
DEPENDS=$(OBJECTS) sybase_api/sybase_api.o sybase_api/sybase_mm.o sybase_api/sybase_hash.o
SYBOBJS=sybase_api.o sybase_mm.o sybase_hash.o

default: cstaproxy

cstaproxy: $(DEPENDS) cstaproxy.c 
	$(CC) $(CFLAGS) -lct $(OBJECTS) $(SYBOBJS) cstaproxy.c -o cstaproxy

clean:
	rm -f *.o *.core cstaproxy
