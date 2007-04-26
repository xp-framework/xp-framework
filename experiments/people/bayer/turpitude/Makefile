################################################
# change these values accorgind to your system #
################################################
#JAVA_HOME = /home/nsn/jdk1.6.0
JAVA_HOME = /usr/lib/jvm/sun-jdk-1.6
#PHP_HOME = /home/nsn/devel/php/php-5.2.0
PHP_HOME = /home/nsn/devel/php/php-hax
############################################
PROPS = -Dnet.xp_framework.turpitude.ini.include_path=.:/path/to/xp
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

all: jar lib

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

arrays: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ArraySample

exceptions: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ExceptionSample

context: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ContextSample

invoke: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.InvocableSample

ini: turpitude.jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. $(PROPS) -cp $(CPATH) net.xp_framework.turpitude.samples.IniSample

exec: jar lib
	$(LIBPATH) $(JBIN) -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ScriptExec ./bin/object.php
	
fatal: jar lib
	$(LIBPATH) $(JBIN) -Dnet.xp_framework.turpitude.ini.magic_quotes_gpc=0 -Dnet.xp_framework.turpitude.ini.include_path=/home/nsn/devel/xp.public/trunk/skeleton -Djava.library.path=. -cp $(CPATH) net.xp_framework.turpitude.samples.ScriptExec ./bin/hw.php
	
classes: $(JSRC)
	$(JCC) -Xlint:unchecked  -d classes -cp $(CPATH) $(JSRC)

jarrun: jar lib
	$(JBIN) -jar turpitude.jar

jar: turpitude.jar

lib: libturpitude.so

headers: classes
	$(JAVAH) -classpath $(CPATH) -d ./src/native/include net.xp_framework.turpitude.PHPScriptEngine net.xp_framework.turpitude.PHPCompiledScript net.xp_framework.turpitude.PHPObject 

turpitude.jar: classes
	cd ./classes && $(JARBIN) -cvfm ../turpitude.jar META-INF/MANIFEST.MF META-INF/services/javax.script.ScriptEngineFactory net

libturpitude.so: $(SRC)
	$(CC) $(CCFLAGS) $(INCLUDES) $(PHP_INCLUDES) -shared -o libturpitude.so  $(PHP_HOME)/libs/libphp5.so $(SRC) 
	

