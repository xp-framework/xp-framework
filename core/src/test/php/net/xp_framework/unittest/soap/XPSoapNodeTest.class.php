<?php namespace net\xp_framework\unittest\soap;

use unittest\TestCase;
use lang\types\Long;
use webservices\soap\xp\XPSoapNode;
use webservices\soap\xp\XPSoapMapping;
use webservices\soap\Parameter;
use webservices\soap\types\SOAPHashMap;
use webservices\soap\types\SOAPDateTime;
use webservices\soap\types\SOAPLong;
use util\Binford;


/**
 * TestCase
 *
 * @see       ...
 * @purpose   TestCase for
 */
class XPSoapNodeTest extends TestCase {

  protected function node($object) {
    $node= XPSoapNode::fromArray(array($object), 'array', new XPSoapMapping());
    return $node->nodeAt(0);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleNull() {
    $this->assertEquals(
      new XPSoapNode('item', null, array('xsi:nil' => 'true')),
      $this->node(null)
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleString() {
    $this->assertEquals(
      new XPSoapNode('item', 'my string', array('xsi:type' => 'xsd:string')),
      $this->node('my string')
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function stringType() {
    $this->assertEquals(
      new XPSoapNode('item', 'my string', array('xsi:type' => 'xsd:string')),
      $this->node(new \lang\types\String('my string'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleInteger() {
    $this->assertEquals(
      new XPSoapNode('item', 12345, array('xsi:type' => 'xsd:int')),
      $this->node(12345)
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function integerType() {
    $this->assertEquals(
      new XPSoapNode('item', 12345, array('xsi:type' => 'xsd:int')),
      $this->node(new \lang\types\Integer(12345))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function soapLong() {
    $this->assertEquals(
      new XPSoapNode('item', '12345', array('xsi:type' => 'xsd:long')),
      $this->node(new SOAPLong(12345))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function longType() {
    $this->assertEquals(
      new XPSoapNode('item', '12345', array('xsi:type' => 'xsd:long')),
      $this->node(new Long(12345))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function namedParameter() {
    $this->assertEquals(
      new XPSoapNode('name', 'content', array('xsi:type' => 'xsd:string')),
      $this->node(new Parameter('name', 'content'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleBoolean() {
    $this->assertEquals(
      new XPSoapNode('item', 'true', array('xsi:type' => 'xsd:boolean')),
      $this->node(true)
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function booleanType() {
    $this->assertEquals(
      new XPSoapNode('item', 'true', array('xsi:type' => 'xsd:boolean')),
      $this->node(new \lang\types\Boolean(true))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleDouble() {
    $this->assertEquals(
      new XPSoapNode('item', 5.0, array('xsi:type' => 'xsd:float')),
      $this->node(5.0)
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function doubleType() {
    $this->assertEquals(
      new XPSoapNode('item', '5', array('xsi:type' => 'xsd:double')),
      $this->node(new \lang\types\Double(5.0))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleHashmap() {
    $this->assertEquals(
      create(new XPSoapNode('item', null, array('xsi:type' => 'xsd:struct')))
        ->withChild(new XPSoapNode('key', 'value', array('xsi:type' => 'xsd:string'))),
      $this->node(array('key' => 'value'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function soapHashmap() {
    $node= new XPSoapNode('item', '', array('xmlns:hash' => 'http://xml.apache.org/xml-soap', 'xsi:type' => 'hash:Map'));
    $node->addChild(new XPSoapNode('item'))
      ->withChild(new XPSoapNode('key', 'key', array('xsi:type' => 'xsd:string')))
      ->withChild(new XPSoapNode('value', 'value', array('xsi:type' => 'xsd:string')));

    $this->assertEquals(
      $node,
      $this->node(new SOAPHashMap(array('key' => 'value')))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleArray() {
    $this->assertEquals(
      create(new XPSoapNode('item', null, array('xsi:type' => 'SOAP-ENC:Array', 'SOAP-ENC:arrayType' => 'xsd:anyType[3]')))
        ->withChild(new XPSoapNode('item', 'one', array('xsi:type' => 'xsd:string')))
        ->withChild(new XPSoapNode('item', 'two', array('xsi:type' => 'xsd:string')))
        ->withChild(new XPSoapNode('item', 'three', array('xsi:type' => 'xsd:string'))),
      $this->node(array('one', 'two', 'three'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function emptyArray() {
    $this->assertEquals(
      create(new XPSoapNode('item', null, array('xsi:type' => 'xsd:struct', 'xsi:nil' => 'true'))),
      $this->node(array())
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleObject() {
    $this->assertEquals(
      create(new XPSoapNode('item', null, array('xmlns:xp' => 'http://xp-framework.net/xmlns/xp', 'xsi:type' => 'xp:util.Binford')))
        ->withChild(new XPSoapNode('poweredBy', 6100, array('xsi:type' => 'xsd:int'))),
      $this->node(new Binford(6100))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleDate() {
    $this->assertEquals(
      new XPSoapNode('item', '1980-05-28T12:05:00+02:00', array('xsi:type' => 'xsd:dateTime')),
      $this->node(new \util\Date('1980-05-28 12:05:00+0200'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function soapDate() {
    $this->assertEquals(
      new XPSoapNode('item', '1980-05-28T12:05:00+02:00', array('xsi:type' => 'xsd:dateTime')),
      $this->node(new SOAPDateTime('1980-05-28 12:05:00+0200'))
    );
  }
}
