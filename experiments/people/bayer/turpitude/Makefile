JCC = /home/nsn/jdk1.6.0/bin/javac
JBIN = /home/nsn/jdk1.6.0/bin/java
JARBIN = /home/nsn/jdk1.6.0/bin/jar
JSRC = $(shell find ./src/java -type f -name \*.java)
CPATH = ./classes:./turpitude.jar
LIBRARY_PATH= ./natives

all: turpitude.jar

version:
	$(JBIN) -version

list: turpitude.jar
	$(JBIN) -cp $(CPATH) net.xp_framework.turpitude.samples.EngineList

classes: $(JSRC)
	$(JCC)  -d classes -cp $(CPATH) $(JSRC)

jarrun: jar
	$(JBIN) -jar turpitude.jar

jar: turpitude.jar

turpitude.jar: classes
	cd ./classes && $(JARBIN) -cvfm ../turpitude.jar META-INF/MANIFEST.MF META-INF/services/javax.script.ScriptEngineFactory net

