<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.scriptlet.rpc.SoapRpcRouterMock'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SoapRpcRouterTest extends TestCase {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setUp() {
      xp::gc();
      $this->router= &new SoapRpcRouterMock(new ClassLoader('net.xp_framework.unittest.scriptlet.rpc.impl'));
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
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test, @expect('scriptlet.HttpScriptletException')]
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'NonExistingClass#getImplementationName',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
      
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#nonExistingMethod',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#methodExistsButIsNotAWebmethod',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
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
      $this->router->setMockHeaders(array(
        'SOAPAction'    => 'DummyRpcImplementation#giveMeFault',
        'Content-Type'  => 'text/xml; charset=iso-8859-1'
      ));
      $this->router->init();
      $response= &$this->router->process();
      $this->assertEquals(500, $response->statusCode);
      
      $message= &SOAPMessage::fromString($response->getContent());
      $fault= &$message->getFault();
      $this->assertEquals(403, $fault->getFaultCode());
      $this->assertEquals('This is a intentionally caused exception.', $fault->getFaultString());
    }
  }
?>
