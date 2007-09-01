<?php
/* This class is part of the XP framework
 *
 * $Id: ConsoleTest.class.php 10072 2007-04-21 18:58:42Z friebe $ 
 */

  namespace net::xp_framework::unittest::util::cmd;

  ::uses(
    'util.cmd.Console',
    'io.streams.StringWriter', 
    'io.streams.MemoryOutputStream',
    'unittest.TestCase'
  );

  /**
   * TestCase
   *
   * @see      xp://util.cmd.Console
   * @purpose  purpose
   */
  class ConsoleTest extends unittest::TestCase {
    protected
      $original = array(),
      $streams  = array();

    /**
     * Sets up test case. Redirects console standard output/error streams
     * to memory streams
     *
     */
    public function setUp() {
      $this->original= array(clone util::cmd::Console::$out, clone util::cmd::Console::$err);
      $this->streams= array(new io::streams::MemoryOutputStream(), new io::streams::MemoryOutputStream());
      util::cmd::Console::$out= new io::streams::StringWriter($this->streams[0]);
      util::cmd::Console::$err= new io::streams::StringWriter($this->streams[1]);
    }
    
    /**
     * Tear down testcase. Restores original standard output/error streams
     *
     */
    public function tearDown() {
      util::cmd::Console::$out= $this->original[0];
      util::cmd::Console::$err= $this->original[1];
    }
    
    /**
     * Test write() method
     *
     */
    #[@test]
    public function write() {
      util::cmd::Console::write('.');
      $this->assertEquals('.', $this->streams[0]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeToOut() {
      util::cmd::Console::$out->write('.');
      $this->assertEquals('.', $this->streams[0]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeToErr() {
      util::cmd::Console::$err->write('.');
      $this->assertEquals('.', $this->streams[1]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writef() {
      util::cmd::Console::writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[0]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writefToOut() {
      util::cmd::Console::$out->writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[0]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writefToErr() {
      util::cmd::Console::$err->writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLine() {
      util::cmd::Console::writeLine('.');
      $this->assertEquals(".\n", $this->streams[0]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLineToOut() {
      util::cmd::Console::$out->writeLine('.');
      $this->assertEquals(".\n", $this->streams[0]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLineToErr() {
      util::cmd::Console::$err->writeLine('.');
      $this->assertEquals(".\n", $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinef() {
      util::cmd::Console::writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[0]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinefToOut() {
      util::cmd::Console::$out->writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[0]->getBytes());
    }
    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinefToErr() {
      util::cmd::Console::$err->writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[1]->getBytes());
    }
  }
?>
