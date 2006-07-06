#
# $Id$
#

OUTPUT=output
CONTENT=output/content
TARGET=/home/httpd/xp.php3.de/doc_root/
XML=xml
CTREE=$(XML)/classTree.xpdoc.xml
INHERITANCE=$(XML)/inheritanceTree.xml
SABCMD=sabcmd 
SABPHP=php -C -q util/sabcmd.php
PHP?=php -q
PUBLISH=/tmp/xpdoc.tgz
CVSROOT=/home/cvs/repositories/xp/
XPROOT=/usr/local/lib/xp/

.PHONY: all clean install xml content $(TARGET)

default: usage
all: clean makexml gen install

usage:
	@echo "Usage: make <target>"
	@echo "            makexml - generate the xml-files out of the source"
	@echo "            gen     - apply xslt on the xml files"
	@echo "            install - install generated html in $(TARGET)"
	@echo "            clean   - remove all generated files (xml/html)"
	@echo "            all     - calls 'clean makexml gen install' in this order"
	@echo "            publish - publishes generated content"
	@echo "                      example: make publish target=user@host"
	@echo "            cvshist - updates CVS history XML file"
	@echo "                      example: make cvshist from=user@host"
	@echo "            release - makes release tarball"
	@echo "                      example: make release"
	@echo "                      example: make release at=user@host"
	@echo ""

release:
ifeq ($(at),)
	cd $(XPROOT)
	cvs update -dP 
	cd .. 
	tar cvfz $(TARGET)downloads/xp-`date +%Y-%m-%d`.tar.gz --exclude=CVS xp
	cd $(TARGET)downloads/
	rm xp-current.tar.gz
	ln -s xp-`date +%Y-%m-%d`.tar.gz xp-current.tar.gz
	md5 -q xp-`date +%Y-%m-%d`.tar.gz > xp-`date +%Y-%m-%d`.tar.gz.md5
else
	ssh $(at) \
	  cd $(XPROOT) \; \
	  cvs update -dP \; \
	  cd .. \; \
	  tar cvfz $(TARGET)downloads/xp-`date +%Y-%m-%d`.tar.gz --exclude=CVS xp \; \
	  cd $(TARGET)downloads/ \; \
	  rm xp-current.tar.gz \; \
	  ln -s xp-`date +%Y-%m-%d`.tar.gz xp-current.tar.gz \; \
	  md5 -q xp-`date +%Y-%m-%d`.tar.gz \> xp-`date +%Y-%m-%d`.tar.gz.md5
endif

cvshist:
	@echo "!==> This command has been disabled due to the switch to subversion."
ifeq ($(from),)
	@echo "*** Please specify from what host to retrieve CVS history file"
else
	#ssh $(from) cat $(CVSROOT)/CVSROOT/history | $(PHP) -dmemory_limit=16M util/history.php php://stdin > src/cvs_history.xml
	#-(cvs diff -u src/cvs_history.xml || true)
	#cvs com -m '- Auto-updated from CVS history file' src/cvs_history.xml
endif

publish:
ifeq ($(target),)
	@echo "*** Please specify a target"
else
	cd $(TARGET) ; tar cfz $(PUBLISH) --exclude=downloads *
	scp $(PUBLISH) $(target):$(TARGET)
	ssh $(target) cd $(TARGET) \; tar xfz xpdoc.tgz
	rm $(PUBLISH)
endif

$(XML):
	-mkdir $(XML)

$(OUTPUT):
	@echo "===> Preparing output directory"
	@-mkdir $(OUTPUT)
	@-mkdir $(OUTPUT)/content
	@-mkdir $(OUTPUT)/apidoc
	@-mkdir $(OUTPUT)/resources
	@-mkdir $(OUTPUT)/devel
	@-mkdir $(OUTPUT)/apidoc/collections
	@-mkdir $(OUTPUT)/apidoc/classes
	@-mkdir $(OUTPUT)/image
	@-mkdir $(OUTPUT)/src
	@-cp -R src/ $(OUTPUT)
	@-$(PHP) -C util/getSource.php --dest=$(OUTPUT)/src

