#!/bin/sh

LD_LIBRARY_PATH=. java -Djava.library.path=. -classpath . PHPExecutor "$*"
