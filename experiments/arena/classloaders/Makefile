XPCLI="../../../ports/technologies/env/bin/xpcli.php"

test: performance unittest

performance:
	php use-many-classes.php
	php recursion.php

unittest:
	php $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.ClassLoaderTest
	php $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.ResourcesTest
	php $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.RuntimeClassDefinitionTest
