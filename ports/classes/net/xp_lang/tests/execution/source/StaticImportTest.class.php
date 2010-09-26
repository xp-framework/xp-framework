<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest', 'io.streams.MemoryOutputStream');

  /**
   * Tests static imports
   *
   */
  class net·xp_lang·tests·execution·source·StaticImportTest extends ExecutionTest {
    protected $stream, $out= NULL;
  
    /**
     * Set up testcase and redirect console output to a memory stream
     *
     */
    public function setUp() {
      parent::setUp();
      $this->stream= new MemoryOutputStream();
      $this->out= Console::$out->getStream();
      Console::$out->setStream($this->stream);
    }
    
    /**
     * Set up testcase and restore console output
     *
     */
    public function tearDown() {
      Console::$out->setStream($this->out);
      delete($this->stream);
    }

    /**
     * Test util.cmd.Console.*
     *
     */
    #[@test]
    public function importAll() {
      $this->run(
        'writeLine("Hello");', 
        array('import static util.cmd.Console.*;')
      );
      $this->assertEquals("Hello\n", $this->stream->getBytes());
    }

    /**
     * Test util.cmd.Console.writeLine
     *
     */
    #[@test]
    public function importSpecific() {
      $this->run(
        'writeLine("Hello");', 
        array('import static util.cmd.Console.writeLine;')
      );
      $this->assertEquals("Hello\n", $this->stream->getBytes());
    }

    /**
     * Test peer.http.HttpConstants.*;
     *
     */
    #[@test]
    public function importConst() {
      $this->run(
        'util.cmd.Console::writeLine(STATUS_OK);', 
        array('import static peer.http.HttpConstants.*;')
      );
      $this->assertEquals("200\n", $this->stream->getBytes());
    }

    /**
     * Test self.*;
     *
     */
    #[@test]
    public function importSelf() {
      $class= $this->define('class', 'ImportSelfTest', NULL, '{
        public static string join(string $a, string $b) {
          return $a ~ " " ~ $b;
        }
        
        public string run() {
          return join("Hello", "World");
        }
      }', array('import static self.*;'));
      $this->assertEquals('Hello World', $class->newInstance()->run());
    }
  }
?>
