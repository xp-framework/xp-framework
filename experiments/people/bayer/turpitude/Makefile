################################################
# change these values accorgind to your system #
################################################
JAVA_HOME = /home/nsn/jdk1.6.0
PHP_HOME = /home/nsn/devel/php/php-5.2.0
############################################
JCC = $(JAVA_HOME)/bin/javac
JBIN = $(JAVA_HOME)/bin/java
JARBIN = $(JAVA_HOME)/bin/jar
JAVAH = $(JAVA_HOME)/bin/javah
JSRC = $(shell find ./src/java -type f -name \*.java)
SRC = $(shell find ./src/native -type f -name \*.cpp)
CPATH = ./classes:./turpitude.jar
INCLUDES = -I./src/native/include -I$(JAVA_HOME)/include/ -I$(JAVA_HOME)/include/linux
PHP_INCLUDES = -I$(PHP_HOME) -I$(PHP_HOME)/main -I$(PHP_HOME)/TSRM -I$(PHP_HOME)/Zend -I$(PHP_HOME)/ext
CC= gcc
CCFLAGS = -fpic -lstdc++
LIBPATH = LD_LIBRARY_PATH=.:$(PHP_HOME)/libs

all: turpitude.jar

version:
	$(JBIN) -version

list: turpitude.jar lib
	$(JBIN) -cp $(CPATH) net.xp_framework.turpitude.samples.EngineList

hello: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.HelloWorld

compiledscript: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.CompileSample

objects: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ObjectSample

exceptions: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ExceptionSample

context: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ContextSample

exec: jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ScriptExec ./bin/object.php
	
classes: $(JSRC)
	$(JCC)  -d classes -cp $(CPATH) $(JSRC)

jarrun: jar lib
	$(JBIN) -jar turpitude.jar

jar: turpitude.jar

lib: turpitude.so

headers: classes
	$(JAVAH) -classpath $(CPATH) -d ./src/native/include net.xp_framework.turpitude.PHPScriptEngine

turpitude.jar: classes
	cd ./classes && $(JARBIN) -cvfm ../turpitude.jar META-INF/MANIFEST.MF META-INF/services/javax.script.ScriptEngineFactory net

turpitude.so:
	$(CC) $(CCFLAGS) $(INCLUDES) $(PHP_INCLUDES) -shared -o libturpitude.so $(PHP_HOME)/libs/libphp5.so $(SRC) 
	

