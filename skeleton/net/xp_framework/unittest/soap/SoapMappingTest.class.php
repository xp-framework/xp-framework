<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.soap.SOAPClient',
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.soap.SOAPDummyTransport'
  );
  
  /**
   * Test for SOAP mapping
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SoapMappingTest extends TestCase {
  
    /**
     * Test deserialization of complex soap-type into an object
     * of the specified class.
     *
     * @access  public
     */
    #[@test]
    function testDeserialization() {
      $transport= &new SOAPDummyTransport();
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
      
      $client= &new SOAPClient($transport, 'urn://test');
      $client->registerMapping(
        new QName('http://net.xp_framework/soap/types', 'SoapTestType'),
        XPClass::forName('net.xp_framework.unittest.soap.SoapMappingTestTarget')
      );
      
      $res= $client->invoke('test');
      $this->assertClass($res, 'net.xp_framework.unittest.soap.SoapMappingTestTarget');
      $this->assertEquals('Test-String', $res->getString());
      $this->assertEquals(12345, $res->getInteger());
    }
  }
?>
