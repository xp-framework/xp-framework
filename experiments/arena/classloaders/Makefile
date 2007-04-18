XPCLI="../../../ports/technologies/env/bin/xpcli.php"
INC=".;./tests/lib/three-and-four.xar;../../../skeleton;../../../ports/classes"

test:
	php use-many-classes.php
	php recursion.php
	php -dinclude_path=$(INC) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.ClassLoaderTest
	php -dinclude_path=$(INC) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner net.xp_framework.unittest.reflection.ClassLoaderTest
