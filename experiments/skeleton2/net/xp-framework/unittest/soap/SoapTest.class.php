<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'xml.soap.SOAPMessage'
  );

  /**
   * Test SOAP api
   *
   * @purpose  Unit Test
   */
  class SoapTest extends TestCase {
      
    /**
     * Test serializatio
     *
     * @access  public
     */
    public function testSerialization() {
      $msg= new SOAPMessage();
      $msg->create('Test', 'testSerialization');
      self::assertEquals($msg->action, 'Test');
      self::assertEquals($msg->method, 'testSerialization');
      self::assertEquals($msg->root->name, 'SOAP-ENV:Envelope');
      self::assertNotEmpty($msg->root->attribute);
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
      self::assertEquals(substr($src, 0, 18), '<SOAP-ENV:Envelope');
      self::assertEquals(substr($src, -20), '</SOAP-ENV:Envelope>');
      
      self::assertContains(
        $src, '<int xsi:type="xsd:int">1</int>', 'integer'
      );
      self::assertContains(
        $src, '<float xsi:type="xsd:float">6.1</float>', 'float'
      );
      self::assertContains(
        $src, '<string xsi:type="xsd:string">Binford</string>', 'string'
      );
      self::assertContains(
        $src, '<string2 xsi:type="xsd:string">&quot;&lt;&amp;&gt;&quot;</string2>', 'escaping'
      );
      self::assertContains(
        $src, '<bool xsi:type="xsd:boolean">true</bool>', 'bool'
      );
      self::assertContains(
        $src, '<date xsi:type="xsd:dateTime">1977-12-14T11:55:00</date>', 'date'
      );
      self::assertContains(
        $src, '<null xsi:nil="true"/>', 'null'
      );
      self::assertContains(
        $src, '<array xsi:type="SOAP-ENC:Array" SOAP-ENC:arrayType="xsd:anyType[2]">', 'array'
      );
      self::assertContains(
        $src, '<item xsi:type="xsd:int">2</item>', 'array.inner'
      );
      self::assertContains(
        $src, '<item xsi:type="xsd:int">3</item>', 'array.inner'
      );
      self::assertContains(
        $src, '<hash xsi:type="xsd:ur-type">', 'hash'
      );
      self::assertContains(
        $src, '<class xsi:type="xsd:string">Test</class>', 'hash.inner'
      );
      self::assertContains(
        $src, '<method xsi:type="xsd:string">testSerialization</method>', 'hash.inner'
      );
      return $src;
    }
  }
?>
