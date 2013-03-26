<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.webservices.rest.srv.fixture.Greeting',
    'webservices.rest.srv.Response',
    'webservices.rest.srv.StreamingOutput'
  );

  /**
   * Fixture for default router
   *
   * @see  xp://net.xp_framework.unittest.webservices.rest.srv.RestDefaultRouterTest
   */
  #[@webservice, @xmlfactory(element= 'greeting')]
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
     * Greet someone
     * 
     * @param   string name
     * @return  webservices.rest.srv.Response
     */
    #[@webmethod(verb= 'GET', path= '/greet/and/go/{name}'), @$name: path]
    public function greet_and_go($name) {
      return Response::noContent();
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

    /**
     * Greet handler class
     *
     * @return  string
     */
    #[@webmethod(verb= 'GET', path= '/class/greet')]
    public function greet_class() {
      return 'Hello '.$this->getClassName();
    }

    /**
     * Download a greeting
     *
     * @return  webservices.rest.srv.Output
     */
    #[@webmethod(verb= 'GET', path= '/download')]
    public function download_greeting() {
      $s= StreamingOutput::of(new MemoryInputStream('Hello World'))
        ->withMediaType('text/plain; charset=utf-8')
        ->withContentLength(11)
        ->withStatus(200)
      ;
      $s->buffered= TRUE;       // For easier testability!
      return $s;
    }
  }
?>