install: $(TARGET)
	find $(OUTPUT) -name '.svn' -type d | xargs rm -rf {} \;
	cp -R $(OUTPUT)/* $(TARGET)
                
$(TARGET):
	@if [ ! -d $(TARGET) ] ; then mkdir $(TARGET) ; fi
	@if [ ! -d $(TARGET)/downloads ] ; then mkdir $(TARGET)/downloads ; fi

clean:
	-rm -rf output/
	-rm -rf xml/
        
gen: index content devel collections classes
	
makexml: $(XML)
	@$(PHP) xpdoku.php

index: $(OUTPUT) $(XML) news
	@echo "===> Creating index"
	@$(SABCMD) xslt/index.xsl content/index.xml $(OUTPUT)/index.html
	@echo "===> Creating copyright"
	@$(SABCMD) xslt/content.xsl content/copyright.xml $(OUTPUT)/copyright.html
	@echo "===> Creating credits"
	@$(SABCMD) xslt/content.xsl content/credits.xml $(OUTPUT)/credits.html
	@echo "===> Creating apidoc index"
	@$(SABCMD) xslt/apidoc.xsl $(CTREE) $(OUTPUT)/apidoc/index.html '$$area=documentation' 2>/dev/null
	@echo "===> Creating resources index"
	@$(SABCMD) xslt/content.xsl content/resources.xml $(OUTPUT)/resources/index.html '$$area=resources'
	@echo "===> Creating devel index"
	@$(SABCMD) xslt/devel.xsl content/devel.xml $(OUTPUT)/devel/index.html '$$area=devel'
	@echo "===> Creating highlighter"
	@$(SABCMD) xslt/showsource.xsl $(CTREE) $(OUTPUT)/showsource.php '$$area=documentation'
	@echo "===> Creating search page"
	@$(SABCMD) xslt/search.xsl $(CTREE) $(OUTPUT)/search.php

news: $(OUTPUT) $(XML)
ifneq ($(news),)
	@echo "===> Creating news from $(news)"
	cp $(news) $(OUTPUT)/news.rdf.xml   
else
	@echo "===> Creating news"
	@-$(PHP) util/news.php > $(OUTPUT)/news.rdf.xml
endif
	@$(SABCMD) xslt/news.xsl content/news.xml $(OUTPUT)/news.html

content: $(OUTPUT) $(XML)
	@echo "===> Creating main content"
	@for i in `find content/main/ -name '*.xml'` ; do \
	  f=`basename $$i | sed -e 's/\.xml$$//g'` ; \
	  echo "---> Processing $$f [$$i]" ; \
	  $(SABPHP) xslt/content.xsl $$i about > $(CONTENT)/$$f.html ; \
	done

devel: $(OUTPUT) $(XML)
	@echo "===> Creating devel content"
	@for i in `find content/devel/ -name '*.xml'` ; do \
	  f=`basename $$i | sed -e 's/\.xml$$//g'` ; \
	  echo "---> Processing $$f [$$i]" ; \
	  $(SABPHP) xslt/content.xsl $$i devel > $(OUTPUT)/devel/$$f.html ; \
	done

collections: $(OUTPUT) $(XML)
	@echo "===> Processing collections"
	@for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allcollections' | sed -e 's/<.*>//'`; do $(MAKE) -s "collection_$$k" ; done

classes: $(OUTPUT) $(XML)
	@echo "===> Processing class inheritance"
	@$(SABCMD) xslt/inheritance.xsl $(INHERITANCE) $(OUTPUT)/apidoc/inheritance.html '$$area=documentation'
	@echo "===> Processing classes"
	@for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allclasses' '$$area=documentation' | sed -e 's/<.*>//'`; do $(MAKE) -s class_$$k; done

collection_%:
	@echo "---> Processing collection $*"
	@$(SABCMD) xslt/collection.xsl $(CTREE) '$$mode=collection' '$$collection=$*' '$$area=documentation' '$$intro='`ls -1 content/collection/$*.xml 2>/dev/null` $(OUTPUT)/apidoc/collections/$*.html 2>/dev/null

class_%:
	@echo "---> Processing class $*"
	@$(SABCMD) xslt/class.xsl $(XML)/$*.xpdoc.xml $(OUTPUT)/apidoc/classes/$*.html '$$area=documentation' 2>/dev/null
