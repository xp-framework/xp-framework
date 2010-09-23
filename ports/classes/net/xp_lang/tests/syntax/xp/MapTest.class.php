<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class MapTest extends ParserTestCase {
  
    /**
     * Test an empty untyped map
     *
     */
    #[@test]
    public function emptyUntypedMap() {
      $this->assertEquals(
        array(new MapNode(array(
          'elements'      => NULL,
          'type'          => NULL,
        ))), 
        $this->parse('[:];')
      );
    }

    /**
     * Test an empty typed map
     *
     */
    #[@test]
    public function emptyTypedMap() {
      $this->assertEquals(array(new MapNode(array(
        'elements'      => NULL,
        'type'          => new TypeName('[:string]'),
      ))), $this->parse('
        new [:string] {:};
      '));
    }

    /**
     * Test "[:int[]]"
     *
     */
    #[@test]
    public function intArrayMap() {
      $this->assertEquals(array(new MapNode(array(
        'elements'      => NULL,
        'type'          => new TypeName('[:int[]]'),
      ))), $this->parse('
        new [:int[]] {:};
      '));
    }

    /**
     * Test "[:var[]]"
     *
     */
    #[@test]
    public function varArrayMap() {
      $this->assertEquals(array(new MapNode(array(
        'elements'      => NULL,
        'type'          => new TypeName('[:var[]]'),
      ))), $this->parse('
        new [:var[]] {:};
      '));
    }

    /**
     * Test "[:[:int]]"
     *
     */
    #[@test]
    public function intMapMap() {
      $this->assertEquals(array(new MapNode(array(
        'elements'      => NULL,
        'type'          => new TypeName('[:[:int]]'),
      ))), $this->parse('
        new [:[:int]] {:};
      '));
    }

    /**
     * Test "[:util.Vector<lang.types.String>]"
     *
     */
    #[@test]
    public function stringToGeneric() {
      $this->assertEquals(array(new MapNode(array(
        'elements'      => NULL,
        'type'          => new TypeName('[:util.Vector<lang.types.String>]'),
      ))), $this->parse('
        new [:util.Vector<lang.types.String>] {:};
      '));
    }

    /**
     * Test a non-empty untyped map
     *
     */
    #[@test]
    public function untypedMap() {
      $this->assertEquals(
        array(new MapNode(array(
          'elements'      => array(
            array(
              new IntegerNode('1'),
              new StringNode('one'),
            ),
            array(
              new IntegerNode('2'),
              new StringNode('two'),
            ),
            array(
              new IntegerNode('3'),
              new StringNode('three'),
            ),
          ),
          'type'          => NULL,
        ))), 
        $this->parse('[ 1 : "one", 2 : "two", 3 : "three" ];')
      );
    }

    /**
     * Test a non-empty typed map
     *
     */
    #[@test]
    public function typedMap() {
      $this->assertEquals(
        array(new MapNode(array(
          'elements'      => array(
            array(
              new IntegerNode('1'),
              new StringNode('one'),
            ),
            array(
              new IntegerNode('2'),
              new StringNode('two'),
            ),
            array(
              new IntegerNode('3'),
              new StringNode('three'),
            ),
          ),
        'type'          => new TypeName('[:string]'),
        ))), 
        $this->parse('new [:string] { 1 : "one", 2 : "two", 3 : "three" };')
      );
    }

    /**
     * Test a non-empty untyped map
     *
     */
    #[@test]
    public function untypedMapWithDanglingComma() {
      $this->assertEquals(
        array(new MapNode(array(
          'elements'      => array(
            array(
              new IntegerNode('1'),
              new StringNode('one'),
            ),
            array(
              new IntegerNode('2'),
              new StringNode('two'),
            ),
            array(
              new IntegerNode('3'),
              new StringNode('three'),
            ),
          ),
          'type'          => NULL,
        ))), 
        $this->parse('[ 1 : "one", 2 : "two", 3 : "three", ];')
      );
    }
  }
?>
