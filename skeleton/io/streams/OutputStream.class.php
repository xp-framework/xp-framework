<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * An OuputStream can be written to
   *
   * @purpose  Interface
   */
  interface OutputStream {

    /**
     * Write a string
     *
     * @param   var arg
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
