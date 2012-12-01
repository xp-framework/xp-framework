<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.webservices.rest.srv.fixture.Greeting');

  /**
   * Fixture for default router
   *
   * @see  xp://net.xp_framework.unittest.webservices.rest.srv.RestDefaultRouterTest
   */
  #[@webservice(path= '/implicit/')]
  class ImplicitGreetingHandler extends TestCase {
    protected $fixture= NULL;

    /**
     * Greet someone
     * 
     * @param   string name
     * @param   string greeting
     * @return  string
     */
    #[@webmethod(verb= 'GET', path= '/greet/{name}')]
    public function greet($name, $greeting= 'Hello') {
      return $greeting.' '.$name;
    }
  }
?>
