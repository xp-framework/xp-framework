<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.RestDefaultRouter',
    'io.streams.MemoryInputStream',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
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
      self::$package= Package::forName('net.xp_framework.unittest.webservices.rest.srv.fixture');
    }

    /**
     * Sets up router fixture
     *
     */
    public function setUp() {
      $this->fixture= new RestDefaultRouter();
      $this->fixture->configure(self::$package->getName());
      $this->fixture->setInputFormats(array('*json'));
      $this->fixture->setOutputFormats(array('text/json'));
    }

    /**
     * Returns a class object for a given fixture class
     *
     * @param  string class
     * @return lang.XPClass
     */
    protected function fixtureClass($class) {
      return self::$package->loadClass($class);
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
     * Test targetsFor()
     * 
     */
    #[@test]
    public function greet_default() {
      $this->assertEquals(
        array(array(
          'handler'  => $this->fixtureClass('GreetingHandler'),
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
          'params'   => array(
            'name'     => new RestParamSource('name', ParamReader::$PATH), 
            'greeting' => new RestParamSource('greeting', ParamReader::$PARAM)
          ),
          'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'text/json'
        )),
        $this->fixture->targetsFor('GET', '/greet/test', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function greet_custom() {
      $this->assertEquals(
        array(array(
          'handler'  => $this->fixtureClass('GreetingHandler'),
          'target'   => $this->fixtureMethod('GreetingHandler', 'hello'),
          'params'   => array('name' => new RestParamSource('name', ParamReader::$PATH)),
          'segments' => array(0 => '/hello/test', 'name' => 'test', 1 => 'test'),
          'input'    => NULL,
          'output'   => 'application/vnd.example.v2+json'
        )),
        $this->fixture->targetsFor('GET', '/hello/test', NULL, new Preference('application/vnd.example.v2+json'))
      );
    }

    /**
     * Test targetsFor()
     *
     */
    #[@test]
    public function greet_post() {
      $this->assertEquals(
        array(array(
          'handler'  => $this->fixtureClass('GreetingHandler'),
          'target'   => $this->fixtureMethod('GreetingHandler', 'greet_posted'),
          'params'   => array(),
          'segments' => array(0 => '/greet'),
          'input'    => 'application/json',
          'output'   => 'text/json'
        )),
        $this->fixture->targetsFor('POST', '/greet', 'application/json', new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     *
     */
    #[@test]
    public function hello_post() {
      $this->assertEquals(
        array(
          array(
            'handler'  => $this->fixtureClass('GreetingHandler'),
            'target'   => $this->fixtureMethod('GreetingHandler', 'hello_posted'),
            'params'   => array(),
            'segments' => array(0 => '/greet'),
            'input'    => 'application/vnd.example.v2+json',
            'output'   => 'text/json'
          ),
          array(
            'handler'  => $this->fixtureClass('GreetingHandler'),
            'target'   => $this->fixtureMethod('GreetingHandler', 'greet_posted'),
            'params'   => array(),
            'segments' => array(0 => '/greet'),
            'input'    => 'application/vnd.example.v2+json',    // because it accepts "*/*"
            'output'   => 'text/json'
          )
        ),
        $this->fixture->targetsFor('POST', '/greet', 'application/vnd.example.v2+json', new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     *
     */
    #[@test]
    public function no_say_route() {
      $this->assertEquals(
        array(),
        $this->fixture->targetsFor('GET', '/say', NULL, new Preference(''))
      );
    }

    /**
     * Test targetsFor()
     *
     */
    #[@test]
    public function no_slash_route() {
      $this->assertEquals(
        array(),
        $this->fixture->targetsFor('GET', '/', NULL, new Preference(''))
      );
    }

    /**
     * Test targetsFor()
     *
     */
    #[@test]
    public function implicit_path() {
      $this->assertEquals(
        array(array(
          'handler'  => $this->fixtureClass('ImplicitGreetingHandler'),
          'target'   => $this->fixtureMethod('ImplicitGreetingHandler', 'hello_world'),
          'params'   => array(),
          'segments' => array(0 => '/implicit'),
          'input'    => NULL,
          'output'   => 'text/json'
        )),
        $this->fixture->targetsFor('GET', '/implicit', NULL, new Preference('*/*'))
      );
    }
  }
?>
