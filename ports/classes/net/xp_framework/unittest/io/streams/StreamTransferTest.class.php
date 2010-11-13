<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.StreamTransfer',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.StreamTransfer
   */
  class StreamTransferTest extends TestCase {

    /**
     * Returns an uncloseable input stream
     *
     * @return  io.streams.InputStream
     */
    protected function uncloseableInputStream() {
      return newinstance('io.streams.InputStream', array(), '{
        public function read($length= 8192) { }
        public function available() { }
        public function close() { throw new IOException("Close error"); }
      }');
    }

    /**
     * Returns a closeable input stream
     *
     * @return  io.streams.InputStream
     */
    protected function closeableInputStream() {
      return newinstance('io.streams.InputStream', array(), '{
        public $closed= FALSE;
        public function read($length= 8192) { }
        public function available() { }
        public function close() { $this->closed= TRUE; }
      }');
    }
    
    /**
     * Returns an uncloseable output stream
     *
     * @return  io.streams.OutputStream
     */
    protected function uncloseableOutputStream() {
      return newinstance('io.streams.OutputStream', array(), '{
        public function write($data) { }
        public function flush() { }
        public function close() { throw new IOException("Close error"); }
      }');
    }

    /**
     * Returns a closeable output stream
     *
     * @return  io.streams.OutputStream
     */
    protected function closeableOutputStream() {
      return newinstance('io.streams.OutputStream', array(), '{
        public $closed= FALSE;
        public function write($data) { }
        public function flush() { }
        public function close() { $this->closed= TRUE; }
      }');
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function dataTransferred() {
      $out= new MemoryOutputStream();

      $s= new StreamTransfer(new MemoryInputStream('Hello'), $out);
      $s->transferAll();

      $this->assertEquals('Hello', $out->getBytes());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nothingAvailableAfterTransfer() {
      $in= new MemoryInputStream('Hello');

      $s= new StreamTransfer($in, new MemoryOutputStream());
      $s->transferAll();

      $this->assertEquals(0, $in->available());
    }

    /**
     * Test closing a stream twice has no effect.
     *
     * @see   xp://lang.Closeable#close
     */
    #[@test]
    public function closingTwice() {
      $s= new StreamTransfer(new MemoryInputStream('Hello'), new MemoryOutputStream());
      $s->close();
      $s->close();
    }

    /**
     * Test close() method
     *
     */
    #[@test]
    public function close() {
      $in= $this->closeableInputStream();
      $out= $this->closeableOutputStream();
      create(new StreamTransfer($in, $out))->close();
      $this->assertTrue($in->closed, 'input closed');
      $this->assertTrue($out->closed, 'output closed');
    }

    /**
     * Test close() and exceptions
     *
     */
    #[@test]
    public function closingOutputFails() {
      $in= $this->closeableInputStream();
      $out= $this->uncloseableOutputStream();
      
      try {
        create(new StreamTransfer($in, $out))->close();
        $this->fail('Expected exception not caught', NULL, 'io.IOException');
      } catch (IOException $expected) {
        $this->assertEquals('Could not close output stream: Close error', $expected->getMessage());
      }
      
      $this->assertTrue($in->closed, 'input closed');
    }

    /**
     * Test close() and exceptions
     *
     */
    #[@test]
    public function closingInputFails() {
      $in= $this->uncloseableInputStream();
      $out= $this->closeableOutputStream();
      
      try {
        create(new StreamTransfer($in, $out))->close();
        $this->fail('Expected exception not caught', NULL, 'io.IOException');
      } catch (IOException $expected) {
        $this->assertEquals('Could not close input stream: Close error', $expected->getMessage());
      }

      $this->assertTrue($out->closed, 'output closed');
    }

    /**
     * Test close() and exceptions
     *
     */
    #[@test]
    public function closingInputAndOutputFails() {
      $in= $this->uncloseableInputStream();
      $out= $this->uncloseableOutputStream();
      
      try {
        create(new StreamTransfer($in, $out))->close();
        $this->fail('Expected exception not caught', NULL, 'io.IOException');
      } catch (IOException $expected) {
        $this->assertEquals('Could not close input stream: Close error, Could not close output stream: Close error', $expected->getMessage());
      }
    }
  }
?>
