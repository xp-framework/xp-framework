<?php
/* This file is part of the XP framework
 *
 * $Id: RoundtripHome.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::beans::stateless;

  ::uses('remote.beans.HomeInterface');

  /**
   * Home interface for xp/demo/Roundtrip
   *
   * @purpose  EASC Client stub
   */
  interface RoundtripHome extends remote::beans::HomeInterface {

    /**
     * Create method
     *
     * @return  &net.xp_framework.beans.stateless.Roundtrip
     */
    public function create();
  }
?>
