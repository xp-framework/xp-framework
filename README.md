XP Framework
============
This is the XP Framework's development checkout


Directory structure
-------------------

	[root]
	|- core               # The XP Framework's core
    |  |- boot.pth        # Bootstrap classpath
    |  |- ChangeLog
    |  `- src             # Sourcecode, by Maven conventions
    |     |- main
    |     |- test
    |     `- resources
    |
    `- tools              # Entry point
	   |- tools           # Bootstrapping (class.php, xar.php, web.php)
       `- src             # Sourcecode, by Maven conventions
          `- main


Using it
--------
To use the the XP Framework development checkout, put the following
in your xp.ini file:

	# Windows
	use=[root]/core;~[root]/tools

	# Un*x
	use=[root]/core:~[root]/tools


Enjoy!

Build status
------------

[![Build Status](https://secure.travis-ci.org/xp-framework/xp-framework.png)](http://travis-ci.org/xp-framework/xp-framework)