<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.CSVGenerator',
    'io.Stream'
  );

  /**
   * TestCase
   *
   * @see      xp://text.CSVGenerator
   * @purpose  Unittest
   */
  class CsvGeneratorTest extends TestCase {
    protected
      $stream    = NULL,
      $generator = NULL;
      
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->stream= new Stream();
      $this->stream->open(STREAM_MODE_READWRITE);
      $this->generator= new CSVGenerator();
      $this->generator->setOutputStream($this->stream);
      
      // Be explicit here, both values are default
      $this->generator->setColDelimiter('|');
      $this->generator->setLineDelimiter("\n");
    }
    
    /**
     * Tear down test - get
     *
     */
    public function tearDown() {
      delete($this->stream);
      delete($this->generator);
    }
    
    /**
     * Test delimiter char is enclosed in quotes
     *
     */
    #[@test]
    public function separatorInFieldIsQuoted() {
      $this->generator->writeRecord(array('A|B', 'C'));
      $this->stream->seek(0);
      $this->assertEquals('"A|B"|C', $this->stream->readLine());
    }

    /**
     * Test quote char is is escaped
     *
     */
    #[@test]
    public function quoteInFieldIsEscaped() {
      $this->generator->writeRecord(array('A"B', 'C'));
      $this->stream->seek(0);
      $this->assertEquals('"A""B"|C', $this->stream->readLine());
    }

    /**
     * Test header writing
     *
     */
    #[@test]
    public function headerIsWritten() {
      $this->generator->setHeader(array('id', 'name'));
      $this->generator->writeRecord(array('1549', 'Timm'));
      $this->stream->seek(0);
      $this->assertEquals('id|name', $this->stream->readLine());
      $this->assertEquals('1549|Timm', $this->stream->readLine());
    }
  }
?>
