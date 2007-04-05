##
# Deployment makefile
#
# $Id$

# Remote deployment
targets?=

# Local deployment
to?=

all:
	@echo "Remote Deployer"
	@echo "======================================================================"
	@echo "Usage:"
	@echo "* Remote deployment:"
	@echo "  $(MAKE) rdeploy [targets=<user>@<host>:/path/to/deploy/ [<user>@<host>:/path/to/deploy/]]"
	@echo "  - targets defaults to $(targets)"
	@echo ""
	@echo "* Local deployment:"
	@echo "  $(MAKE) deploy [to=/path/to/deploy/]"
	@echo "  - deploy defaults to $(to)"
	@echo "======================================================================"

deploy:		dist/beans.jar
	cp dist/beans.jar $(to)

rdeploy:	dist/beans.jar
	@for item in $(targets) ; do \
	  target=`echo $$item | cut -d : -f 1` ; \
	  deploy=`echo $$item | cut -d : -f 2` ; \
	  echo "===> Deploying to $$item" ; \
	  scp dist/beans.jar $$target:/tmp && ssh $$target mv /tmp/beans.jar $$deploy ; \
	done
