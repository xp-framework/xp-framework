<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse',
    'webservices.rest.server.routing.RestPath',
    'webservices.rest.server.transport.JsonHttpRequestAdapter',
    'webservices.rest.server.transport.JsonHttpResponseAdapter'
  );
  
  /**
   * Test RestMethodRoute class
   *
   */
  class RestMethodRouteTest extends TestCase {
    protected
      $target= NULL,
      $targetMethod= NULL;
    
    /**
     * Set up
     * 
     */
    public function setUp() {
      $this->target= newinstance('lang.Object', array(), '{
        protected static $invoked= NULL;
        protected static $args= array();
        
        public function setInvoked($value= TRUE) {
          self::$invoked= TRUE;
          self::$args= func_get_args();
        }
        
        public function setInvokedMultiple($value, $other) {
          self::$invoked= TRUE;
          self::$args= func_get_args();
        }
        
        public function getInvoked() {
          return self::$invoked;
        }
        
        public function getInvokedArgs() {
          return self::$args;
        }
      }');
      $this->targetMethod= $this->target->getClass()->getMethod('setInvoked');
      $this->targetMethodMultiple= $this->target->getClass()->getMethod('setInvokedMultiple');
    }
    
    /**
     * Create method route
     * 
     * @return webservices.rest.routing.RestMethodRoute
     */
    protected function routeFor($path= '/path/{value}', $target= NULL) {
      return new RestMethodRoute($target === NULL ? $this->targetMethod : $target);
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.server.routing.RestMethodRoute', $this->routeFor());
    }
    
    /**
     * Test routing to method
     * 
     */
    #[@test]
    public function routeToMethod() {
      $this->routeFor()->process();
      
      $this->assertTrue($this->target->getInvoked());
    }
    
    /**
     * Test routing to method with argument
     * 
     */
    #[@test]
    public function routeToMethodWithArg() {
      $this->routeFor('/path/{value}', $this->targetMethod)->process(array(123));
      
      $this->assertEquals(array(123), $this->target->getInvokedArgs());
    }
    
    /**
     * Test routing to method with multiple argument
     * 
     */
    #[@test]
    public function routeToMethodWithMultipleArgs() {
      $route= $this->routeFor('/path/{other}/thing/{value}', $this->targetMethodMultiple)->process(array(123, 6100));
      
      $this->assertEquals(array(123, 6100), $this->target->getInvokedArgs());
    }
    
    /**
     * Test routing to method with missing arguments
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function routeToMethodWithMissingArgs() {
      $this->routeFor('/path/{value}', $this->targetMethodMultiple)->process();
    }
  }
?>
