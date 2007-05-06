XPCLI="../../../ports/technologies/env/bin/xpcli.php"
PHP?=php

test: performance unittest exampletests coretests

performance:
	$(PHP) use-many-classes.php
	$(PHP) recursion.php

unittest:
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.ClassLoaderTest
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.ResourcesTest
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.RuntimeClassDefinitionTest
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.PackageTest
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.FullyQualifiedTest

exampletests:
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner examples.LoggingClassLoaderTest

coretests:
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner ../../../ports/unittest/core.ini
