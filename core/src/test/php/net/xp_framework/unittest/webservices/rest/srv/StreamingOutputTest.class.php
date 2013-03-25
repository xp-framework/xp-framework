<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.StreamingOutput',
    'io.streams.MemoryInputStream'
  );
  
  /**
   * Test response class
   *
   * @see  xp://webservices.rest.srv.StreamingOutput
   */
  class StreamingOutputTest extends TestCase {

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function no_input_stream() {
      $this->assertEquals(NULL, create(new StreamingOutput())->inputStream);
    }

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function input_stream_given() {
      $s= new MemoryInputStream('Test');
      $this->assertEquals($s, create(new StreamingOutput($s))->inputStream);
    }

    /**
     * Test of() factory method
     *
     */
    #[@test]
    public function of_with_input_stream() {
      $s= new MemoryInputStream('Test');

      $o= StreamingOutput::of($s);
      $this->assertEquals($s, $o->inputStream, 'inputStream');
      $this->assertEquals('application/octet-stream', $o->mediaType, 'mediaType');
      $this->assertEquals(NULL, $o->contentLength, 'contentLength');
    }

    /**
     * Test of() factory method
     *
     */
    #[@test]
    public function of_with_file() {
      $f= newinstance('io.File', array(new MemoryInputStream('Test')), '{
        protected $stream;
        public function __construct($stream) { $this->stream= $stream; }
        public function getFileName() { return "test.txt"; }
        public function getSize() { return 6100; }
        public function getInputStream() { return $this->stream; }
      }');

      $o= StreamingOutput::of($f);
      $this->assertEquals($f->getInputStream(), $o->inputStream, 'inputStream');
      $this->assertEquals('text/plain', $o->mediaType, 'mediaType');
      $this->assertEquals(6100, $o->contentLength, 'contentLength');
    }

    /**
     * Test of() factory method
     *
     */
    #[@test]
    public function of_with_io_element() {
      $e= newinstance('io.collections.IOElement', array(new MemoryInputStream('Test')), '{
        protected $stream;
        public function __construct($stream) { $this->stream= $stream; }
        public function getURI() { return "/path/to/test.txt"; }
        public function getSize() { return 6100; }
        public function createdAt() { return NULL; }
        public function lastAccessed() { return NULL; }
        public function lastModified() { return NULL; }
        public function getOrigin() { return NULL; }
        public function setOrigin(IOCollection $origin) { }
        public function getInputStream() { return $this->stream; }
        public function getOutputStream() { return NULL; }
      }');

      $o= StreamingOutput::of($e);
      $this->assertEquals($e->getInputStream(), $o->inputStream, 'inputStream');
      $this->assertEquals('text/plain', $o->mediaType, 'mediaType');
      $this->assertEquals(6100, $o->contentLength, 'contentLength');
    }
  }
?>
