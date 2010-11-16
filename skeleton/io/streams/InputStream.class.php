<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('lang.types.Bytes', 'lang.Closeable');

  /**
   * An InputStream can be read from
   *
   */
  interface InputStream extends Closeable {

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
  }
?>
