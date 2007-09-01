<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: InputStream.class.php 8963 2006-12-27 14:21:05Z friebe $
 */

  namespace io::streams;

  /**
   * An InputStream can be read from
   *
   * @purpose  Interface
   */
  interface InputStream {

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192);

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available();

    /**
     * Close this buffer
     *
     */
    public function close();
  }
?>
