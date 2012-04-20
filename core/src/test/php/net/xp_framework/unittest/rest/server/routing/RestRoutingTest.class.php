<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.routing.RestRouting'
  );
  
  /**
   * Test REST routing table
   *
   * @purpose Test
   */
  class RestRoutingTest extends TestCase {
    protected $routing= NULL;
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $this->routing= new RestRouting();
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.server.routing.RestRouting', $this->routing);
    }
    
    /**
     * Test getItems()
     * 
     */
    #[@test]
    public function getItems() {
      $this->assertEquals(array(), $this->routing->getItems());
    }
    
    /**
     * Test getItems() with routing items
     * 
     */
    #[@test]
    public function getItemsWithItems() {
      $this->routing->addRoute('GET', '/path/to/something', new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)));
      $routes= $this->routing->getItems();
      
      $this->assertEquals(1, sizeof($routes));
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingItem', $routes[0]);
    }
    
    /**
     * Test hasRoutings() with not routes
     * 
     */
    #[@test]
    public function hasNoRouting() {
      $this->assertFalse($this->routing->hasRoutings('GET', '/'));
    }
    
    /**
     * Test hasRoutings()
     * 
     */
    #[@test]
    public function hasRouting() {
      $this->routing->addRoute('GET', '/path/to/something', new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)));
      
      $this->assertTrue($this->routing->hasRoutings('GET', '/path/to/something'));
    }
    
    /**
     * Test getRoutings() with no routes
     * 
     */
    #[@test]
    public function getNoRouting() {
      $this->assertEquals(array(), $this->routing->getRoutings('GET', '/'));
    }
    
    /**
     * Test getRoutings()
     * 
     */
    #[@test]
    public function getRoutings() {
      $this->routing->addRoute(
        'GET', 
        '/path/to/something', 
        new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)), 
        new RestRoutingArgs(array('id'), array('some.Class'))
      );
      
      $routing= current($this->routing->getRoutings('GET', '/path/to/something'));
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingItem', $routing);
      $this->assertEquals('GET', $routing->getVerb());
      $this->assertEquals('/path/to/something', $routing->getPath()->getPath());
      $this->assertInstanceOf('webservices.rest.server.routing.RestMethodRoute', $routing->getTarget());
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingArgs', $routing->getArgs());
    }
  }
?>
