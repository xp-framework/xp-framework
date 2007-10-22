<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.soap.xp.XPSoapClient',
    'unittest.TestCase',
    'net.xp_framework.unittest.soap.SOAPDummyTransport'
  );
  
  /**
   * Test for SOAP client class
   *
   * @see      xp://webservices.soap.SOAPClient
   * @purpose  Unittest
   */
  class SoapClientTest extends TestCase {
  
    /**
     * Test a array is returned when multiple returns
     *
     */
    #[@test]
    public function testMultipleOutputArguments() {
      $transport= new SOAPDummyTransport();
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
      
      $client= new XPSoapClient('http://xp-framework.net/', 'urn://test');
      $client->transport= $transport;
      $this->assertEquals(array(NULL, NULL), $client->invoke('irrelevant'));
    }
  
    /**
     * Ensures no exception is thrown in case we have 
     * no output arguments
     *
     */
    #[@test]
    public function testNoOutputArguments() {
      $transport= new SOAPDummyTransport();
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
    <ctl:irrelevant/>    
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope> 
');
      
      $client= new XPSoapClient('http://xp-framework.net/', 'urn://test');
      $client->transport= $transport;
      $client->invoke('irrelevant');
    }

    /**
     * Ensures no exception is thrown in case we have 
     * one output arguments
     *
     */
    #[@test]
    public function testOneOutputArguments() {
      $transport= new SOAPDummyTransport();
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
      <item xsi:type="xsd:int">5</item>
    </ctl:irrelevant>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope> 
');
      
      $client= new XPSoapClient('http://xp-framework.net/', 'urn://test');
      $client->transport= $transport;
      $this->assertEquals(5, $client->invoke('irrelevant'));
    }

    /**
     * Check for correct array handling in root node
     *
     */
    #[@test]
    public function testRootArrayResult() {
      $transport= new SOAPDummyTransport();
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
      <item xsi:type="xsi:array">
        <string>first value</string>
        <string>second value</string>
      </item>
    </ctl:irrelevant>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
');
      
      $client= new XPSoapClient('http://xp-framework.net/', 'urn://test');
      $client->transport= $transport;
      $this->assertEquals(array('first value', 'second value'), $client->invoke('irrelevant'));
    }

    /**
     * Check for correct array handling in sub nodes
     *
     */
    #[@test]
    public function testDeepArrayResult() {
          $transport= new SOAPDummyTransport();
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
      <item>
        <scalar>some string</scalar>
        <array>
          <string>first value</string>
          <string>second value</string>
        </array>
      </item>
    </ctl:irrelevant>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
');
      
      $client= new XPSoapClient('http://xp-framework.net/', 'urn://test');
      $client->transport= $transport;
      $this->assertEquals(array(
        'scalar' => 'some string',
        'array' => array(
          'string' => array('first value', 'second value')
        )
      ), $client->invoke('irrelevant'));
    }
  }
?>
