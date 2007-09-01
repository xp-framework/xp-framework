<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc;

  ::uses(
    'webservices.soap.xp.XPSoapMessage',
    'unittest.TestCase',
    'net.xp_framework.unittest.scriptlet.rpc.mock.SoapRpcRouterMock'
  );

  /**
   * Test case for SoapRpcRouter
   *
   * @see      xp://webservices.soap.rpc.SoapRpcRouter
   * @purpose  Testcase
   */
  class SoapRpcRouterTest extends unittest::TestCase {
    
    /**
     * Setup test fixture
     *
     */
    public function setUp() {
      ::xp::gc();
      $this->router= new net::xp_framework::unittest::scriptlet::rpc::mock::SoapRpcRouterMock('net.xp_framework.unittest.scriptlet.rpc.impl');
      $this->router->setMockMethod(HTTP_POST);
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#getImplementationName',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <SOAP-ENV:Envelope
         xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
         xmlns:xsd="http://www.w3.org/2001/XMLSchema"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
         xmlns:si="http://soapinterop.org/xsd"
         SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
         xmlns:ctl="DummyRpcImplementation"
        >
          <SOAP-ENV:Body>  
            <ctl:foo/>
          </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
      ');
    }
    
    /**
     * Test post request
     *
     */
    #[@test]
    public function basicPostRequest() {
      $this->router->init();
      $response= $this->router->process();
      $this->assertEquals(200, $response->statusCode);
      $this->assertTrue(in_array('Content-type: text/xml; charset=iso-8859-1', $response->headers));
    }

    /**
     * Test
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'NonExistingClass#getImplementationName',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
      
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#nonExistingMethod',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#methodExistsButIsNotAWebmethod',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#giveMeFault',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
      $this->router->init();
      $response= $this->router->process();
      $this->assertEquals(500, $response->statusCode);
      
      $message= webservices::soap::xp::XPSoapMessage::fromString($response->getContent());
      $fault= $message->getFault();
      $this->assertEquals(403, $fault->getFaultCode());
      $this->assertEquals('This is a intentionally caused exception.', $fault->getFaultString());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function multipleParameters() {
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#checkMultipleParameters',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:si="http://soapinterop.org/xsd"
 SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:ctl="DummyRpcImplementation"
>
  <SOAP-ENV:Body>  
    <ctl:checkMultipleParameters>    
      <item xsi:type="xsd:string">Lalala</item>
      <item xsi:type="xsd:int">1</item>
      <item xsi:type="SOAP-ENC:Array" SOAP-ENC:arrayType="xsd:anyType[4]">        
        <item xsi:type="xsd:int">12</item>
        <item xsi:type="xsd:string">Egypt</item>
        <item xsi:type="xsd:boolean">false</item>
        <item xsi:type="xsd:int">-31</item>
      </item>
      <item xsi:type="xsd:struct">        
        <lowerBound xsi:type="xsd:int">18</lowerBound>
        <upperBound xsi:type="xsd:int">139</upperBound>
      </item>
    </ctl:checkMultipleParameters>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
      ');
      $this->router->init();
      $response= $this->router->process();
      if (!$this->assertEquals(200, $response->statusCode)) return;

      $msg= webservices::soap::xp::XPSoapMessage::fromString($response->getContent());
      $data= array_shift($msg->getData());
      
      $this->assertEquals('Lalala', $data[0]) &&
      $this->assertEquals(1, $data[1]) &&
      $this->assertEquals(array(12, 'Egypt', FALSE, -31), $data[2]) &&
      $this->assertEquals(array('lowerBound' => 18, 'upperBound' => 139), $data[3]);
    }
  }
?>
