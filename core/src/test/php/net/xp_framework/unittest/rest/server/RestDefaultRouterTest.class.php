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
      $this->fixture->configure(self::$package->getName());
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
          'output'   => NULL
        )),
        $this->fixture->routesFor($request)
      );
    }

    /**
     * Test routesFor()
     *
     */
    #[@test]
    public function greet_post() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/greet'));
      $request->method= 'POST';
      $request->addHeader('Content-Type', 'application/json');
      $request->setData('"test"');
      $this->assertEquals(
        array(array(
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet_posted'),
          'segments' => array(0 => '/greet'),
          'input'    => NULL,
          'output'   => NULL
        )),
        $this->fixture->routesFor($request)
      );
    }

    /**
     * Test routesFor()
     *
     */
    #[@test]
    public function no_say_route() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/say'));
      $this->assertEquals(
        array(),
        $this->fixture->routesFor($request)
      );
    }

    /**
     * Test routesFor()
     *
     */
    #[@test]
    public function no_slash_route() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/'));
      $this->assertEquals(
        array(),
        $this->fixture->routesFor($request)
      );
    }
  }
?>
