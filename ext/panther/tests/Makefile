##
# This file is part of the XP extension "panter"
#
# $Id$

CFLAGS=-Wall -pipe -O -g

all:
	for i in `ls -1 *.c` ; do $(MAKE) `echo $$i | sed -e 's/\.c$$//'` ; done

parserequest:   rmi.o parserequest.c
	${CC} $(CFLAGS) -lmd -I.. rmi.o parserequest.c -o parserequest

response:   rmi.o response.c
	${CC} $(CFLAGS) -lmd -I.. rmi.o response.c -o response

rmi.o:	../rmi.c

clean:
	-rm *.o *.core
	-for i in `ls -1 *.c` ; do rm `echo $$i | sed -e 's/\.c$$//'` ; done
