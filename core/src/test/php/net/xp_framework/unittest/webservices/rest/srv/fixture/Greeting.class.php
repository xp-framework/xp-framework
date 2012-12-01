<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Fixture for default router
   *
   * @see  xp://net.xp_framework.unittest.webservices.rest.srv.RestDefaultRouterTest
   */
  class Greeting extends Object {
    public $word;
    public $name;

    /**
     * Greet someone
     * 
     * @param   string name
     * @param   string greeting
     */
    public function __construct($greeting, $name) {
      $this->greeting= $greeting;
      $this->name= $name;
    }
  }
?>
