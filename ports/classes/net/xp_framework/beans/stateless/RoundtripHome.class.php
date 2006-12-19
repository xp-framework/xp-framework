<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('remote.beans.HomeInterface');

  /**
   * Home interface for xp/demo/Roundtrip
   *
   * @purpose  EASC Client stub
   */
  interface RoundtripHome extends HomeInterface {

    /**
     * Create method
     *
     * @access  public
     * @return  &net.xp_framework.beans.stateless.Roundtrip
     */
    public function &create();
  }
?>
