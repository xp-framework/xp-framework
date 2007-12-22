<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.Tree',
    'webservices.xmlrpc.XmlRpcDecoder'
  );

  /**
   * TestCase
   *
   * @see      xp://webservices.xmlrpc.XmlRpcDecoder
   * @purpose  Unittest
   */
  class XmlRpcDecoderTest extends TestCase {
  
    /**
     * Decode xml
     *
     * @param   string xml
     * @return  mixed
     */
    protected function decode($xml) {
      return create(new XmlRpcDecoder())->decode(
        Tree::fromString('<?xml version="1.0" encoding="utf-8"?><value>'.$xml.'</value>')->root
      );
    }
  
    /**
     * Test decoding an integer.
     *
     */
    #[@test]
    public function int() {
      $this->assertEquals(1, $this->decode('<int>1</int>'));
    }

    /**
     * Test decoding an integer.
     *
     */
    #[@test]
    public function i4() {
      $this->assertEquals(1, $this->decode('<i4>1</i4>'));
    }

    /**
     * Test decoding an double.
     *
     */
    #[@test]
    public function double() {
      $this->assertEquals(1.0, $this->decode('<double>1.0</double>'));
    }

    /**
     * Test decoding a string.
     *
     */
    #[@test]
    public function string() {
      $this->assertEquals('Hello', $this->decode('<string>Hello</string>'));
    }

    /**
     * Test decoding a string.
     *
     */
    #[@test]
    public function emptyString() {
      $this->assertEquals('', $this->decode('<string></string>', 'long form'));
      $this->assertEquals('', $this->decode('<string/>', 'short form'));
    }

    /**
     * Test decoding a boolean FALSE.
     *
     */
    #[@test]
    public function false() {
      $this->assertEquals(FALSE, $this->decode('<boolean>0</boolean>'));
    }

    /**
     * Test decoding a boolean FALSE.
     *
     */
    #[@test]
    public function true() {
      $this->assertEquals(TRUE, $this->decode('<boolean>1</boolean>'));
    }

    /**
     * Test decoding NULL.
     *
     */
    #[@test]
    public function nil() {
      $this->assertEquals(NULL, $this->decode('<nil/>'));
    }

    /**
     * Test decoding an empty array.
     *
     */
    #[@test]
    public function emptyArray() {
      $this->assertEquals(array(), $this->decode('<array><data></data></array>'), 'long form');
      $this->assertEquals(array(), $this->decode('<array><data/></array>'), 'short form');
    }

    /**
     * Test decoding an array with one element
     *
     */
    #[@test]
    public function oneElementArray() {
      $this->assertEquals(array(1), $this->decode('
        <array>
          <data>
            <value><i4>1</i4></value>
          </data>
        </array>
      '));
    }

    /**
     * Test decoding the example array from the XMLRPC spec
     *
     * @see   http://www.xmlrpc.com/spec
     */
    #[@test]
    public function exampleArray() {
      $this->assertEquals(array(12, 'Egypt', FALSE, -31), $this->decode('
        <array>
          <data>
            <value><i4>12</i4></value>
            <value><string>Egypt</string></value>
            <value><boolean>0</boolean></value>
            <value><i4>-31</i4></value>
          </data>
        </array>
      '));
    }

    /**
     * Test decoding an empty struct.
     *
     */
    #[@test]
    public function emptyStruct() {
      $this->assertEquals(array(), $this->decode('<struct></struct>'), 'long form');
      $this->assertEquals(array(), $this->decode('<struct/>'), 'short form');
    }

    /**
     * Test decoding the example struct from the XMLRPC spec
     *
     * @see   http://www.xmlrpc.com/spec
     */
    #[@test]
    public function exampleStruct() {
      $this->assertEquals(array('lowerBound' => 18, 'upperBound' => 139), $this->decode('
        <struct>
          <member>
            <name>lowerBound</name>
            <value><i4>18</i4></value>
          </member>
          <member>
            <name>upperBound</name>
            <value><i4>139</i4></value>
          </member>
        </struct>
      '));
    }

    /**
     * Test decoding a struct with our special __xp_class member.
     *
     */
    #[@test]
    public function valueObject() {
      $vo= $this->decode('
        <struct>
          <member>
            <name>__xp_class</name>
            <value><string>net.xp_framework.unittest.scriptlet.rpc.ValueObject</string></value>
          </member>
          <member>
            <name>name</name>
            <value><string>Timm</string></value>
          </member>
          <member>
            <name>age</name>
            <value><int>30</int></value>
          </member>
          <member>
            <name>_new</name>
            <value><boolean>1</boolean></value>
          </member>
        </struct>
      ');
      $this->assertClass($vo, 'net.xp_framework.unittest.scriptlet.rpc.ValueObject');
      $this->assertEquals('Timm', $vo->getName());
      $this->assertEquals(30, $vo->getAge());
      $this->assertEquals(TRUE, $vo->isNew());
    }

    /**
     * Test decoding a struct with our special __xp_class member.
     *
     */
    #[@test]
    public function valueObjectMemberOmitted() {
      $vo= $this->decode('
        <struct>
          <member>
            <name>__xp_class</name>
            <value><string>net.xp_framework.unittest.scriptlet.rpc.ValueObject</string></value>
          </member>
          <member>
            <name>name</name>
            <value><string>Timm</string></value>
          </member>
          <member>
            <name>_new</name>
            <value><boolean>1</boolean></value>
          </member>
        </struct>
      ');
      $this->assertClass($vo, 'net.xp_framework.unittest.scriptlet.rpc.ValueObject');
      $this->assertEquals('Timm', $vo->getName());
      $this->assertEquals(0, $vo->getAge());
      $this->assertEquals(TRUE, $vo->isNew());
    }

    /**
     * Test decoding a struct with our special __xp_class member.
     *
     */
    #[@test]
    public function valueObjectMembersAreNotInvented() {
      $vo= $this->decode('
        <struct>
          <member>
            <name>__xp_class</name>
            <value><string>net.xp_framework.unittest.scriptlet.rpc.ValueObject</string></value>
          </member>
          <member>
            <name>Power</name>
            <value><i4>6100</i4></value>
          </member>
        </struct>
      ');
      $this->assertClass($vo, 'net.xp_framework.unittest.scriptlet.rpc.ValueObject');
      $this->assertFalse(property_exists($vo, 'Power'));
    }

    /**
     * Test decoding a struct with our special __xp_class member.
     *
     */
    #[@test]
    public function valueObjectStaticMembersNotSettable() {
      $vo= $this->decode('
        <struct>
          <member>
            <name>__xp_class</name>
            <value><string>net.xp_framework.unittest.scriptlet.rpc.ValueObject</string></value>
          </member>
          <member>
            <name>cache</name>
            <value><string>INJECTED</string></value>
          </member>
        </struct>
      ');
      $this->assertClass($vo, 'net.xp_framework.unittest.scriptlet.rpc.ValueObject');
      $this->assertNull($vo->getClass()->getField('cache')->get(NULL));
    }

    /**
     * Test decoding dates.
     *
     */
    #[@test]
    public function date() {
      $iso8601String= '20051129T18:13:48';
      $this->assertEquals(
        new Date($iso8601String), 
        $this->decode('<dateTime.iso8601>'.$iso8601String.'</dateTime.iso8601>'
      ));
    }
 
    /**
     * Test decoding unsupported types will raise an exception.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unsupported() {
      $this->decode('<not_a_type>any-value</not_a_type>');
    }
 }
?>
