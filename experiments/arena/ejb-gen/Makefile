##
# Makefile
#
# $Id$

all:	usage

usage:
	@echo "Usage:"
	@echo "================================================================="
	@echo "- $(MAKE) run file=<source_file.java>"
	@echo "  Runs the generator on the specified java sourcecode file"
	@echo
	@echo "- $(MAKE) clean"
	@echo "  Cleans up class files"
	@echo "================================================================="

compile:
	javac -cp src/:$${JAVA_HOME}/lib/tools.jar `find src -name '*.java'`
    
run:	compile $(file)
	javadoc -docletpath src -sourcepath src -doclet net.xp_framework.ejb_gen.Generator $(file)

clean:
	find . -name '*.class' | xargs rm
