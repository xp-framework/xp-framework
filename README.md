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
	   |- tools           # Bootstrappung
       `- src             # Sourcecode, by Maven conventions
          `- main


Using it
--------
To use the the XP Framework development checkout, put the following
in your xp.ini file:

	# Windows
	use=[root]/xp-framework/core;~[root]/xp-framework/tools

	# Un*x
	use=[root]/xp-framework/core:~[root]/xp-framework/tools


Enjoy!
