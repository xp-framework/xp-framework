<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('lang.types.Bytes');

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
     * @return  lang.types.Bytes
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
