JCC = /home/nsn/jdk1.6.0/bin/javac
JBIN = /home/nsn/jdk1.6.0/bin/java
JSRC = $(shell find ./src/java -type f -name \*.java)
CPATH = ./classes:./turpitude.jar
LIBRARY_PATH= ./natives

all: turpitude.jar

version:
	$(JBIN) -version

list: turpitude.jar
	$(JBIN) -cp $(CPATH) net.xp_framework.turpitude.samples.EngineList

classes: $(JSRC)
	@echo $(JSRC)
	$(JCC)  -d classes -cp $(CPATH) $(JSRC)

turpitude.jar: classes
	cd ./src/java && jar -cvf ../../turpitude.jar META-INF/services/javax.script.ScriptEngineFactory `find ./ -type f -name \*.java |grep -v samples`

