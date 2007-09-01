<?php
/* This class is part of the XP framework
 *
 * $Id: SoapMappingTest.class.php 10189 2007-05-03 13:01:32Z olli $ 
 */

  namespace net::xp_framework::unittest::soap;

  ::uses(
    'webservices.soap.xp.XPSoapClient',
    'unittest.TestCase',
    'net.xp_framework.unittest.soap.SOAPDummyTransport'
  );
  
  /**
   * Test for SOAP mapping
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SoapMappingTest extends unittest::TestCase {
  
    /**
     * Test deserialization of complex soap-type into an object
     * of the specified class.
     *
     */
    #[@test]
    public function testDeserialization() {
      $transport= new SOAPDummyTransport();
      $transport->setAnswer('<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <soapenv:Body soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
  <ns1:getTestsResponse xmlns:ns1="urn://test">
   <result href="#id1"/>
  </ns1:getTestsResponse>
  <multiRef id="id1" soapenc:root="0" soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xsi:type="ns4:SoapTestType" xmlns:ns4="http://net.xp_framework/soap/types" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">
   <string xsi:type="xsd:string">Test-String</string>
   <integer xsi:type="xsd:long">12345</integer>
  </multiRef>
 </soapenv:Body>
</soapenv:Envelope>
      ');
      
      $client= new webservices::soap::xp::XPSoapClient('http://xp-framework.net/', 'urn://test');
      $client->transport= $transport;
      $client->registerMapping(
        new QName('http://net.xp_framework/soap/types', 'SoapTestType'),
        lang::XPClass::forName('net.xp_framework.unittest.soap.SoapMappingTestTarget')
      );
      
      $res= $client->invoke('test');
      $this->assertClass($res, 'net.xp_framework.unittest.soap.SoapMappingTestTarget');
      $this->assertEquals('Test-String', $res->getString());
      $this->assertEquals(12345, $res->getInteger());
    }
    
    /**
     * Test serialization of registered type into a correct
     * soap encoding with it's representing node being in the
     * correct namespace (and not in the default XP namespace).
     *
     * This is a bit ugly, because the test is a white-box-test,
     * knows alot about the result and even violates the
     * access restrictions (_bodyElement).
     *
     */
    #[@test]
    public function testSerialization() {
      $transport= new SOAPDummyTransport();
      $client= new webservices::soap::xp::XPSoapClient('http://xp-framework.net/', 'urn://test');
      
      // Re-set transport as constructor created a copy of it!
      $client->transport= $transport;
      
      $client->registerMapping(
        new QName('http://net.xp_framework/soap/types', 'SoapTestType'),
        lang::XPClass::forName('net.xp_framework.unittest.soap.SoapMappingTestTarget')
      );
      
      try {
        $client->invoke('foo', new SoapMappingTestTarget('Teststring', 12345));
      } catch (XMLFormatException $ignored) {
        // We don't receive a "real" answer, which will cause an exception
      }
      
      $msg= $transport->getRequest();
      $body= $msg->_bodyElement();
      $this->assertEquals(
        'http://net.xp_framework/soap/types', 
        $body->children[0]->children[0]->attribute['xmlns:ns1']
      );
      $this->assertEquals(
        'ns1:SoapTestType', 
        $body->children[0]->children[0]->attribute['xsi:type']
      );
    }
  }
?>
