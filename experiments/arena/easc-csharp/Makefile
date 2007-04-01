##
# Makefile for C# EASC
#
# $Id$

CSC=/cygdrive/c/WINDOWS/Microsoft.NET/Framework/v2.0.50727/csc.exe
CSC_OPT=/nologo

all:
	@echo "- $(MAKE) build"
	@echo "  Builds the EASC library and the Test Runner client"
	@echo ""
	@echo "- $(MAKE) run"
	@echo "  Runs the Test Runner"
	@echo ""

library:
	cd EASCLibrary && $(CSC) $(CSC_OPT) /t:library /out:bin/release/EASCLibrary.dll /recurse:'*.cs'
	cp EASCLibrary/bin/release/EASCLibrary.dll TestRunnerClient/bin/release/EASCLibrary.dll

testrunner:
	cd TestRunnerClient && $(CSC) $(CSC_OPT) /t:exe /out:bin/release/TestRunnerClient.exe /r:bin/release/EASCLibrary.dll /recurse:'*.cs'

build: library testrunner
	@echo "===> Build complete"

run: build
	TestRunnerClient/bin/release/TestRunnerClient.exe
