<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.cmd.Console',
    'io.streams.MemoryOutputStream',
    'unittest.TestCase'
  );

  /**
   * TestCase
   *
   * @see      xp://util.cmd.Console
   * @purpose  purpose
   */
  class ConsoleTest extends TestCase {
    protected
      $original = array(),
      $streams  = array();

    /**
     * Sets up test case. Redirects console standard output/error streams
     * to memory streams
     *
     */
    public function setUp() {
      $this->original= array(Console::$out->getStream(), clone Console::$err->getStream());
      $this->streams= array(new MemoryOutputStream(), new MemoryOutputStream());
      Console::$out->setStream($this->streams[0]);
      Console::$err->setStream($this->streams[1]);
    }
    
    /**
     * Tear down testcase. Restores original standard output/error streams
     *
     */
    public function tearDown() {
      Console::$out->setStream($this->original[0]);
      Console::$err->setStream($this->original[1]);
    }
    
    /**
     * Test write() method
     *
     */
    #[@test]
    public function write() {
      Console::write('.');
      $this->assertEquals('.', $this->streams[0]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeToOut() {
      Console::$out->write('.');
      $this->assertEquals('.', $this->streams[0]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeToErr() {
      Console::$err->write('.');
      $this->assertEquals('.', $this->streams[1]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writef() {
      Console::writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[0]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writefToOut() {
      Console::$out->writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[0]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writefToErr() {
      Console::$err->writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLine() {
      Console::writeLine('.');
      $this->assertEquals(".\n", $this->streams[0]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLineToOut() {
      Console::$out->writeLine('.');
      $this->assertEquals(".\n", $this->streams[0]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLineToErr() {
      Console::$err->writeLine('.');
      $this->assertEquals(".\n", $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinef() {
      Console::writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[0]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinefToOut() {
      Console::$out->writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[0]->getBytes());
    }
    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinefToErr() {
      Console::$err->writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[1]->getBytes());
    }
  }
?>
