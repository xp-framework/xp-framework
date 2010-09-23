<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·LiteralTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test double-quoted string
     *
     */
    #[@test]
    public function doubleQuotedStringLiteral() {
      $this->assertEquals(array(new StringNode('Hello World')), $this->parse('"Hello World";'));
    }

    /**
     * Test single-quoted string
     *
     */
    #[@test]
    public function singleQuotedStringLiteral() {
      $this->assertEquals(array(new StringNode('Hello World')), $this->parse("'Hello World';"));
    }

    /**
     * Test empty strings
     *
     */
    #[@test]
    public function emptyStrings() {
      $this->assertEquals(array(
        new StringNode(''),
        new StringNode(''),
      ), $this->parse('""; \'\';'));
    }

    /**
     * Test double-quoted string
     *
     */
    #[@test]
    public function doubleQuotedStringWithEscapes() {
      $this->assertEquals(
        array(new StringNode('"Hello", he said')),
        $this->parse('"\"Hello\", he said";')
      );
    }
    /**
     * Test single-quoted string
     *
     */
    #[@test]
    public function singleQuotedStringWithEscapes() {
      $this->assertEquals(
        array(new StringNode("Timm's e-mail address")),
        $this->parse("'Timm\'s e-mail address';")
      );
    }

    /**
     * Test single-quoted string
     *
     */
    #[@test]
    public function multiLineString() {
      $this->assertEquals(array(new StringNode('This
         is 
         a
         multiline
         string'
      )), $this->parse("
        'This
         is 
         a
         multiline
         string';
      "));
    }

    /**
     * Test number
     *
     */
    #[@test]
    public function numberLiteral() {
      $this->assertEquals(array(new IntegerNode('1')), $this->parse('1;'));
    }

    /**
     * Test negative number
     *
     */
    #[@test]
    public function negativeInt() {
      $this->assertEquals(array(new UnaryOpNode(array(
        'expression'    => new IntegerNode('1'),
        'op'            => '-'
      ))), $this->parse("
        -1;
      "));
    }

    /**
     * Test negative number
     *
     */
    #[@test]
    public function negativeDecimal() {
      $this->assertEquals(array(new UnaryOpNode(array(
        'expression'    => new DecimalNode('1.0'),
        'op'            => '-'
      ))), $this->parse("
        -1.0;
      "));
    }

    /**
     * Test hex
     *
     */
    #[@test]
    public function hexLiteral() {
      $this->assertEquals(array(new HexNode('0x0')), $this->parse('0x0;'));
    }

    /**
     * Test decimal
     *
     */
    #[@test]
    public function decimalLiteral() {
      $this->assertEquals(array(new DecimalNode('1.0')), $this->parse('1.0;'));
    }

    /**
     * Test true
     *
     */
    #[@test]
    public function booleanTrueLiteral() {
      $this->assertEquals(
        array(new BooleanNode(TRUE)),
        $this->parse('true;')
      );
    }

    /**
     * Test true
     *
     */
    #[@test, @ignore('Recognized as cast')]
    public function booleanTrueLiteralInBraces() {
      $this->assertEquals(
        array(new BracedExpressionNode(new BooleanNode(TRUE))),
        $this->parse('(true);')
      );
    }

    /**
     * Test true
     *
     */
    #[@test]
    public function booleanFalseLiteral() {
      $this->assertEquals(
        array(new BooleanNode(FALSE)),
        $this->parse('false;')
      );
    }

    /**
     * Test null
     *
     */
    #[@test]
    public function nullLiteral() {
      $this->assertEquals(
        array(new NullNode()),
        $this->parse('null;')
      );
    }

    /**
     * Test array
     *
     */
    #[@test]
    public function arrayLiteral() {
      $this->assertEquals(array(new ArrayNode(array(
        'values'        => array(
          new IntegerNode('1'),
          new IntegerNode('2'),
        ),
        'type'          => NULL
      ))), $this->parse("
        array(1, 2);
      "));
    }

    /**
     * Test map
     *
     */
    #[@test]
    public function mapLiteral() {
      $this->assertEquals(array(new MapNode(array(
        'elements'      => array(array(
          new StringNode('one'),
          new IntegerNode('1')
        )),
        'type'          => NULL
      ))), $this->parse("
        array('one' => 1);
      "));
    }
  }
?>
