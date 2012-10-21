<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.server.routing.AbstractRestRouter',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.server.routing.RestDefaultRouter
   */
  class AbstractRestRouterTest extends TestCase {
    protected $fixture= NULL;
    protected $target= NULL;

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new AbstractRestRouter();
      $this->target= $this->getClass()->getMethod('target');
    }

    /**
     * Target method
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
      $route= new RestRoute('GET', '/hello', $this->target, NULL, NULL);
      $this->assertEquals($route, $this->fixture->addRoute($route));
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_a_route() {
      $route= new RestRoute('GET', '/hello', $this->target, NULL, NULL);
      $this->fixture->addRoute($route);
      $this->assertEquals(array($route), $this->fixture->allRoutes());
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_two_routes() {
      $route1= new RestRoute('GET', '/hello', $this->target, NULL, NULL);
      $route2= new RestRoute('GET', '/world', $this->target, NULL, NULL);
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
      $route1= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, NULL, NULL);
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
        $this->fixture->targetsFor('GET', '/resource', NULL, new Preference('*/*'), array())
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function get_route_returned() {
      $route1= new RestRoute('GET', '/resource/{id}', $this->target, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'target'   => $route1->getTarget(),
          'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->targetsFor('GET', '/resource/1', NULL, new Preference('*/*'), array('text/json'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function post_route_returned() {
      $route1= new RestRoute('GET', '/resource/{id}', NULL, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'target'   => $this->target,
          'segments' => array(0 => '/resource'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->targetsFor('POST', '/resource', NULL, new Preference('*/*'), array('text/json'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function route_with_custom_mimetype_preferred_according_to_accept() {
      $route1= new RestRoute('GET', '/resource/{id}', NULL, NULL, NULL);
      $route2= new RestRoute('GET', '/resource/{id}', $this->target, NULL, array('application/vnd.example.v2+json'));
      $this->fixture->addRoute($route1); 
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(
          array(
            'target'   => $this->target,
            'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
            'input'    => NULL,
            'output'   => 'application/vnd.example.v2+json'
          ),
          array(
            'target'   => NULL,
            'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
            'input'    => NULL,
            'output'   => 'text/json'
          )
        ), 
        $this->fixture->targetsFor('GET', '/resource/1', NULL, new Preference('application/vnd.example.v2+json, text/json'), array('text/json'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function route_with_custom_mimetype_preferred_according_to_type() {
      $route1= new RestRoute('POST', '/resource', NULL, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, array('application/vnd.example.v2+json'), NULL);
      $this->fixture->addRoute($route1); 
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(
          array(
            'target'   => $this->target,
            'segments' => array(0 => '/resource'),
            'input'    => 'application/vnd.example.v2+json',
            'output'   => 'text/json'
          ),
          array(
            'target'   => NULL,
            'segments' => array(0 => '/resource'),
            'input'    => 'application/vnd.example.v2+json',
            'output'   => 'text/json'
          )
        ), 
        $this->fixture->targetsFor('POST', '/resource', 'application/vnd.example.v2+json', new Preference('*/*'), array('text/json'))
      );
    }

  }
?>
