XPCLI="../../../ports/technologies/env/bin/xpcli.php"
PHP?=php

test: performance exampletests coretests

performance:
	$(PHP) use-many-classes.php
	$(PHP) recursion.php

exampletests:
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner examples.LoggingClassLoaderTest

coretests:
	$(PHP) $(XPCLI) net.xp_framework.unittest.runner.cli.CliRunner ../../../ports/unittest/core.ini
