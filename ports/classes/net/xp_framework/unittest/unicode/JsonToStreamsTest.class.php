<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.json.JsonDecoder',
    'io.streams.MemoryOutputStream',
    'io.streams.MemoryInputStream',
    'io.streams.TextReader',
    'io.streams.TextWriter'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.json.JsonDecoder
   */
  class JsonToStreamsTest extends TestCase {
    protected $decoder;
    protected $out;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->decoder= new JsonDecoder();
      $this->out= new MemoryOutputStream();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function writeDecodedToStream() {
      create(new TextWriter($this->out, 'utf-8'))->write($this->decoder->decode('"\u00DCbercoder"'));
      $this->assertEquals("\xC3\x9Cbercoder", $this->out->getBytes());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function writeDecodedToStreamWithBom() {
      create(new TextWriter($this->out))->withBom()->write($this->decoder->decode('"\u00DCbercoder"'));
      $this->assertEquals("\357\273\277\xC3\x9Cbercoder", $this->out->getBytes());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function endodeDataReadFromStream() {
      $reader= new TextReader(new MemoryInputStream("\xC3\x9Cbercoder"), 'utf-8');
      $this->assertEquals("\"\xC3\x9Cbercoder\"", $this->decoder->encode($reader->readLine()));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function endodeDataReadFromStreamWithBom() {
      $reader= new TextReader(new MemoryInputStream("\357\273\277\xC3\x9Cbercoder"));
      $this->assertEquals("\"\xC3\x9Cbercoder\"", $this->decoder->encode($reader->readLine()));
    }
  }
?>
