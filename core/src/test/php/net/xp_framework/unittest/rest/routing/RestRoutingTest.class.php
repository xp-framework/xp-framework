<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.routing.RestRouting'
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
      $this->assertInstanceOf('webservices.rest.routing.RestRouting', $this->routing);
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
      $this->routing->addRoute('GET', '/path/to/something', new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)), new RestRoutingArgs(array('id'), array('some.Class')));
      
      $routing= current($this->routing->getRoutings('GET', '/path/to/something'));
      $this->assertInstanceOf('webservices.rest.routing.RestRoutingItem', $routing);
      $this->assertEquals('GET', $routing->getMethod());
      $this->assertEquals('/path/to/something', $routing->getPath()->getPath());
      $this->assertInstanceOf('webservices.rest.routing.RestMethodRoute', $routing->getTarget());
      $this->assertInstanceOf('webservices.rest.routing.RestRoutingArgs', $routing->getArgs());
    }
  }
?>
