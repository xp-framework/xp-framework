<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.InputStream', 'io.streams.OutputStream', 'lang.Closeable');

  /**
   * A stream transfer copies from an input stream to an output stream
   *
   * Example (downloading a file):
   * <code>
   *   $t= new StreamTransfer(
   *     create(new HttpConnection('http://example.com'))->get('/')->getInputStream(), 
   *     new FileOutputStream(new File('index.html'))
   *   );
   *   $t->transferAll();
   *   $t->close();
   * </code>
   *
   * @test    xp://net.xp_framework.unittest.io.streams.StreamTransferTest
   */
  class StreamTransfer extends Object implements Closeable {
    protected $in= NULL;
    protected $out= NULL;
    
    /**
     * Creates a new stream transfer
     *
     * @param   io.streams.InputStream in
     * @param   io.streams.OutputStream out
     */
    public function __construct(InputStream $in, OutputStream $out) {
      $this->in= $in;
      $this->out= $out;
    }

    /**
     * Copy all available input from in
     *
     * @return  int number of bytes copied
     * @throws  io.IOException
     */
    public function transferAll() {
      $r= 0;
      while ($this->in->available() > 0) {
        $r+= $this->out->write($this->in->read());
      }
      return $r;
    }

    /**
     * Close input and output streams. Guarantees to try to close both 
     * streams even if one of the close() calls yields an exception.
     *
     * @throws  io.IOException
     */
    public function close() {
      $errors= '';
      try {
        $this->in->close();
      } catch (IOException $e) {
        $errors.= 'Could not close input stream: '.$e->getMessage().', ';
      }
      try {
        $this->out->close();
      } catch (IOException $e) {
        $errors.= 'Could not close output stream: '.$e->getMessage().', ';
      }
      if ($errors) {
        throw new IOException(rtrim($errors, ', '));
      }
    }
  }
?>
