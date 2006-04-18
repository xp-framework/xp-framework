<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.scriptlet.rpc.JsonRpcRouterMock'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class JsonRpcRouterTest extends TestCase {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setUp() {
      xp::gc();
      $this->router= &new JsonRpcRouterMock(new ClassLoader('net.xp_framework.unittest.scriptlet.rpc.impl'));
      $this->router->setMockMethod(HTTP_POST);
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.getImplementationName", "params" : [ ], "id" : 1 }');
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function basicPostRequest() {
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(200, $response->statusCode);
      $this->assertEquals(
        '{ "result" : "net.xp_framework.unittest.scriptlet.rpc.impl.DummyRpcImplementationHandler" , "error" : null , "id" : 1 }',
        $response->getContent()
      );
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test,@expect('scriptlet.HttpScriptletException')]
    function basicGetRequest() {
      $this->router->setMockMethod(HTTP_GET);
      $this->router->init();
      $response= &$this->router->process();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function callNonexistingClass() {
      $this->router->setMockData('{ "method" : "ClassDoesNotExist.getImplementationName", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function callNonexistingMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.methodDoesNotExist", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function callNonWebmethodMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.methodExistsButIsNotAWebmethod", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function callFailingMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.giveMeFault", "params" : [ ], "id" : 1 }');
      
      $this->router->init();
      $response= &$this->router->process();
      $this->assertEquals(500, $response->statusCode);

      // Check for correct fault code
      $message= &JsonMessage::fromString($response->getContent());
      $fault= &$message->getFault();
      $this->assertEquals(403, $fault->getFaultcode());
    }
  }
?>
