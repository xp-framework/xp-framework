<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'webservices.soap.xp.XPSoapMessage'
  );

  /**
   * Test SOAP api
   *
   * @purpose  Unit Test
   */
  class SoapTest extends TestCase {
      
    /**
     * Test serialization
     *
     */
    #[@test]
    public function testSerialization() {
      $msg= new XPSoapMessage();
      $msg->createCall('Test', 'testSerialization');
      $this->assertEquals($msg->action, 'Test');
      $this->assertEquals($msg->method, 'testSerialization');
      $this->assertEquals($msg->root->name, 'SOAP-ENV:Envelope');
      $this->assertNotEmpty($msg->root->attribute);
      $msg->setData(array(
        'int'       => 1,
        'float'     => 6.1,
        'string'    => 'Binford',
        'string2'   => '"<&>"',
        'bool'      => TRUE,
        'date'      => Date::fromString('1977-12-14 11:55AM'),
        'null'      => NULL,
        'array'     => array(2, 3),
        'hash'      => array(
          'class'     => 'Test',
          'method'    => 'testSerialization'
        )
      ));
      
      // Let's be somewhat forgiving on whitespace
      $src= trim(chop($msg->getSource(0)));
      $this->assertEquals(substr($src, 0, 18), '<SOAP-ENV:Envelope');
      $this->assertEquals(substr($src, -20), '</SOAP-ENV:Envelope>');
      
      $this->assertContains(
        $src, '<int xsi:type="xsd:int">1</int>', 'integer'
      );
      $this->assertContains(
        $src, '<float xsi:type="xsd:float">6.1</float>', 'float'
      );
      $this->assertContains(
        $src, '<string xsi:type="xsd:string">Binford</string>', 'string'
      );
      $this->assertContains(
        $src, '<string2 xsi:type="xsd:string">&quot;&lt;&amp;&gt;&quot;</string2>', 'escaping'
      );
      $this->assertContains(
        $src, '<bool xsi:type="xsd:boolean">true</bool>', 'bool'
      );
      $this->assertContains(
        $src, '<date xsi:type="xsd:dateTime">1977-12-14T11:55:00</date>', 'date'
      );
      $this->assertContains(
        $src, '<null xsi:nil="true"/>', 'null'
      );
      $this->assertContains(
        $src, '<array xsi:type="SOAP-ENC:Array" SOAP-ENC:arrayType="xsd:anyType[2]">', 'array'
      );
      $this->assertContains(
        $src, '<item xsi:type="xsd:int">2</item>', 'array.inner'
      );
      $this->assertContains(
        $src, '<item xsi:type="xsd:int">3</item>', 'array.inner'
      );
      $this->assertContains(
        $src, '<hash xsi:type="xsd:struct">', 'hash'
      );
      $this->assertContains(
        $src, '<class xsi:type="xsd:string">Test</class>', 'hash.inner'
      );
      $this->assertContains(
        $src, '<method xsi:type="xsd:string">testSerialization</method>', 'hash.inner'
      );
      return $src;
    }
    
    /**
     * Test deserialization of SOAP header
     *
     */
    #[@test]
    public function testHeader() {
      $msg= XpSoapMessage::fromString('
        <SOAP-ENV:Envelope
          xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <SOAP-ENV:Header>
          <targetAddress SOAP-ENV:mustUnderstand="1">
            http://tokyo:8004/glue/urn:CorpDataServices
          </targetAddress>
        </SOAP-ENV:Header>
        <SOAP-ENV:Body>
          <ns1:getQuote
           xmlns:ns1="urn:DirectedQuoteProxyService"
           SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <stocks xsi:type="xsd:string">AMX</stocks>
          </ns1:getQuote>
        </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
      ');
      
      $headers= $msg->getHeaders();
      $this->assertNotEquals(NULL, $msg->getHeaders());
      $this->assertEquals(1, sizeof ($headers));
      foreach ($headers as $h) { $this->assertSubclass($h, 'webservices.soap.xp.XPSoapHeaderElement'); }
    }
  }
?>
