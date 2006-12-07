JCC = /home/nsn/jdk1.6.0/bin/javac
JBIN = /home/nsn/jdk1.6.0/bin/java
JSRC = $(shell find ./src/java -type f -name \*.java)
CPATH = ./classes
LIBRARY_PATH= ./natives

all: classes

version:
	$(JBIN) -version

list: classes
	$(JBIN) -cp $(CPATH) net.xp_framework.turpitude.samples.EngineList

classes: $(JSRC)
	@echo $(JSRC)
	$(JCC) -d classes -cp $(CPATH) $(JSRC)


