<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.soap.xp.XPSoapNode',
    'webservices.soap.xp.XPSoapMapping',
    'webservices.soap.Parameter'
  );

  /**
   * TestCase
   *
   * @see       ...
   * @purpose   TestCase for
   */
  class XPSoapNodeTest extends TestCase {

    protected function node($object) {
      $node= XPSoapNode::fromArray(array($object), 'array', new XPSoapMapping());
      return $node->children[0];
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
        $this->node(new String('my string'))
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
        $this->node(new Integer(12345))
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
  }
?>
