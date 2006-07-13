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
  class EascReader extends Object {
  
    function readBytes(&$sock, $num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    function readFrom(&$sock) {
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
        $this->readBytes($sock, 12)
      );

      if (0x3c872747 != $header['magic']) {
        ApplicationServerListener::answer($sock, 0x0007 /* REMOTE_MSG_ERROR */, 'Magic number mismatch');
        return NULL;
      }

      return array($header['type'], $this->readBytes($sock, $header['length']));
    }
  }
?>
