<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: ConsoleOutputStream.class.php 8963 2006-12-27 14:21:05Z friebe $
 */

  uses('io.streams.ConsoleOutputStream');

  /**
   * OuputStream that decodes the UTF-8 strings we use internally
   * into the specified encoding.
   *
   * @ext      iconv
   * @purpose  OuputStream implementation
   */
  class DecodingOutputStream extends Object implements OutputStream {
    protected
      $enclosed = NULL,
      $charset  = NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream enclodes
     * @param   string charset the charset to convert to
     */
    public function __construct(OutputStream $enclosed, $charset) {
      $this->enclosed= $enclosed;
      $this->charset= $charset;
    }
    
    /**
     * Creates a string representation of this output stream
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'[decode->'.$this->charset.']->*'.$this->enclosed->toString();
    }

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) {
      $this->enclosed->write(iconv('UTF-8', $this->charset, $arg));
    }

    /**
     * Flush this stream.
     *
     */
    public function flush() { 
      $this->enclosed->flush();
    }

    /**
     * Close this stream.
     *
     */
    public function close() {
      $this->enclosed->close();
    }
  }
?>
