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

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new AbstractRestRouter();
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
      $route= new RestRoute('GET', '/hello', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $this->assertEquals($route, $this->fixture->addRoute($route));
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_a_route() {
      $route= new RestRoute('GET', '/hello', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $this->fixture->addRoute($route);
      $this->assertEquals(array($route), $this->fixture->allRoutes());
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_two_routes() {
      $route1= new RestRoute('GET', '/hello', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $route2= new RestRoute('GET', '/world', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
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
      $route1= new RestRoute('GET', '/resource', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(array($route1, $route2), $this->fixture->allRoutes());
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function routes_for_empty_fixture() {
      $this->assertEquals(
        array(), 
        $this->fixture->routesFor('GET', '/resource', NULL, new Preference('*/*'), array())
      );
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function get_route_returned() {
      $route1= new RestRoute('GET', '#^/resource/(?P<id>[%\w:\+\-\.]*)$#', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $route2= new RestRoute('POST', '#^/resource$#', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'target'   => $route1->getTarget(),
          'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->routesFor('GET', '/resource/1', NULL, new Preference('*/*'), array('text/json'))
      );
    }

    /**
     * Test routesFor()
     * 
     */
    #[@test]
    public function post_route_returned() {
      $route1= new RestRoute('GET', '#^/resource/(?P<id>[%\w:\+\-\.]*)$#', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $route2= new RestRoute('POST', '#^/resource$#', $this->getClass()->getMethod(__FUNCTION__), NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'target'   => $route2->getTarget(),
          'segments' => array(0 => '/resource'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->routesFor('POST', '/resource', NULL, new Preference('*/*'), array('text/json'))
      );
    }
  }
?>
