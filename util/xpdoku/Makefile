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
PHP=php -q
PUBLISH=/tmp/xpdoc.tgz
CVSROOT=/home/cvs/repositories/xp/

.PHONY: all clean install xml content

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
	@echo ""

cvshist:
ifeq ($(from),)
	@echo "*** Please specify from what host to retrieve CVS history file"
else
	ssh $(from) cat $(CVSROOT)/CVSROOT/history | $(PHP) util/history.php php://stdin > src/cvs_history.xml
	-cvs diff -u src/cvs_history.xml
	#cvs com -m '- Auto-updated from CVS history file' src/cvs_history.xml
endif

publish:
ifeq ($(target),)
	@echo "*** Please specify a target"
else
	cd $(TARGET) ; tar cfz $(PUBLISH) *
	scp $(PUBLISH) $(target):$(TARGET)
	ssh $(target) cd $(TARGET) \; tar xfz xpdoc.tgz
	scp content/news.rdf.xml $(target):$(TARGET)
	rm $(PUBLISH)
endif

$(XML):
	-mkdir $(XML)

$(OUTPUT):
	@echo "===> Preparing output directory"
	@-mkdir $(OUTPUT)
	@-mkdir $(OUTPUT)/content
	@-mkdir $(OUTPUT)/apidoc
	@-mkdir $(OUTPUT)/apidoc/collections
	@-mkdir $(OUTPUT)/apidoc/classes
	@-mkdir $(OUTPUT)/image
	@-mkdir $(OUTPUT)/src
	@-cp -R src/ $(OUTPUT)
	@-$(PHP) -C util/getSource.php --dest=$(OUTPUT)/src

install:
	cp -R $(OUTPUT)/* $(TARGET)
                
$(TARGET):
	@-mkdir $(TARGET)

clean:
	-rm -rf output/
	-rm -rf xml/
        
gen: index content collections classes
	
makexml: $(XML)
	@$(PHP) xpdoku.php

index: $(OUTPUT) $(XML) news
	@echo "===> Creating index"
	@$(SABCMD) xslt/index.xsl content/index.xml $(OUTPUT)/index.html 2>/dev/null
	@echo "===> Creating copyright"
	@$(SABCMD) xslt/content.xsl content/copyright.xml $(OUTPUT)/copyright.html 2>/dev/null
	@echo "===> Creating credits"
	@$(SABCMD) xslt/content.xsl content/credits.xml $(OUTPUT)/credits.html 2>/dev/null
	@echo "===> Creating apidoc index"
	@$(SABCMD) xslt/apidoc.xsl $(CTREE) $(OUTPUT)/apidoc/index.html 2>/dev/null
	@echo "===> Creating highlighter"
	@$(SABCMD) xslt/showsource.xsl $(CTREE) $(OUTPUT)/showsource.php 2>/dev/null
	@echo "===> Creating search page"
	@$(SABCMD) xslt/search.xsl $(CTREE) $(OUTPUT)/search.php 2>/dev/null

news:
ifneq ($(news),)
	@echo "===> Creating news from $(news)"
	cp $(news) content/news.rdf.xml   
else
	@echo "===> Creating news"
	@-$(PHP) util/news.php > content/news.rdf.xml
endif
	@$(SABCMD) xslt/news.xsl content/news.xml $(OUTPUT)/news.html 2>/dev/null

content:
	@echo "===> Creating main content"
	@for i in `find content/main/ -name '*.xml'` ; do \
	  f=`basename $$i | sed -e 's/\.xml$$//g'` ; \
	  echo "---> Processing $$f" ; \
	  $(SABPHP) xslt/content.xsl $$i > $(CONTENT)/$$f.html ; \
	done

collections: $(OUTPUT) $(XML)
	@echo "===> Processing collections"
	@for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allcollections' | sed -e 's/<.*>//'`; do $(MAKE) -s "collection_$$k" ; done

classes: $(OUTPUT) $(XML)
	@echo "===> Processing class inheritance"
	@$(SABCMD) xslt/inheritance.xsl $(INHERITANCE) $(OUTPUT)/apidoc/inheritance.html 2>/dev/null
	@echo "===> Processing classes"
	@for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allclasses' | sed -e 's/<.*>//'`; do $(MAKE) -s class_$$k; done

collection_%:
	@echo "---> Processing collection $*"
	@$(SABCMD) xslt/collection.xsl $(CTREE) '$$mode=collection' '$$collection=$*' '$$intro='`ls -1 content/collection/$*.xml 2>/dev/null` $(OUTPUT)/apidoc/collections/$*.html 2>/dev/null

class_%:
	@echo "---> Processing class $*"
	@$(SABCMD) xslt/class.xsl $(XML)/$*.xpdoc.xml $(OUTPUT)/apidoc/classes/$*.html 2>/dev/null
