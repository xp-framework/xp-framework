XPCLI="../../../util/bin/xpcli.php"
INC=".;./tests/lib/three-and-four.xar;../../../skeleton;../../../ports/classes"

test:
	php -dinclude_path=$(INC) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner tests.ClassLoaderTest
