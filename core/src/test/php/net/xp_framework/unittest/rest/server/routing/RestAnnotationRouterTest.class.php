<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.server.routing.RestAnnotationRouter'
  );
  
  /**
   * Test annotation based router
   *
   */
  class RestAnnotationRouterTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new RestAnnotationRouter();
      $this->fixture->configure('net.xp_framework.unittest.rest.server.mock');
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.server.routing.RestRouter', $this->fixture);
    }
    
    /**
     * Test configuration
     * 
     */
    #[@test]
    public function configure() {
      $this->fixture->configure('net.xp_framework.unittest.rest.server.mock');
    }
    
    /**
     * Test getRouting()
     * 
     */
    #[@test]
    public function getRouting() {
      $this->assertInstanceOf('webservices.rest.server.routing.RestRouting', $this->fixture->getRouting());
    }
    
    /**
     * Test hasRoutesFor()
     * 
     */
    #[@test]
    public function hasRoutesFor() {
      $this->assertFalse($this->fixture->hasRoutesFor('PUT', '/'));
    }
    
    /**
     * Test hasRoutesFor()
     * 
     */
    #[@test]
    public function hasRoutesForPath() {
      $this->assertTrue($this->fixture->hasRoutesFor('GET', '/some/thing'));
    }
    
    /**
     * Test hasRoutesFor() with case insensitive method
     * 
     */
    #[@test]
    public function hasRoutesForPathCaseInsensitive() {
      $this->assertTrue($this->fixture->hasRoutesFor('get', '/some/thing'));
    }
    
    /**
     * Test routesFor() with no target found
     * 
     */
    #[@test]
    public function routesForWithNoTarget() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/'));
      
      $this->assertEquals(array(), $this->fixture->routesFor(
        new JsonHttpRequestAdapter($request),
        new JsonHttpResponseAdapter(new HttpScriptletResponse())
      ));
    }
    
    /**
     * Test routesFor() with target found
     * 
     */
    #[@test]
    public function routesForWithTarget() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/some/thing'));
      
      $routes= $this->fixture->routesFor(
        new JsonHttpRequestAdapter($request),
        new JsonHttpResponseAdapter(new HttpScriptletResponse())
      );
      
      $this->assertEquals(1, sizeof($routes));
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingItem', $routes[0]);
    }
    
    /**
     * Test routesFor() with target using parameters
     * 
     */
    #[@test]
    public function routesForWithTargetAndParameters() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/some/thing/123'));
      
      $routes= $this->fixture->routesFor(
        new JsonHttpRequestAdapter($request),
        new JsonHttpResponseAdapter(new HttpScriptletResponse())
      );
      
      $this->assertEquals(1, sizeof($routes));
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingItem', $routes[0]);
      $this->assertEquals(array('id'), $routes[0]->getArgs()->getArguments());
    }
    
    /**
     * Test routesFor() with target using injection
     * 
     */
    #[@test]
    public function routesForWithTargetAndInjection() {
      $request= new HttpScriptletRequest();
      $request->setURL(new HttpScriptletURL('http://localhost/some/injected/thing/123'));
      
      $routes= $this->fixture->routesFor(
        $reqAdapter= new JsonHttpRequestAdapter($request),
        $resAdapter= new JsonHttpResponseAdapter(new HttpScriptletResponse())
      );
      
      $this->assertEquals(1, sizeof($routes));
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingItem', $routes[0]);
      $this->assertEquals(array('request', 'response', 'id'), $routes[0]->getArgs()->getArguments());
      $this->assertEquals(
        array('webservices.rest.server.transport.HttpRequestAdapter', 'webservices.rest.server.transport.HttpResponseAdapter'),
        $routes[0]->getArgs()->getInjections()
      );
    }
  }
?>
