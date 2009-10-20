<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.TextWriter',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.TextWriter
   */
  class TextWriterTest extends TestCase {
    protected $out= NULL;

    /**
     * Returns a text writer for a given output string.
     *
     * @param   string charset
     * @return  io.streams.TextWriter
     */
    protected function newWriter($charset= NULL) {
      $this->out= new MemoryOutputStream();
      return new TextWriter($this->out, $charset);
    }
    
    /**
     * Test writing text
     *
     */
    #[@test]
    public function write() {
      $this->newWriter()->write('Hello');
      $this->assertEquals('Hello', $this->out->getBytes());
    }

    /**
     * Test writing text
     *
     */
    #[@test]
    public function writeOne() {
      $this->newWriter()->write('H');
      $this->assertEquals('H', $this->out->getBytes());
    }

    /**
     * Test writing text
     *
     */
    #[@test]
    public function writeEmpty() {
      $this->newWriter()->write('');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test writing a text and a line
     *
     */
    #[@test]
    public function writeLine() {
      $this->newWriter()->writeLine('Hello');
      $this->assertEquals("Hello\n", $this->out->getBytes());
    }

    /**
     * Test writing a line
     *
     */
    #[@test]
    public function writeEmptyLine() {
      $this->newWriter()->writeLine();
      $this->assertEquals("\n", $this->out->getBytes());
    }

    /**
     * Test "\n" is the default line separator
     *
     */
    #[@test]
    public function unixLineSeparatorIsDefault() {
      $this->assertEquals("\n", $this->newWriter()->getNewLine());
    }

    /**
     * Test setNewLine() method
     *
     */
    #[@test]
    public function setNewLine() {
      $w= $this->newWriter();
      $w->setNewLine("\r");
      $this->assertEquals("\r", $w->getNewLine());
    }

    /**
     * Test withNewLine() method
     *
     */
    #[@test]
    public function withNewLine() {
      $w= $this->newWriter()->withNewLine("\r");
      $this->assertEquals("\r", $w->getNewLine());
    }

    /**
     * Test writing a line using the Windows line separator, "\r\n"
     *
     */
    #[@test]
    public function writeLineWindows() {
      $this->newWriter()->withNewLine("\r\n")->writeLine();
      $this->assertEquals("\r\n", $this->out->getBytes());
    }

    /**
     * Test writing a line using the Un*x line separator, "\n"
     *
     */
    #[@test]
    public function writeLineUnix() {
      $this->newWriter()->withNewLine("\n")->writeLine();
      $this->assertEquals("\n", $this->out->getBytes());
    }

    /**
     * Test writing a line using the Mac line separator, "\r"
     *
     */
    #[@test]
    public function writeLineMac() {
      $this->newWriter()->withNewLine("\r")->writeLine();
      $this->assertEquals("\r", $this->out->getBytes());
    }

    /**
     * Test text written is encoded in character set
     *
     */
    #[@test]
    public function writeUtf8() {
      $this->newWriter('utf-8')->write('Übercoder');
      $this->assertEquals('Ãœbercoder', $this->out->getBytes());
    }

    /**
     * Test text written is encoded in character set
     *
     */
    #[@test]
    public function writeLineUtf8() {
      $this->newWriter('utf-8')->writeLine('Übercoder');
      $this->assertEquals("Ãœbercoder\n", $this->out->getBytes());
    }
  }
?>
