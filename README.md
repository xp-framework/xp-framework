XP Framework
============
[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/xp-framework.png)](http://travis-ci.org/xp-framework/xp-framework)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/xp-framework/blob/master/core/src/main/php/LICENCE)
[![Required PHP 5.3+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_3plus.png)](http://php.net/)


This is the XP Framework's development checkout for the 5.X-SERIES. 

***Note: Development now focusses on the 6.X-SERIES, which are maintained in the [xp-framework/core](https://github.com/xp-framework/core) repository. Bugfixes may be backported, but new features will usually only go to 6.X***

Installation
------------
Clone this repository, e.g. using Git Read-Only:

```sh
$ cd [path]
$ git clone git://github.com/xp-framework/xp-framework.git
```

### Directory structure
```
[path]/xp-framework
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

### Runners
The entry point for software written in the XP Framework is not the PHP
interpreter's CLI / web server API but either a command line runner or
a specialized *web* entry point. These runners can be installed by using
the following one-liner:

```sh
$ cd ~/bin
$ wget 'https://github.com/xp-framework/xp-runners/releases/download/v5.7.2/setup' -O - | php
```

### Using it
To use the the XP Framework development checkout, put the following
in your `~/bin/xp.ini` file:

```ini
use=[path]/xp-framework/core
```

**Enjoy!**

Contributing
------------
To contribute, use the GitHub way - fork, hack, and submit a pull request!
