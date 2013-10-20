<?php namespace net\xp_framework\unittest\webservices\rest\srv;

use unittest\TestCase;
use webservices\rest\srv\AbstractRestRouter;
use webservices\rest\srv\RestContext;
use scriptlet\HttpScriptletRequest;
use scriptlet\HttpScriptletResponse;


/**
 * Test default router
 *
 * @see  xp://webservices.rest.srv.RestDefaultRouter
 */
class AbstractRestRouterTest extends TestCase {
  protected $fixture= null;
  protected $target= null;
  protected $handler= null;

  /**
   * Setup
   * 
   */
  public function setUp() {
    $this->fixture= new AbstractRestRouter();
    $this->fixture->setInputFormats(array('*json'));
    $this->fixture->setOutputFormats(array('text/json'));
    $this->handler= $this->getClass();
    $this->target= $this->handler->getMethod('target');
  }

  /**
   * Target method
   *
   */
  #[@webservice]
  public function target() {
    // Intentionally empty
  }

  /**
   * Test allRoutes()
   * 
   */
  #[@test]
  public function routes_initially_empty() {
    $this->assertEquals(array(), $this->fixture->allRoutes());
  }

  /**
   * Test addRoute()
   * 
   */
  #[@test]
  public function add_route_returns_added_route() {
    $route= new \webservices\rest\srv\RestRoute('GET', '/hello', $this->handler, $this->target, null, null);
    $this->assertEquals($route, $this->fixture->addRoute($route));
  }

  /**
   * Test addRoute() and allRoutes()
   * 
   */
  #[@test]
  public function add_a_route() {
    $route= new \webservices\rest\srv\RestRoute('GET', '/hello', $this->handler, $this->target, null, null);
    $this->fixture->addRoute($route);
    $this->assertEquals(array($route), $this->fixture->allRoutes());
  }

  /**
   * Test addRoute() and allRoutes()
   * 
   */
  #[@test]
  public function add_two_routes() {
    $route1= new \webservices\rest\srv\RestRoute('GET', '/hello', $this->handler, $this->target, null, null);
    $route2= new \webservices\rest\srv\RestRoute('GET', '/world', $this->handler, $this->target, null, null);
    $this->fixture->addRoute($route1);
    $this->fixture->addRoute($route2);
    $this->assertEquals(array($route1, $route2), $this->fixture->allRoutes());
  }

  /**
   * Test addRoute() and allRoutes()
   * 
   */
  #[@test]
  public function a_post_and_a_get_route() {
    $route1= new \webservices\rest\srv\RestRoute('GET', '/resource', $this->handler, $this->target, null, null);
    $route2= new \webservices\rest\srv\RestRoute('POST', '/resource', $this->handler, $this->target, null, null);
    $this->fixture->addRoute($route1);
    $this->fixture->addRoute($route2);
    $this->assertEquals(array($route1, $route2), $this->fixture->allRoutes());
  }

  /**
   * Test targetsFor()
   * 
   */
  #[@test]
  public function routes_for_empty_fixture() {
    $this->assertEquals(
      array(), 
      $this->fixture->targetsFor('GET', '/resource', null, new \scriptlet\Preference('*/*'))
    );
  }

  /**
   * Test targetsFor()
   * 
   */
  #[@test]
  public function get_route_returned() {
    $route1= new \webservices\rest\srv\RestRoute('GET', '/resource/{id}', $this->handler, $this->target, null, null);
    $route2= new \webservices\rest\srv\RestRoute('POST', '/resource', $this->handler, $this->target, null, null);
    $this->fixture->addRoute($route1);
    $this->fixture->addRoute($route2);
    $this->assertEquals(
      array(array(
        'handler'  => $this->handler,
        'target'   => $route1->getTarget(),
        'params'   => array(),
        'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
        'input'    => null,
        'output'   => 'text/json'
      )), 
      $this->fixture->targetsFor('GET', '/resource/1', null, new \scriptlet\Preference('*/*'))
    );
  }

  /**
   * Test targetsFor()
   * 
   */
  #[@test]
  public function post_route_returned() {
    $route1= new \webservices\rest\srv\RestRoute('GET', '/resource/{id}', $this->handler, null, null, null);
    $route2= new \webservices\rest\srv\RestRoute('POST', '/resource', $this->handler, $this->target, null, null);
    $this->fixture->addRoute($route1);
    $this->fixture->addRoute($route2);
    $this->assertEquals(
      array(array(
        'handler'  => $this->handler,
        'target'   => $this->target,
        'params'   => array(),
        'segments' => array(0 => '/resource'),
        'input'    => null,
        'output'   => 'text/json'
      )), 
      $this->fixture->targetsFor('POST', '/resource', null, new \scriptlet\Preference('*/*'))
    );
  }

  /**
   * Test targetsFor()
   * 
   */
  #[@test]
  public function route_with_custom_mimetype_preferred_according_to_accept() {
    $route1= new \webservices\rest\srv\RestRoute('GET', '/resource/{id}', $this->handler, null, null, null);
    $route2= new \webservices\rest\srv\RestRoute('GET', '/resource/{id}', $this->handler, $this->target, null, array('application/vnd.example.v2+json'));
    $this->fixture->addRoute($route1); 
    $this->fixture->addRoute($route2);
    $this->assertEquals(
      array(
        array(
          'handler'  => $this->handler,
          'target'   => $this->target,
          'params'   => array(),
          'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
          'input'    => null,
          'output'   => 'application/vnd.example.v2+json'
        ),
        array(
          'handler'  => $this->handler,
          'target'   => null,
          'params'   => array(),
          'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
          'input'    => null,
          'output'   => 'text/json'
        )
      ), 
      $this->fixture->targetsFor('GET', '/resource/1', null, new \scriptlet\Preference('application/vnd.example.v2+json, text/json'))
    );
  }

  /**
   * Test targetsFor()
   * 
   */
  #[@test]
  public function route_with_custom_mimetype_preferred_according_to_type() {
    $route1= new \webservices\rest\srv\RestRoute('POST', '/resource', $this->handler, null, null, null);
    $route2= new \webservices\rest\srv\RestRoute('POST', '/resource', $this->handler, $this->target, array('application/vnd.example.v2+json'), null);
    $this->fixture->addRoute($route1); 
    $this->fixture->addRoute($route2);
    $this->assertEquals(
      array(
        array(
          'handler'  => $this->handler,
          'target'   => $this->target,
          'params'   => array(),
          'segments' => array(0 => '/resource'),
          'input'    => 'application/vnd.example.v2+json',
          'output'   => 'text/json'
        ),
        array(
          'handler'  => $this->handler,
          'target'   => null,
          'params'   => array(),
          'segments' => array(0 => '/resource'),
          'input'    => 'application/vnd.example.v2+json',
          'output'   => 'text/json'
        )
      ), 
      $this->fixture->targetsFor('POST', '/resource', 'application/vnd.example.v2+json', new \scriptlet\Preference('*/*'))
    );
  }
}
