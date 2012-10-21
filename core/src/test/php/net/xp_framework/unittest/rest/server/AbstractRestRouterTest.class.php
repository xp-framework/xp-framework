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
  }
?>
