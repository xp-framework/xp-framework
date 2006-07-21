<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class DefaultReader extends Object {
  
    public function readFrom(&$sock) {
      return $sock->readBinary();
    }
  }
?>
