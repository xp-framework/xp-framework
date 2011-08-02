<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse',
    'webservices.rest.routing.RestPath',
    'webservices.rest.transport.JsonHttpRequestAdapter',
    'webservices.rest.transport.JsonHttpResponseAdapter'
  );
  
  /**
   * Test RestMethodRoute class
   *
   */
  class RestMethodRouteTest extends TestCase {
    protected
      $target= NULL,
      $targetMethod= NULL,
      $request= NULL,
      $response= NULL;
    
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
      
      $this->request= new JsonHttpRequestAdapter(new HttpScriptletRequest());
      $this->response= new JsonHttpResponseAdapter(new HttpScriptletResponse());
    }
    
    /**
     * Create method route
     * 
     * @return webservices.rest.routing.RestMethodRoute
     */
    protected function routeFor($path= '/path/{value}', $target= NULL, $args= array()) {
      return new RestMethodRoute(
        $target === NULL ? $this->targetMethod : $target,
        $args
      );
    }
    
    /**
     * Test instance
     * 
     */
    #[@test]
    public function instance() {
      $this->assertInstanceOf('webservices.rest.routing.RestMethodRoute', $this->routeFor());
    }
    
    /**
     * Test routing to method
     * 
     */
    #[@test]
    public function routeToMethod() {
      $this->routeFor()->route($this->request, $this->response);
      
      $this->assertTrue($this->target->getInvoked());
    }
    
    /**
     * Test routing to method with argument
     * 
     */
    #[@test]
    public function routeToMethodWithArg() {
      $route= $this->routeFor('/path/{value}', $this->targetMethod, array(
        'value' => 123
      ));
      $route->route($this->request, $this->response);
      
      $this->assertEquals(array(123), $this->target->getInvokedArgs());
    }
    
    /**
     * Test routing to method with multiple argument
     * 
     */
    #[@test]
    public function routeToMethodWithMultipleArgs() {
      $route= $this->routeFor('/path/{other}/thing/{value}', $this->targetMethodMultiple, array(
        'other' => 6100,
        'value' => 123
      ));
      $route->route($this->request, $this->response);
      
      $this->assertEquals(array(123, 6100), $this->target->getInvokedArgs());
    }
    
    /**
     * Test routing to method with missing arguments
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function routeToMethodWithMissingArgs() {
      $route= $this->routeFor('/path/{value}', $this->targetMethodMultiple);
      $route->route($this->request, $this->response);
    }
  }
?>
