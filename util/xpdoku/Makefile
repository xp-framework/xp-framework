#
# $Id$
#

OUTPUT=output
TARGET=/home/httpd/xp.php3.de/doc_root/
XML=xml
CTREE=$(XML)/classTree.xpdoc.xml
SABCMD=sabcmd 
PHP=php -q

.PHONY: all clean install xml

default: usage
all: clean makexml gen install

usage:
	@echo "Usage: make <target>"
	@echo "            makexml - generate the xml-files out of the source"
	@echo "            gen     - apply xslt on the xml files"
	@echo "            install - install generated html in $(TARGET)"
	@echo "            clean   - remove all generated files (xml/html)"
	@echo "            all     - calls 'clean makexml gen install' in this order"

$(XML):
	-mkdir $(XML)

$(OUTPUT):
	@echo "===> Preparing output directory"
	@-mkdir $(OUTPUT)
	@-mkdir $(OUTPUT)/apidoc
	@-mkdir $(OUTPUT)/apidoc/collections
	@-mkdir $(OUTPUT)/apidoc/classes
	@-mkdir $(OUTPUT)/image
	@-mkdir $(OUTPUT)/src
	@-cp -R src/ $(OUTPUT)
	@-$(PHP) uril/getSource.php --dest=$(OUTPUT)/src

install:
	cp -R $(OUTPUT)/* $(TARGET)
                
$(TARGET):
	@-mkdir $(TARGET)

clean:
	-rm -rf output/
	-rm -rf xml/
        
gen: index collections classes
	
makexml: $(XML)
	@$(PHP) xpdoku.php

index: $(OUTPUT) $(XML)
	@echo "===> Creating index"
	@$(SABCMD) xslt/index.xsl $(CTREE) $(OUTPUT)/index.html 2>/dev/null
	@echo "===> Creating apidoc index"
	@$(SABCMD) xslt/apidoc.xsl $(CTREE) $(OUTPUT)/apidoc/index.html 2>/dev/null
	@echo "===> Creating highlighter"
	@$(SABCMD) xslt/showsource.xsl $(CTREE) $(OUTPUT)/showsource.php 2>/dev/null
	@echo "===> Creating search page"
	@$(SABCMD) xslt/search.xsl $(CTREE) $(OUTPUT)/search.php 2>/dev/null
        
collections: $(OUTPUT) $(XML)
	@echo "===> Processing collections"
	@for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allcollections' | sed -e 's/<.*>//'`; do $(MAKE) -s "collection_$$k" ; done

classes: $(OUTPUT) $(XML)
	@echo "===> Processing classes"
	@for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allclasses' | sed -e 's/<.*>//'`; do $(MAKE) -s class_$$k; done

collection_%:
	@echo "---> Processing collection $*"
	@$(SABCMD) xslt/collection.xsl $(CTREE) '$$mode=collection' '$$collection=$*' $(OUTPUT)/apidoc/collections/$*.html 2>/dev/null

class_%:
	@echo "---> Processing class $*"
	@$(SABCMD) xslt/class.xsl $(XML)/$*.xpdoc.xml $(OUTPUT)/apidoc/classes/$*.html 2>/dev/null
