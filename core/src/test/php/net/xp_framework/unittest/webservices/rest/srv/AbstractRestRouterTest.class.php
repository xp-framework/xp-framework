<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.AbstractRestRouter',
    'webservices.rest.srv.RestContext',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
   */
  class AbstractRestRouterTest extends TestCase {
    protected $fixture= NULL;
    protected $target= NULL;
    protected $handler= NULL;

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
      $route= new RestRoute('GET', '/hello', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals($route, $this->fixture->addRoute($route));
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_a_route() {
      $route= new RestRoute('GET', '/hello', $this->handler, $this->target, NULL, NULL);
      $this->fixture->addRoute($route);
      $this->assertEquals(array($route), $this->fixture->allRoutes());
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_two_routes() {
      $route1= new RestRoute('GET', '/hello', $this->handler, $this->target, NULL, NULL);
      $route2= new RestRoute('GET', '/world', $this->handler, $this->target, NULL, NULL);
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
      $route1= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->handler, $this->target, NULL, NULL);
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
        $this->fixture->targetsFor('GET', '/resource', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function get_route_returned() {
      $route1= new RestRoute('GET', '/resource/{id}', $this->handler, $this->target, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'handler'  => $this->handler,
          'target'   => $route1->getTarget(),
          'params'   => array(),
          'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->targetsFor('GET', '/resource/1', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function post_route_returned() {
      $route1= new RestRoute('GET', '/resource/{id}', $this->handler, NULL, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'handler'  => $this->handler,
          'target'   => $this->target,
          'params'   => array(),
          'segments' => array(0 => '/resource'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->targetsFor('POST', '/resource', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function route_with_custom_mimetype_preferred_according_to_accept() {
      $route1= new RestRoute('GET', '/resource/{id}', $this->handler, NULL, NULL, NULL);
      $route2= new RestRoute('GET', '/resource/{id}', $this->handler, $this->target, NULL, array('application/vnd.example.v2+json'));
      $this->fixture->addRoute($route1); 
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(
          array(
            'handler'  => $this->handler,
            'target'   => $this->target,
            'params'   => array(),
            'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
            'input'    => NULL,
            'output'   => 'application/vnd.example.v2+json'
          ),
          array(
            'handler'  => $this->handler,
            'target'   => NULL,
            'params'   => array(),
            'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
            'input'    => NULL,
            'output'   => 'text/json'
          )
        ), 
        $this->fixture->targetsFor('GET', '/resource/1', NULL, new Preference('application/vnd.example.v2+json, text/json'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function route_with_custom_mimetype_preferred_according_to_type() {
      $route1= new RestRoute('POST', '/resource', $this->handler, NULL, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->handler, $this->target, array('application/vnd.example.v2+json'), NULL);
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
            'target'   => NULL,
            'params'   => array(),
            'segments' => array(0 => '/resource'),
            'input'    => 'application/vnd.example.v2+json',
            'output'   => 'text/json'
          )
        ), 
        $this->fixture->targetsFor('POST', '/resource', 'application/vnd.example.v2+json', new Preference('*/*'))
      );
    }
  }
?>
