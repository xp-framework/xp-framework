<?php namespace net\xp_framework\unittest\webservices\rest\srv;

use unittest\TestCase;
use webservices\rest\srv\RestDefaultRouter;
use io\streams\MemoryInputStream;
use scriptlet\HttpScriptletRequest;
use scriptlet\HttpScriptletResponse;


/**
 * Test default router
 *
 * @see  xp://webservices.rest.srv.RestDefaultRouter
 */
class RestDefaultRouterTest extends TestCase {
  protected $fixture= null;
  protected static $package= null;

  /**
   * Sets up fixture package
   *
   */
  #[@beforeClass]
  public static function fixturePackage() {
    self::$package= \lang\reflect\Package::forName('net.xp_framework.unittest.webservices.rest.srv.fixture');
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
          'name'     => new \webservices\rest\srv\RestParamSource('name', \webservices\rest\srv\ParamReader::$PATH), 
          'greeting' => new \webservices\rest\srv\RestParamSource('greeting', \webservices\rest\srv\ParamReader::$PARAM)
        ),
        'segments' => array(0 => '/greet/test', 'name' => 'test', 1 => 'test'),
        'input'    => null,
        'output'   => 'text/json'
      )),
      $this->fixture->targetsFor('GET', '/greet/test', null, new \scriptlet\Preference('*/*'))
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
        'params'   => array('name' => new \webservices\rest\srv\RestParamSource('name', \webservices\rest\srv\ParamReader::$PATH)),
        'segments' => array(0 => '/hello/test', 'name' => 'test', 1 => 'test'),
        'input'    => null,
        'output'   => 'application/vnd.example.v2+json'
      )),
      $this->fixture->targetsFor('GET', '/hello/test', null, new \scriptlet\Preference('application/vnd.example.v2+json'))
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
      $this->fixture->targetsFor('POST', '/greet', 'application/json', new \scriptlet\Preference('*/*'))
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
      $this->fixture->targetsFor('POST', '/greet', 'application/vnd.example.v2+json', new \scriptlet\Preference('*/*'))
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
      $this->fixture->targetsFor('GET', '/say', null, new \scriptlet\Preference(''))
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
      $this->fixture->targetsFor('GET', '/', null, new \scriptlet\Preference(''))
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
        'input'    => null,
        'output'   => 'text/json'
      )),
      $this->fixture->targetsFor('GET', '/implicit', null, new \scriptlet\Preference('*/*'))
    );
  }
}
