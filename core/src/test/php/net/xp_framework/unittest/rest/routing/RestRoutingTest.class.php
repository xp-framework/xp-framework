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
     * Test hasRouting() with not routes
     * 
     */
    #[@test]
    public function hasNoRouting() {
      $this->assertFalse($this->routing->hasRouting('GET', '/'));
    }
    
    /**
     * Test hasRouting()
     * 
     */
    #[@test]
    public function hasRouting() {
      $this->routing->addRoute('GET', '/path/to/something', new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)));
      
      $this->assertTrue($this->routing->hasRouting('GET', '/path/to/something'));
    }
    
    /**
     * Test getRouting() with no routes
     * 
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function getNoRouting() {
      $this->routing->getRouting('GET', '/');
    }
    
    /**
     * Test getRouting()
     * 
     */
    #[@test]
    public function getRouting() {
      $this->routing->addRoute('GET', '/path/to/something', new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)), new RestRoutingArgs(array('id'), array('some.Class')));
      
      $routing= $this->routing->getRouting('GET', '/path/to/something');
      $this->assertInstanceOf('webservices.rest.routing.RestRoutingItem', $routing);
      $this->assertEquals('GET', $routing->getMethod());
      $this->assertEquals('/path/to/something', $routing->getPath()->getPath());
      $this->assertInstanceOf('webservices.rest.routing.RestMethodRoute', $routing->getTarget());
      $this->assertInstanceOf('webservices.rest.routing.RestRoutingArgs', $routing->getArgs());
    }
  }
?>
