<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Closeable');

  /**
   * The socket handle interface
   */
  interface SocketHandle extends Closeable {

    /**
     * Returns the underlying socket handle
     *
     * @return  var
     */
    public function getHandle();

  }
?>
