<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
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
  class SoapRpcRouterTest extends TestCase {
    
    /**
     * Setup test fixture
     *
     */
    public function setUp() {
      $this->router= new SoapRpcRouterMock('net.xp_framework.unittest.scriptlet.rpc.impl');
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
     * Test get request
     *
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
    public function basicGetRequest() {
      $this->router->setMockMethod(HTTP_GET);
      $this->router->init();
      $response= $this->router->process();
    }
    
    /**
     * Test calling a nonexistant class results in HTTP status code 500
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
     * Test calling a nonexistant method results in HTTP status code 500
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
     * Test calling a method without @webmethod annotation
     * results in HTTP status code 500
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
     * Test calling a failing function results in
     * HTTP status code 403
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
      
      $message= XPSoapMessage::fromString($response->getContent());
      $fault= $message->getFault();
      $this->assertEquals(403, $fault->getFaultCode());
      $this->assertEquals('This is a intentionally caused exception.', $fault->getFaultString());
    }
    
    /**
     * Test multiple parameters are corretly deserialized
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

      $msg= XPSoapMessage::fromString($response->getContent());
      $data= current($msg->getData());
      
      $this->assertEquals('Lalala', $data[0]) &&
      $this->assertEquals(1, $data[1]) &&
      $this->assertEquals(array(12, 'Egypt', FALSE, -31), $data[2]) &&
      $this->assertEquals(array('lowerBound' => 18, 'upperBound' => 139), $data[3]);
    }
    
    /**
     * Test messages in encoding ISO-8859-1 are deserialized
     * correctly
     *
     */
    #[@test]
    public function handleIso88591Message() {
      $this->router->setMockHeaders(array(
        'Host'          => 'outage.xp-framework.net',
        'Connection'    => 'Keep-Alive',
        'Content-Type'  => 'text/xml; charset=iso-8859-1',
        'SOAPAction'    => 'DummyRpcImplementation#checkUTF8Content',
        'User-Agent'    => 'PHP SOAP 0.1'
      ));
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
        <SOAP-ENV:Envelope 
         xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
         xmlns:ns1="urn:Outage" 
         xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
         xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
         SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
          <SOAP-ENV:Body>
            <ns1:createDSLOutageStringDate>
              <description xsi:type="xsd:string">Störung in Düsseldorf</description>
            </ns1:createDSLOutageStringDate>
          </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
      ');
      
      $this->router->init();
      $response= $this->router->process();
      
      // The executed method throws an error, if the string is wrong and this
      // will be indicated by a different statuscode than 200
      $this->assertEquals(200, $response->statusCode);
    }
    
    
    /**
     * Test messages in encoding UTF-8 are deserialized
     * correctly
     *
     */
    #[@test]
    public function handleUTF8Message() {
      $this->router->setMockHeaders(array(
        'Host'          => 'outage.xp-framework.net',
        'Connection'    => 'Keep-Alive',
        'Content-Type'  => 'text/xml; charset=utf-8',
        'SOAPAction'    => 'DummyRpcImplementation#checkUTF8Content',
        'User-Agent'    => 'PHP SOAP 0.1'
      ));
      $this->router->setMockData('<?xml version="1.0" encoding="UTF-8"?>
        <SOAP-ENV:Envelope 
         xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
         xmlns:ns1="urn:Outage" 
         xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
         xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
         SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
          <SOAP-ENV:Body>
            <ns1:createDSLOutageStringDate>
              <description xsi:type="xsd:string">StÃ¶rung in DÃ¼sseldorf</description>
            </ns1:createDSLOutageStringDate>
          </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
      ');
      
      $this->router->init();
      $response= $this->router->process();
      
      // The executed method throws an error, if the string is wrong and this
      // will be indicated by a different statuscode than 200
      $this->assertEquals(200, $response->statusCode);
    }
  }
?>
