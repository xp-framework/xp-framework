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
   * Test case for JsonRpcRpcRouter
   *
   * @see      xp://org.json.rpc.JsonRpcRouter
   * @purpose  Testcase
   */
  class JsonRpcRouterTest extends TestCase {
    
    /**
     * Setup test fixture
     *
     * @access  public
     */
    function setUp() {
      xp::gc();
      $this->router= &new JsonRpcRouterMock(new ClassLoader('net.xp_framework.unittest.scriptlet.rpc.impl'));
      $this->router->setMockMethod(HTTP_POST);
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.getImplementationName", "params" : [ ], "id" : 1 }');
    }
    
    /**
     * Test
     *
     * @access  public
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
      $this->assertIn($response->headers, 'Content-type: application/json; charset=iso-8859-1');
    }

    /**
     * Test
     *
     * @access  public
     */
    #[@test,@expect('scriptlet.HttpScriptletException')]
    function basicGetRequest() {
      $this->router->setMockMethod(HTTP_GET);
      $this->router->init();
      $response= &$this->router->process();
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function callNonexistingClass() {
      $this->router->setMockData('{ "method" : "ClassDoesNotExist.getImplementationName", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function callNonexistingMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.methodDoesNotExist", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }

    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function callNonWebmethodMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.methodExistsButIsNotAWebmethod", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }

    /**
     * Test
     *
     * @access  public
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
