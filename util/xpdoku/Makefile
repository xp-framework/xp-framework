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

default: all
all: clean gen install

$(XML):
	-mkdir $(XML)

$(OUTPUT):
	-mkdir $(OUTPUT)
	-mkdir $(OUTPUT)/collections
	-mkdir $(OUTPUT)/classes
	-mkdir $(OUTPUT)/image
	-cp -R src/ $(OUTPUT)

install:
	cp -R $(OUTPUT)/* $(TARGET)
                
$(TARGET):
	-mkdir $(TARGET)

clean:
	-rm -rvf output/
	-rm -rvf xml/
        
gen: index collections classes
	
makexml: $(XML)
	$(PHP) xpdoku.php

index: $(OUTPUT) $(XML)
	$(SABCMD) xslt/index.xsl $(CTREE) $(OUTPUT)/index.html
        
collections: $(OUTPUT) $(XML)
	for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allcollections' | sed -e 's/<.*>//'`; do $(MAKE) "collection_$$k" ; done

classes: $(OUTPUT) $(XML)
	for k in `$(SABCMD) xslt/helper.xsl $(CTREE) '$$mode=allclasses' | sed -e 's/<.*>//'`; do $(MAKE) class_$$k; done

collection_%:
	@echo "===> Making collection $*"
	@$(SABCMD) xslt/collection.xsl $(CTREE) '$$mode=collection' '$$collection=$*' $(OUTPUT)/collections/$*.html

class_%:
	@echo "---> Making class $*"
	@$(SABCMD) xslt/class.xsl $(XML)/$*.xpdoc.xml $(OUTPUT)/classes/$*.html
