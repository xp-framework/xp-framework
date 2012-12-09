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
  #[@webservice]
  class GreetingHandler extends Object {
    protected $fixture= NULL;

    /**
     * Greet someone
     * 
     * @param   string name
     * @param   string greeting
     * @return  string
     */
    #[@webmethod(verb= 'GET', path= '/greet/{name}'), @$name: path, @$greeting: param]
    public function greet($name, $greeting= 'Hello') {
      return $greeting.' '.$name;
    }

    /**
     * Say "hello" someone
     * 
     * @param   string name
     * @return  net.xp_framework.unittest.rest.fixture.Greeting
     */
    #[@webmethod(verb= 'GET', path= '/hello/{name}', returns= 'application/vnd.example.v2+json'), @$name: path]
    public function hello($name) {
      return new Greeting('Hello', $name);
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
     * Say "hello" to someone
     * 
     * @param   net.xp_framework.unittest.rest.fixture.Greeting
     * @return  bool
     */
    #[@webmethod(verb= 'POST', path= '/greet', accepts= 'application/vnd.example.v2+json')]
    public function hello_posted(Greeting $payload) {
      return $this->greet($payload->name, $payload->word);
    }

    /**
     * Greet someone
     * 
     * @param   string name
     * @param   scriptlet.Preference language
     * @return  string
     */
    #[@webmethod(verb= 'GET', path= '/intl/greet/{name}'), @$name: path, @$language: header('Accept-Language')]
    public function greet_intl($name, Preference $language) {
      // TBI
    }

    /**
     * Greet logged in user
     * 
     * @param   string name
     * @return  string
     */
    #[@webmethod(verb= 'GET', path= '/user/greet'), @$name: cookie('user')]
    public function greet_user($name) {
      return 'Hello '.$name;
    }
  }
?>
