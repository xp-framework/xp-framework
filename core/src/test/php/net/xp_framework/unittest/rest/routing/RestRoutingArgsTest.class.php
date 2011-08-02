<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.routing.RestRoutingArgs'
  );
  
  /**
   * Test REST routing args
   *
   * @purpose Test
   */
  class RestRoutingArgsTest extends TestCase {
    protected $args= NULL;
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $this->args= new RestRoutingArgs(
        array('id', 'title', 'name'),
        array('webservices.rest.transport.HttpRequestAdapter', 'webservices.rest.transport.HttpResponseAdapter')
      );
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.routing.RestRoutingArgs', $this->args);
    }
    
    /**
     * Test getArgs()
     * 
     */
    #[@test]
    public function getArgumnets() {
      $this->assertEquals(array('id', 'title', 'name'), $this->args->getArguments());
    }
    
    /**
     * Test addArg()
     * 
     */
    #[@test]
    public function addArgumnets() {
      $this->args->addArgument('another');
      
      $this->assertEquals(array('id', 'title', 'name', 'another'), $this->args->getArguments());
    }
    
    /**
     * Test getInjections()
     * 
     */
    #[@test]
    public function getInjections() {
      $this->assertEquals(
        array('webservices.rest.transport.HttpRequestAdapter', 'webservices.rest.transport.HttpResponseAdapter'),
        $this->args->getInjections()
      );
    }
    
    /**
     * Test addInjection()
     * 
     */
    #[@test]
    public function addInjection() {
      $this->args->addInjection('some.other.Class');
      
      $this->assertEquals(
        array('webservices.rest.transport.HttpRequestAdapter', 'webservices.rest.transport.HttpResponseAdapter', 'some.other.Class'),
        $this->args->getInjections()
      );
    }
  }
?>
