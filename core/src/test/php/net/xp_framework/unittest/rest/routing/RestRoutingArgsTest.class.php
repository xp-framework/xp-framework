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
     * Test getArguments()
     * 
     */
    #[@test]
    public function getArgumnets() {
      $this->assertEquals(array('id', 'title', 'name'), $this->args->getArguments());
    }
    
    /**
     * Test addArgument()
     * 
     */
    #[@test]
    public function addArgument() {
      $this->args->addArgument('another');
      
      $this->assertEquals(array('id', 'title', 'name', 'another'), $this->args->getArguments());
    }
    
    /**
     * Test hasArgument()
     * 
     */
    #[@test]
    public function hasArgument() {
      $this->assertTrue($this->args->hasArgument('id'));
      $this->assertFalse($this->args->hasArgument('unknown'));
    }
    
    /**
     * Test getArgumentType() for argument registration without specifying
     * type to default to Type::$VAR
     * 
     */
    #[@test]
    public function getArgumentTypeDefault() {
      $this->assertEquals(Type::$VAR, $this->args->getArgumentType('id'));
    }
    
    /**
     * Test getArgumentType() for argument with type
     * 
     */
    #[@test]
    public function getArgumentTypeForObject() {
      $this->args->addArgument('obj', XPClass::forName('lang.Object'));
      
      $this->assertEquals(XPClass::forName('lang.Object'), $this->args->getArgumentType('obj'));
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
    
    /**
     * Test getInjectionRef()
     * 
     */
    #[@test]
    public function getInjectionRef() {
      $this->args->addInjection('some.other.Class', 'arg1');
      
      $this->assertEquals('arg1', $this->args->getInjectionRef(2));
    }
  }
?>
