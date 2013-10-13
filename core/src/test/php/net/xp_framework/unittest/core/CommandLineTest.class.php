<?php namespace net\xp_framework\unittest\core;

use lang\CommandLine;

/**
 * TestCase
 *
 * @see      xp://lang.CommandLine
 */
class CommandLineTest extends \unittest\TestCase {

  #[@test]
  public function forWindows() {
    $this->assertEquals(CommandLine::$WINDOWS, CommandLine::forName('Windows'));
  }

  #[@test]
  public function forWinNT() {
    $this->assertEquals(CommandLine::$WINDOWS, CommandLine::forName('WINNT'));
  }

  #[@test]
  public function forBSD() {
    $this->assertEquals(CommandLine::$UNIX, CommandLine::forName('FreeBSD'));
  }

  #[@test]
  public function forLinux() {
    $this->assertEquals(CommandLine::$UNIX, CommandLine::forName('Linux'));
  }

  #[@test]
  public function noquotingWindows() {
    $this->assertEquals('php -v', CommandLine::$WINDOWS->compose('php', array('-v')));
  }

  #[@test]
  public function noquotingUnix() {
    $this->assertEquals('php -v', CommandLine::$UNIX->compose('php', array('-v')));
  }

  #[@test]
  public function emptyArgumentQuotingWindows() {
    $this->assertEquals('echo "" World', CommandLine::$WINDOWS->compose('echo', array('', 'World')));
  }

  #[@test]
  public function emptyArgumentQuotingUnix() {
    $this->assertEquals("echo '' World", CommandLine::$UNIX->compose('echo', array('', 'World')));
  }

  #[@test]
  public function commandIsQuotedWindows() {
    $this->assertEquals(
      '"C:/Users/Timm Friebe/php" -v', 
      CommandLine::$WINDOWS->compose('C:/Users/Timm Friebe/php', array('-v'))
    );
  }

  #[@test]
  public function commandIsQuotedUnix() {
    $this->assertEquals(
      "'/Users/Timm Friebe/php' -v", 
      CommandLine::$UNIX->compose('/Users/Timm Friebe/php', array('-v'))
    );
  }

  #[@test]
  public function argumentsContainingSpacesAreQuotedWindows() {
    $this->assertEquals(
      'php -r "a b"',
      CommandLine::$WINDOWS->compose('php', array('-r', 'a b'))
    );
  }

  #[@test]
  public function argumentsContainingSpacesAreQuotedUnix() {
    $this->assertEquals(
      "php -r 'a b'",
      CommandLine::$UNIX->compose('php', array('-r', 'a b'))
    );
  }

  #[@test]
  public function quotesInArgumentsAreEscapedWindows() {
    $this->assertEquals(
      'php -r "a"""b"',
      CommandLine::$WINDOWS->compose('php', array('-r', 'a"b'))
    );
  }

  #[@test]
  public function quotesInArgumentsAreEscapedUnix() {
    $this->assertEquals(
      "php -r 'a'\''b'",
      CommandLine::$UNIX->compose('php', array('-r', "a'b"))
    );
  }
  
  #[@test]
  public function emptyArgsWindows() {
    $this->assertEquals(
      array('C:\\Windows\\Explorer.EXE'),
      CommandLine::$WINDOWS->parse('C:\\Windows\\Explorer.EXE')
    );
  }

  #[@test]
  public function emptyArgsUnix() {
    $this->assertEquals(
      array('/etc/init.d/apache'),
      CommandLine::$UNIX->parse('/etc/init.d/apache')
    );
  }

  #[@test]
  public function guidArgWindows() {
    $this->assertEquals(
      array('taskeng.exe', '{58B7C886-2D94-4DBF-BBB9-96608B332124}'),
      CommandLine::$WINDOWS->parse('taskeng.exe {58B7C886-2D94-4DBF-BBB9-96608B332124}')
    );
  }

  #[@test]
  public function guidArgUnix() {
    $this->assertEquals(
      array('guid', '{58B7C886-2D94-4DBF-BBB9-96608B332124}'),
      CommandLine::$WINDOWS->parse('guid {58B7C886-2D94-4DBF-BBB9-96608B332124}')
    );
  }

  #[@test]
  public function quotedCommandWindows() {
    $this->assertEquals(
      array('C:\\Program Files\\Windows Sidebar\\sidebar.exe', '/autoRun'),
      CommandLine::$WINDOWS->parse('"C:\\Program Files\\Windows Sidebar\\sidebar.exe" /autoRun')
    );
  }

  #[@test]
  public function quotedCommandUnix() {
    $this->assertEquals(
      array('/opt/MySQL Daemon/bin/mysqld', '--pid-file=/var/mysql.pid'),
      CommandLine::$UNIX->parse("'/opt/MySQL Daemon/bin/mysqld' --pid-file=/var/mysql.pid")
    );
  }

  #[@test]
  public function doubleQuotedCommandUnix() {
    $this->assertEquals(
      array('/opt/MySQL Daemon/bin/mysqld', '--pid-file=/var/mysql.pid'),
      CommandLine::$UNIX->parse('"/opt/MySQL Daemon/bin/mysqld" --pid-file=/var/mysql.pid')
    );
  }

