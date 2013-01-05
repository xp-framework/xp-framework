<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.routing.RestRoutingArgs'
  );
  
  /**
   * Test REST routing args
   *
   * @purpose Test
   */
  class RestRoutingArgsTest extends TestCase {
    protected $args= NULL;
    
    /**
     * Method without functionality to be used by tests.
     *
     * @param   string arg1
     * @param   string arg2 default NULL
     */
    private function demoMethod($arg1, $arg2= 'default') { }
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $params= XPClass::forName('net.xp_framework.unittest.rest.server.mock.MockArgs')
        ->newInstance()->getClass()->getMethod('methodWithMultipleArguments')->getParameters();
      $this->args= new RestRoutingArgs(
        $params,
        array('webservices.rest.server.transport.HttpRequestAdapter', 'webservices.rest.server.transport.HttpResponseAdapter')
      );
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.server.routing.RestRoutingArgs', $this->args);
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
      $params= XPClass::forName('net.xp_framework.unittest.rest.server.mock.MockArgs')
        ->newInstance()->getClass()->getMethod('methodWithAnotherArgument')->getParameters();
      $this->args->addArgument('another', $params[0]);
      
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
     * Test getArgumentType() for argument registration
     * 
     */
    #[@test]
    public function getArgumentTypeDefault() {
      $this->assertEquals(Primitive::$STRING, $this->args->getArgumentType('id'));
    }
    
    /**
     * Test getArgumentType() for argument with type
     * 
     */
    #[@test]
    public function getArgumentTypeForObject() {
      $params= XPClass::forName('net.xp_framework.unittest.rest.server.mock.MockArgs')
        ->newInstance()->getClass()->getMethod('methodWithAnotherArgument')->getParameters();
      $this->args->addArgument('another', $params[0]);
      
      $this->assertEquals(XPClass::forName('lang.Object'), $this->args->getArgumentType('another'));
    }
    
    /**
     * Test getInjections()
     * 
     */
    #[@test]
    public function getInjections() {
      $this->assertEquals(
        array('webservices.rest.server.transport.HttpRequestAdapter', 'webservices.rest.server.transport.HttpResponseAdapter'),
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
        array('webservices.rest.server.transport.HttpRequestAdapter', 'webservices.rest.server.transport.HttpResponseAdapter', 'some.other.Class'),
        $this->args->getInjections()
      );
    }
    
    /**
     * Test getInjection() with parameter name
     * 
     */
    #[@test]
    public function getInjectionByName() {
      $this->args->addInjection('payload[test]', 'arg1');
      
      $this->assertEquals('payload[test]', $this->args->getInjection('arg1'));
    }
    
    /**
     * Test getInjection() with wrong name
     * 
     */
    #[@test]
    public function getInjectionWrongName() {
      $this->assertNull($this->args->getInjection('unknown'));
    }
    
    /**
     * Test getInjection() with parameter index
     * 
     */
    #[@test]
    public function getInjectionByIndex() {
      $this->assertEquals('webservices.rest.server.transport.HttpRequestAdapter', $this->args->getInjection(0));
    }
    
    /**
     * Test getInjection() with wrong index
     * 
     */
    #[@test]
    public function getInjectionWrongIndex() {
      $this->assertNull($this->args->getInjection(5));
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
    
    /**
     * Test isInjected() for named parameter
     * 
     */
    #[@test]
    public function isInjectedByName() {
      $this->args->addInjection('some.other.Class', 'arg1');
      
      $this->assertTrue($this->args->isInjected('arg1'));
    }
    
    /**
     * Test isInjected() for parameter index
     * 
     */
    #[@test]
    public function isInjectedByIndex() {
      $this->assertTrue($this->args->isInjected(0));
    }
    
    /**
     * Test isInjected() for non-existant parameter name
     * 
     */
    #[@test]
    public function isInjectedWrongName() {
      $this->assertFalse($this->args->isInjected('unknown'));
    }
    
    /**
     * Test isInjected() for non-existant parameter index
     * 
     */
    #[@test]
    public function isInjectedWrongIndex() {
      $this->assertFalse($this->args->isInjected(5));
    }
    
    /**
     * Test optional params
     * 
     */
    #[@test]
    public function optionalParams() {
      $params= XPClass::forName('net.xp_framework.unittest.rest.server.mock.MockArgs')
        ->newInstance()->getClass()->getMethod('methodWithOptionalArguments')->getParameters();
      $restArgs= new RestRoutingArgs($params);
      $this->assertFalse($restArgs->isArgumentOptional('arg1'));
      $this->assertTrue($restArgs->isArgumentOptional('arg2'));
    }
    
    /**
     * Test params default value
     * 
     */
    #[@test]
    public function paramsDefaultValue() {
      $params= XPClass::forName('net.xp_framework.unittest.rest.server.mock.MockArgs')
        ->newInstance()->getClass()->getMethod('methodWithOptionalArguments')->getParameters();
      $restArgs= new RestRoutingArgs($params);
      $this->assertEquals('default', $restArgs->getArgumentDefaultValue('arg2'));
    }
  }
?>
