<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.scriptlet.rpc.mock.JsonRpcRouterMock',
    'webservices.json.JsonFactory'
  );

  /**
   * Test case for JsonRpcRpcRouter
   *
   * @see      xp://webservices.json.rpc.JsonRpcRouter
   * @purpose  Testcase
   */
  class JsonRpcRouterTest extends TestCase {
    
    /**
     * Setup test fixture
     *
     */
    public function setUp() {
      xp::gc();
      $this->router= new JsonRpcRouterMock('net.xp_framework.unittest.scriptlet.rpc.impl');
      $this->router->setMockMethod(HTTP_POST);
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.getImplementationName", "params" : [ ], "id" : 1 }');
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function basicPostRequest() {
      $this->router->init();
      $response= $this->router->process();
      
      $this->assertEquals(200, $response->statusCode);
      $this->assertEquals(
        '{ "result" : "net.xp_framework.unittest.scriptlet.rpc.impl.DummyRpcImplementationHandler" , "error" : null , "id" : 1 }',
        $response->getContent()
      );
      $this->assertTrue(in_array('Content-type: application/json; charset=UTF-8', $response->headers));
    }
    
    /**
     * Tests echo method
     *
     */
    #[@test]
    public function basicEchoTest() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.passBackMethod", "params" : [ "string" , 1 , { "object" : "object" } , [ 1, 2, 3, 4, 5 ] ] , "id" : 1 }');
      $this->router->init();
      $response= $this->router->process();
      
      $this->assertEquals(200, $response->statusCode);
      $str= $response->getContent();
      
      $decoder= JsonFactory::create();
      $data= $decoder->decode($str);
    }    

    /**
     * Test
     *
     */
    #[@test,@expect('scriptlet.HttpScriptletException')]
    public function basicGetRequest() {
      $this->router->setMockMethod(HTTP_GET);
      $this->router->init();
      $response= $this->router->process();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function callNonexistingClass() {
      $this->router->setMockData('{ "method" : "ClassDoesNotExist.getImplementationName", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= $this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function callNonexistingMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.methodDoesNotExist", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= $this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function callNonWebmethodMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.methodExistsButIsNotAWebmethod", "params" : [ ], "id" : 1 }');
      $this->router->init();
      $response= $this->router->process();
      
      $this->assertEquals(500, $response->statusCode);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function callFailingMethod() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.giveMeFault", "params" : [ ], "id" : 1 }');
      
      $this->router->init();
      $response= $this->router->process();
      $this->assertEquals(500, $response->statusCode);

      // Check for correct fault code
      $message= JsonResponseMessage::fromString($response->getContent());
      $fault= $message->getFault();
      $this->assertEquals(403, $fault->getFaultcode());
    }
    
    
    /**
     * Test
     *
     */
    #[@test]
    public function multipleParameters() {
      $this->router->setMockData('{ "method" : "DummyRpcImplementation.checkMultipleParameters", "params" : [ "Lalala", 1, [ 12, "Egypt", false, -31 ], { "lowerBound" : 18, "upperBound" : 139 } ], "id" : 12 }');
      $this->router->init();
      $response= $this->router->process();
      $this->assertTrue(in_array('Content-type: application/json; charset=UTF-8', $response->headers));
      $this->assertEquals(200, $response->statusCode);
      
      $msg= JsonResponseMessage::fromString($response->getContent());
      $data= $msg->getData();
      $this->assertEquals('Lalala', $data[0]);
      $this->assertEquals(1, $data[1]);
      $this->assertEquals(array(12, 'Egypt', FALSE, -31), $data[2]);
      $this->assertEquals(array('lowerBound' => 18, 'upperBound' => 139), $data[3]);
    }
  }
?>