  #[@test]
  public function quotedArgumentPartWindows() {
    $this->assertEquals(
      array('C:/usr/bin/php', '-q', '-dinclude_path=.:/usr/share', '-dmagic_quotes_gpc=Off'),
      CommandLine::$WINDOWS->parse('C:/usr/bin/php -q -dinclude_path=".:/usr/share" -dmagic_quotes_gpc=Off')
    );        
  }

  #[@test]
  public function quotedArgumentPartUnix() {
    $this->assertEquals(
      array('/usr/bin/php', '-q', '-dinclude_path=".:/usr/share"', '-dmagic_quotes_gpc=Off'),
      CommandLine::$UNIX->parse('/usr/bin/php -q -dinclude_path=".:/usr/share" -dmagic_quotes_gpc=Off')
    );        
  }

  #[@test]
  public function quotedCommandAndArgumentPartWindows() {
    $this->assertEquals(
      array('C:/usr/bin/php', '-q', '-dinclude_path=.:/usr/share', '-dmagic_quotes_gpc=Off'),
      CommandLine::$WINDOWS->parse('"C:/usr/bin/php" -q -dinclude_path=".:/usr/share" -dmagic_quotes_gpc=Off')
    );
  }

  #[@test]
  public function quotedCommandAndArgumentPartUnix() {
    $this->assertEquals(
      array('/usr/bin/php', '-q', '-dinclude_path=".:/usr/share"', '-dmagic_quotes_gpc=Off'),
      CommandLine::$UNIX->parse('"/usr/bin/php" -q -dinclude_path=".:/usr/share" -dmagic_quotes_gpc=Off')
    );
  }

  #[@test]
  public function quotedArgumentWindows() {
    $this->assertEquals(
      array('nedit', '/mnt/c/Users/Mr. Example/notes.txt'),
      CommandLine::$WINDOWS->parse('nedit "/mnt/c/Users/Mr. Example/notes.txt"')
    );
  }

  #[@test]
  public function doubleQuotedArgumentUnix() {
    $this->assertEquals(
      array('nedit', '/mnt/c/Users/Mr. Example/notes.txt'),
      CommandLine::$UNIX->parse("nedit '/mnt/c/Users/Mr. Example/notes.txt'")
    );
  }

  #[@test]
  public function quotedArgumentUnix() {
    $this->assertEquals(
      array('nedit', '/mnt/c/Users/Mr. Example/notes.txt'),
      CommandLine::$UNIX->parse('nedit "/mnt/c/Users/Mr. Example/notes.txt"')
    );
  }

  #[@test]
  public function quotedArgumentsWindows() {
    $this->assertEquals(
      array('nedit', '/mnt/c/Users/Mr. Example/notes.txt', '../All Notes.txt'),
      CommandLine::$WINDOWS->parse('nedit "/mnt/c/Users/Mr. Example/notes.txt" "../All Notes.txt"')
    );
  }

  #[@test]
  public function quotedArgumentsUnix() {
    $this->assertEquals(
      array('nedit', '/mnt/c/Users/Mr. Example/notes.txt', '../All Notes.txt'),
      CommandLine::$UNIX->parse("nedit '/mnt/c/Users/Mr. Example/notes.txt' '../All Notes.txt'")
    );
  }

  #[@test]
  public function doubleQuotedArgumentsUnix() {
    $this->assertEquals(
      array('nedit', '/mnt/c/Users/Mr. Example/notes.txt', '../All Notes.txt'),
      CommandLine::$UNIX->parse('nedit "/mnt/c/Users/Mr. Example/notes.txt" "../All Notes.txt"')
    );
  }

  #[@test]
  public function evalCommandLineWindows() {
    $cmd= 'xp xp.runtime.Evaluate "echo """Hello World""";"';
    $this->assertEquals(
      array('xp', 'xp.runtime.Evaluate', 'echo "Hello World";'),
      CommandLine::$WINDOWS->parse($cmd)
    );
  }

  #[@test]
  public function evalCommandLineWindowsUnclosed() {
    $cmd= 'xp xp.runtime.Evaluate "1+ 2';
    $this->assertEquals(
      array('xp', 'xp.runtime.Evaluate', '1+ 2'),
      CommandLine::$WINDOWS->parse($cmd)
    );
  }

  #[@test]
  public function evalCommandLineWindowsUnclosedTriple() {
    $cmd= 'xp xp.runtime.Evaluate "echo """Hello World';
    $this->assertEquals(
      array('xp', 'xp.runtime.Evaluate', 'echo "Hello World'),
      CommandLine::$WINDOWS->parse($cmd)
    );
  }

  #[@test]
  public function evalCommandLineWindowsTripleClosedBySingle() {
    $cmd= 'xp xp.runtime.Evaluate "echo """Hello World" a';
    $this->assertEquals(
      array('xp', 'xp.runtime.Evaluate', 'echo "Hello World', 'a'),
      CommandLine::$WINDOWS->parse($cmd)
    );
  }
}
