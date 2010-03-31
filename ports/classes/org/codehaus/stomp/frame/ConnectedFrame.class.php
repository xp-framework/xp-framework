<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('org.codehaus.stomp.frame.Frame');

  $package= 'org.codehaus.stomp.frame';
  class org·codehaus·stomp·frame·ConnectedFrame extends org·codehaus·stomp·frame·Frame {

    public function command() {
      return 'CONNECTED';
    }
  }
?>
