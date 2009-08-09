<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.cmd.Console',
    'io.streams.MemoryInputStream',
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
      $this->original= array(
        Console::$in->getStream(), 
        Console::$out->getStream(), 
        Console::$err->getStream()
      );
      $this->streams= array(NULL, new MemoryOutputStream(), new MemoryOutputStream());
      Console::$out->setStream($this->streams[1]);
      Console::$err->setStream($this->streams[2]);
    }
    
    /**
     * Tear down testcase. Restores original standard output/error streams
     *
     */
    public function tearDown() {
      Console::$in->setStream($this->original[0]);
      Console::$out->setStream($this->original[1]);
      Console::$err->setStream($this->original[2]);
    }

    /**
     * Test read() method
     *
     */
    #[@test]
    public function read() {
      Console::$in->setStream(new MemoryInputStream('.'));
      $this->assertEquals('.', Console::read());
    }

    /**
     * Test readLine() method
     *
     */
    #[@test]
    public function readLineUnix() {
      Console::$in->setStream(new MemoryInputStream("Hello\nHallo"));
      $this->assertEquals('Hello', Console::readLine());
      $this->assertEquals('Hallo', Console::readLine());
    }

    /**
     * Test readLine() method
     *
     */
    #[@test]
    public function readLineMac() {
      Console::$in->setStream(new MemoryInputStream("Hello\rHallo"));
      $this->assertEquals('Hello', Console::readLine());
      $this->assertEquals('Hallo', Console::readLine());
    }

    /**
     * Test readLine() method
     *
     */
    #[@test]
    public function readLineWindows() {
      Console::$in->setStream(new MemoryInputStream("Hello\r\nHallo"));
      $this->assertEquals('Hello', Console::readLine());
      $this->assertEquals('Hallo', Console::readLine());
    }

    /**
     * Test read() method
     *
     */
    #[@test]
    public function readFromIn() {
      Console::$in->setStream(new MemoryInputStream('.'));
      $this->assertEquals('.', Console::$in->read(1));
    }
 
     /**
     * Test read() method
     *
     */
    #[@test]
    public function readLineFromIn() {
      Console::$in->setStream(new MemoryInputStream("Hello\nHallo\nOla"));
      $this->assertEquals('Hello', Console::$in->readLine());
      $this->assertEquals('Hallo', Console::$in->readLine());
      $this->assertEquals('Ola', Console::$in->readLine());
    }
   
    /**
     * Test write() method
     *
     */
    #[@test]
    public function write() {
      Console::write('.');
      $this->assertEquals('.', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeMultiple() {
      Console::write('.', 'o', 'O', '0');
      $this->assertEquals('.oO0', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeInt() {
      Console::write(1);
      $this->assertEquals('1', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeTrue() {
      Console::write(TRUE);
      $this->assertEquals('1', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeFalse() {
      Console::write(FALSE);
      $this->assertEquals('', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeFloat() {
      Console::write(1.5);
      $this->assertEquals('1.5', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeArray() {
      Console::write(array(1, 2, 3));
      $this->assertEquals(
        "[\n".
        "  0 => 1\n".
        "  1 => 2\n".
        "  2 => 3\n".
        "]", 
        $this->streams[1]->getBytes()
      );
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeMap() {
      Console::write(array('key' => 'value', 'color' => 'blue'));
      $this->assertEquals(
        "[\n".
        "  key => \"value\"\n".
        "  color => \"blue\"\n".
        "]", 
        $this->streams[1]->getBytes()
      );
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeObject() {
      Console::write(newinstance('lang.Object', array(), '{
        public function toString() { return "Hello"; }
      }'));
      $this->assertEquals('Hello', $this->streams[1]->getBytes());
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function exceptionFromToString() {
      try {
        Console::write(newinstance('lang.Object', array(), '{
          public function toString() { throw new IllegalStateException("Cannot render string"); }
        }'));
        $this->fail('Expected exception not thrown', NULL, 'lang.IllegalStateException');
      } catch (IllegalStateException $expected) {
        $this->assertEquals('', $this->streams[1]->getBytes());
      }
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeToOut() {
      Console::$out->write('.');
      $this->assertEquals('.', $this->streams[1]->getBytes());
    }
    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeToErr() {
      Console::$err->write('.');
      $this->assertEquals('.', $this->streams[2]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writef() {
      Console::writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[1]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writefToOut() {
      Console::$out->writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[1]->getBytes());
    }

    /**
     * Test writef() method
     *
     */
    #[@test]
    public function writefToErr() {
      Console::$err->writef('Hello "%s"', 'Timm');
      $this->assertEquals('Hello "Timm"', $this->streams[2]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLine() {
      Console::writeLine('.');
      $this->assertEquals(".\n", $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLineToOut() {
      Console::$out->writeLine('.');
      $this->assertEquals(".\n", $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLineToErr() {
      Console::$err->writeLine('.');
      $this->assertEquals(".\n", $this->streams[2]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinef() {
      Console::writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[1]->getBytes());
    }

    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinefToOut() {
      Console::$out->writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[1]->getBytes());
    }
    /**
     * Test writeLine() method
     *
     */
    #[@test]
    public function writeLinefToErr() {
      Console::$err->writeLinef('Hello %s', 'World');
      $this->assertEquals("Hello World\n", $this->streams[2]->getBytes());
    }
  }
?>
