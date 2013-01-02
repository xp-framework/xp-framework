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
  class ImplicitGreetingHandler extends Object {
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

    /**
     * Say "hello" to someone
     * 
     * @param   string payload
     * @return  string
     */
    #[@webmethod(verb= 'POST', path= '/greet')]
    public function greet_posted($payload) {
      sscanf($payload, '%s %s', $greeting, $name);
      return $this->greet($name, $greeting);
    }

    /**
     * Greet the world
     * 
     * @return  string
     */
    #[@webmethod(verb= 'GET')]
    public function hello_world() {
      return $this->greet('World');
    }
  }
?>
