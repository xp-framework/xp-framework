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
   * Test for SOAP client class
   *
   * @see      xp://xml.soap.SOAPClient
   * @purpose  Unittest
   */
  class SoapClientTest extends TestCase {
  
    /**
     * Ensures an IllegalArgumentException is thrown in case we have 
     * multiple output arguments
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function testMultipleOutputArguments() {
      $transport= &new SOAPDummyTransport();
      $transport->setAnswer('<?xml version="1.0" encoding="iso-8859-1"?>
<SOAP-ENV:Envelope
 xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:si="http://soapinterop.org/xsd"
 SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
 xmlns:ctl="ctl"
>
  <SOAP-ENV:Body>  
    <ctl:irrelevant>    
      <item xsi:nil="true"/>
      <item xsi:nil="true"/>
    </ctl:irrelevant>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope> 
');
      
      $client= &new SOAPClient($transport, 'urn://test');
      $client->invoke('irrelevant');
    }
  }
?>
