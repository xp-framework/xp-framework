XSLTPROC = xsltproc
XSLSS = data/xp5.php.xsl
GEN = de.schlund.db.cmd.DataSetCreator
DIR = definitions
PHP = xpcli
DIRS = $(shell ls -1 $(DIR))
OUTPUT = .

#Generation of Database
help:
	@echo '---------------------------------------------------------------------------'
	@echo 'Usage: 	make <target> DIRS="Directory1 Directory2 Directory3"' 
	@echo ' '
	@echo 'Targets: god - Generation of Database: Supply DIRS for specific Database(s)'
	@echo '               or leave it out for generation of xmls from all databases'
	@echo ''
	@echo '         php - Convert xml-files to php classes. DIRS can also be supplied'
	@echo ''
	@echo '         altgod + Pretty much the same as god and php. But you have to'
	@echo '         and    | specify the full path to the directory containing'
	@echo '         altphp | the config.ini-file.'
	@echo ''
	@echo '         clean - Will delete all xml and class.php files in the'
	@echo '                 subdirectories. Be careful (I am not kidding)'
	@echo '---------------------------------------------------------------------------'



god:
	-for i in $(DIRS); do \
	  echo $$i; \
	  $(PHP) $(GEN) -c definitions/$$i/config.ini -O $(DIR); \
	done;

php:
	for j in $(DIRS); do \
		for i in `find $(DIR)/$$j/tables -name *.xml`; do \
			echo $$i; \
			$(PHP) $(GEN) -c definitions/$$j/config.ini -X $$i -O $(OUTPUT) -S $(XSLSS) ; \
	        done; \
	done;

altgod:
	-for i in $(DIRS); do \
	  $(PHP) $(GEN) -c $$i/config.ini -O $(DIR); \
	done;


altphp:
	for j in $(DIRS); do \
		for i in `find $$j -name *.xml`; do \
			$(PHP) $(GEN) -c $$j/config.ini -X $$i -O $(OUTPUT); -S $(XSLSS)\
		done; \
	done;

clean:
	@find . -name *.xml -exec rm {} + &&\
	find . -name *.class.php -exec rm {} +; 
