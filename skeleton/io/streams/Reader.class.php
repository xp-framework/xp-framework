<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.InputStream');

  /**
   * Servers as an bstract base class for all other readers. A reader 
   * returns characters it reads from the underlying InputStream 
   * implementation (which works with bytes - for single-byte character
   * sets, there is no difference, obviously).
   *
   */
  abstract class Reader extends Object {
    protected $stream= NULL;
    
    /**
     * Constructor. Creates a new Reader from an InputStream.
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      $this->stream= $stream;
    }
    
    /**
     * Returns the underlying stream
     *
     * @return  io.streams.InputStream stream
     */
    public function getStream() {
      return $this->stream;
    }

    /**
     * Closes this reader (and the underlying stream)
     *
     */
    public function close() {
      $this->stream->close();
    }
  }
?>
