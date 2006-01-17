LIBDIR?=/usr/local/lib
JAVA_HOME=/usr/local/jdk1.5.0/
INCLUDES=-I. -I$(JAVA_HOME)include/ -I$(JAVA_HOME)include/freebsd/ 
PHP_INCLUDES=$(shell php-config --includes)
CFLAGS=-g -O -pthread -D_REENTRANT -D_THREAD_SAFE -DPTHREADS -DZTS $(INCLUDES) $(PHP_INCLUDES) -L$(LIBDIR)

all: compile

PHPExecutor.class:  PHPExecutor.java
	javac PHPExecutor.java

PHPExecutor.h: 	  PHPExecutor.class
	javah -classpath .  -jni PHPExecutor

libphp.so:  PHPExecutor.h PHPExecutorImp.c executor_sapi.h executor_sapi.c libphp4.so
	gcc $(INCLUDES) $(PHP_INCLUDES) -shared -o libphp.so libphp4.so executor_sapi.c PHPExecutorImp.c

compile: libphp.so

run-command:
	@echo "LD_LIBRARY_PATH=. java -Djava.library.path=. -classpath . PHPExecutor"

clean:
	rm *.class *.so *.core hs_err_pid*.log
