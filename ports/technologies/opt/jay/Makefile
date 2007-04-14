##
# Makefile for JAY
#
# $Id$

all	= jay
c	= src/closure.c src/error.c src/lalr.c src/lr0.c src/main.c src/mkpar.c \
      src/output.c src/reader.c src/symtab.c src/verbose.c src/warshall.c

CFLAGS=-g

phpJay:	$(c:.c=.o)	; $(CC) -o $@ $(CFLAGS) $(c:.c=.o)

clean:
	rm -f phpJay phpJay.exe src/*.o
