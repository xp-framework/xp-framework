# Makefile
#
# $Id$

MAKEEMBED=php -q php2cembed.php
PHPLIBDIR?=.
INCLUDEDIR?=/usr/local/include/php
LIBDIR?=/usr/local/lib
INCLUDES=-I$(INCLUDEDIR) -I$(INCLUDEDIR)/main -I$(INCLUDEDIR)/Zend -I$(INCLUDEDIR)/TSRM -I$(INCLUDEDIR)/sapi/embed
LIBDIRS=-L$(LIBDIR)
CC=gcc
CFLAGS=-g -Wall
LIBTOOL=$(SHELL) libtool

all:    usage
usage:
	@echo "Makes executables from PHP sourcecode"
	@echo ""
	@echo "Usage:"
	@echo "- make exe src=source.php"
	@echo "- make exe src=test.php INCLUDEDIR=/usr/src/php PHPLIBDIR=/usr/src/php"
	@echo ""
	@echo "Notes:"
	@echo "- INCLUDEDIR is the directory where PHPs includes are"
	@echo "- PHPLIBDIR is the directory where libphp4.la and libphp4.a reside"
	@echo ""

.if defined(src)
FILE=${src:S/.php//g}
c:
	@echo "===> Making executable from $(src)"
	@echo "---> Building for $(FILE)"
	$(MAKEEMBED) $(src) > $(FILE).c

$(FILE): $(FILE).o
	@echo "---> Linking for $(FILE)"
	$(LIBTOOL) --mode=link $(CC) $(CFLAGS) $(LIBDIRS) -o $(FILE) $(PHPLIBDIR)/libphp4.la $(LIBS) $(FILE).o
	strip $(FILE)

$(FILE).o: $(FILE).c
	@echo "---> Compiling for $(FILE)"
	$(CC) $(CFLAGS) $(INCLUDES) -c $(FILE).c

clean:
	@echo "---> Cleaning for $(FILE)"
	rm -f $(FILE).o $(FILE).c
	rmdir .libs
 
exe:    c $(FILE) clean
	@echo -n "---> Executable"
	@ls -al $(FILE)
	@echo "===> Done"

.endif
