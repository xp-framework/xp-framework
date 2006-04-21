<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.scriptlet.rpc.XmlRpcRouterMock'
  );

  /**
   * Test case for XmlRpcRpcRouter
   *
   * @see      xp://xml.xmlrpc.rpc.XmlRpcRouter
   * @purpose  Testcase
   */
  class XmlRpcRouterTest extends TestCase {
    
    /**
     * Setup test fixture
     *
     * @access  public
     */
    function setUp() {
      xp::gc();
      $this->router= &new XmlRpcRouterMock(new ClassLoader('net.xp_framework.unittest.scriptlet.rpc.impl'));
      $this->router->setMockMethod(HTTP_POST);
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <methodCall>
          <methodName>DummyRpcImplementation.getImplementationName</methodName>
          <params/>
        </methodCall>
      ');
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
      $this->assertIn($response->headers, 'Content-type: text/xml; charset=iso-8859-1');
      
      $msg= &XmlRpcResponseMessage::fromString($response->getContent());
      $this->assertEquals('net.xp_framework.unittest.scriptlet.rpc.impl.DummyRpcImplementationHandler', $msg->getData());
    }

    /**
     * Test
     *
     * @access  public
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
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
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <methodCall>
          <methodName>ClassDoesNotExist.getImplementationName</methodName>
          <params/>
        </methodCall>
      ');
      
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
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <methodCall>
          <methodName>DummyRpcImplementation.methodDoesNotExist</methodName>
          <params/>
        </methodCall>
      ');
      
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
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <methodCall>
          <methodName>DummyRpcImplementation.methodExistsButIsNotAWebmethod</methodName>
          <params/>
        </methodCall>
      ');
      
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
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <methodCall>
          <methodName>DummyRpcImplementation.giveMeFault</methodName>
          <params/>
        </methodCall>
      ');
      
      $this->router->init();
      $response= &$this->router->process();
      $this->assertEquals(500, $response->statusCode);

      // Check for correct fault code
      $message= &XmlRpcResponseMessage::fromString($response->getContent());
      $fault= &$message->getFault();
      $this->assertEquals(403, $fault->getFaultcode());
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function multipleParameters() {
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <methodCall>
          <methodName>DummyRpcImplementation.checkMultipleParameters</methodName>
          <params>
            <param>
              <value>
                <string>Lalala</string>
              </value>
            </param>
            <param>
              <value>
                <int>1</int>
              </value>
            </param>
            <param>
              <value>
                <array>
                  <data>
                    <value><i4>12</i4></value>
                    <value><string>Egypt</string></value>
                    <value><boolean>0</boolean></value>
                    <value><i4>-31</i4></value>
                  </data>
                </array>
              </value>
            </param>
            <param>
              <value>
                <struct>
                  <member>
                    <name>lowerBound</name>
                    <value><i4>18</i4></value>
                  </member>
                  <member>
                    <name>upperBound</name>
                    <value><i4>139</i4></value>
                  </member>
                </struct>
              </value>
            </param>
          </params>
        </methodCall>
      ');
      
      $this->router->init();
      $response= &$this->router->process();
      $this->assertIn($response->headers, 'Content-type: text/xml; charset=iso-8859-1');
      $this->assertEquals(200, $response->statusCode);
      
      $msg= &XmlRpcResponseMessage::fromString($response->getContent());
      $data= $msg->getData();
      $this->assertEquals('Lalala', $data[0]);
      $this->assertEquals(1, $data[1]);
      $this->assertEquals(array(12, 'Egypt', FALSE, -31), $data[2]);
      $this->assertEquals(array('lowerBound' => 18, 'upperBound' => 139), $data[3]);
    }
  }
?>
