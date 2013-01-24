XP Framework
============
[![Build Status](https://secure.travis-ci.org/xp-framework/xp-framework.png)](http://travis-ci.org/xp-framework/xp-framework)

This is the XP Framework's development checkout


Directory structure
-------------------

```
[root]
`- core
   |- ChangeLog         # Version log
   |- boot.pth          # Bootstrap classpath
   |- tools             # Bootstrapping (lang.base.php, class.php, xar.php, web.php)
   `- src               # Sourcecode, by Maven conventions
      |- main
      |  `- php
      `- test
         |- php
         |- config      # Unittest configuration
         `- resources   # Test resources
```

Using it
--------
To use the the XP Framework development checkout, put the following
in your xp.ini file:

```ini
use=[root]/core
```

Enjoy!
