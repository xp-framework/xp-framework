<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.server.routing.RestDefaultRouter',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.server.routing.RestDefaultRouter
   */
  class RestDefaultRouterTest extends TestCase {
    protected $fixture= NULL;
    protected static $package= NULL;

    /**
     * Sets up fixture package
     *
     */
    #[@beforeClass]
    public static function fixturePackage() {
      self::$package= Package::forName('net.xp_framework.unittest.rest.fixture');
    }

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new RestDefaultRouter();
      $this->fixture->configure('net.xp_framework.unittest.rest.fixture');
    }

    /**
     * Returns a method from given fixture class
     *
     * @param  string class
     * @param  string method
     * @return lang.reflect.Method
     */
    protected function fixtureMethod($class, $method) {
      return self::$package->loadClass($class)->getMethod($method);
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function greet_default() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'application/json'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function greet_accepting_application_xml() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'application/xml');

      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'application/xml'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function greet_accepting_application_json() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'application/json');

      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'application/json'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function greet_favor_xml() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'application/json;q=0.9, text/xml');

      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'text/xml'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     *
     */
    #[@test]
    public function greet_favor_text_any() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'application/json;q=0.9, text/*');

      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'text/json'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     *
     */
    #[@test]
    public function greet_favor_text_any_over_html() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'text/html;q=0.3, application/json;q=0.9, text/*');

      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'text/json'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     *
     */
    #[@test]
    public function greet_favor_application_json_over_text_any() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'text/*;q=0.3, application/json;q=0.9');

      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'application/json'
        )),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function greet_accepting_dos_program() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet/test'));
      $request->addHeader('Accept', 'application/x-msdos-program');

      $this->assertEquals(
        array(),
        $this->fixture->routesFor($request, new HttpScriptletResponse())
      );
    }
  }
?>
