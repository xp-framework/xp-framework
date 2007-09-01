<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: OutputStream.class.php 8963 2006-12-27 14:21:05Z friebe $
 */

  namespace io::streams;

  /**
   * An OuputStream can be written to
   *
   * @purpose  Interface
   */
  interface OutputStream {

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg);

    /**
     * Flush this buffer
     *
     */
    public function flush();

    /**
     * Close this buffer
     *
     */
    public function close();
  }
?>
