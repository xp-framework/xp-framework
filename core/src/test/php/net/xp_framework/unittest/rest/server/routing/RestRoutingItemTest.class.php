<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.routing.RestRoutingItem',
    'webservices.rest.server.routing.RestRoutingArgs'
  );
  
  /**
   * Test RestRoutingItem class
   *
   */
  class RestRoutingItemTest extends TestCase {
    protected $fixture= NULL;
    protected $fixtureParams= NULL;
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $this->fixture= new RestRoutingItem(
        'GET',
        new RestPath('/some/path'),
        new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)),
        new RestRoutingArgs(array('id'), array('some.Class'))
      );
      $this->fixtureParams= new RestRoutingItem(
        'GET',
        new RestPath('/some/path/{arg}'),
        new RestMethodRoute($this->getClass()->getMethod(__FUNCTION__)),
        new RestRoutingArgs(array('id'), array('some.Class'))
      );
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertEquals('GET', $this->fixture->getVerb());
      $this->assertEquals('/some/path', $this->fixture->getPath()->getPath());
      $this->assertEquals($this->getClass()->getMethod('setUp'), $this->fixture->getTarget()->getMethod());
      $this->assertEquals(array('id'), $this->fixture->getArgs()->getArguments());
      $this->assertEquals(array('some.Class'), $this->fixture->getArgs()->getInjections());
    }
    
    /**
     * Test appliesTo()
     * 
     */
    #[@test]
    public function appliesTo() {
      $this->assertTrue($this->fixture->appliesTo('GET', '/some/path'));
    }
    
    /**
     * Test appliesTo() with wrong method
     * 
     */
    #[@test]
    public function appliesToWrongMethod() {
      $this->assertFalse($this->fixture->appliesTo('PUT', '/some/path'));
    }
    
    /**
     * Test appliesTo() with wrong path
     * 
     */
    #[@test]
    public function appliesToWrongPath() {
      $this->assertFalse($this->fixture->appliesTo('GET', '/wrong/path'));
    }
    
    /**
     * Test appliesTo() with lowercase method
     * 
     */
    #[@test]
    public function appliesToLowercaseMethod() {
      $this->assertFalse($this->fixture->appliesTo('get', '/some/path'));
    }
    
    /**
     * Test appliesTo() with parameter
     *
     */
    #[@test]
    public function appliesToParameterPath() {
      $this->assertTrue($this->fixtureParams->appliesTo('GET', '/some/path/123'));
    }
    
    /**
     * Test appliesTo() with parameter including encoded characters
     *
     */
    #[@test]
    public function appliesToEncodedParameterPath() {
      $this->assertTrue($this->fixtureParams->appliesTo('GET', '/some/path/123%20456'));
    }
  }
?>
