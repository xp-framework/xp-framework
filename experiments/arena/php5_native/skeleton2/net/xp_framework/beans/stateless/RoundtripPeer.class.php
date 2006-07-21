<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.beans.stateless.RoundtripBean');

  /**
   * Home interface for xp/demo/Roundtrip
   *
   * @purpose  EASC Client stub
   */
  class RoundtripPeer extends Object implements RoundtripHome {

    /**
     * Create method
     *
     * @access  public
     * @return  net.xp_framework.beans.stateless.RoundtripBean
     */
    public function create() {
      return new RoundtripBean();
    }
  } 
?>
