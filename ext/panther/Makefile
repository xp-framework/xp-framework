# Makefile
#
# $Id$

PHPLIBDIR?=.
INCLUDEDIR?=/usr/local/include/php
LIBDIR?=/usr/local/lib
INCLUDES=-I$(INCLUDEDIR) -I$(INCLUDEDIR)/main -I$(INCLUDEDIR)/Zend -I$(INCLUDEDIR)/TSRM
LIBDIRS=-L$(LIBDIR)
CFLAGS=-g -O -Wall -pthread -D_REENTRANT -D_THREAD_SAFE -DZTS $(INCLUDES) $(LIBDIRS)
LIBTOOL=$(SHELL) libtool
LIBRARIES=-lmd

all:    panther

usage:
	@echo "Makefile for panther"
	@echo ""
	@echo "Usage:"
	@echo "- make"
	@echo "- make INCLUDEDIR=/usr/src/php PHPLIBDIR=/usr/src/php"
	@echo ""
	@echo "Notes:"
	@echo "- INCLUDEDIR is the directory where PHPs includes are"
	@echo "- PHPLIBDIR is the directory where libphp5.so resides"
	@echo ""

panther: panther.o rmi.o
	@echo "---> Linking"
	$(LIBTOOL) --mode=link $(CC) $(CFLAGS) $(LIBRARIES) $(LIBDIRS) -o panther $(PHPLIBDIR)/libphp5.so $(LIBS) panther.o rmi.o

clean:
	-rm *.o *.core panther
	-rm -rf .libs

start:
	./panther 1061
