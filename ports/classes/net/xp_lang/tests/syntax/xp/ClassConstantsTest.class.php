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
  class ClassConstantsTest extends ParserTestCase {
  
    /**
     * Parse class source and return statements inside field declaration
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer($src, '<string:'.$this->name.'>'))->declaration->body;
    }

    /**
     * Test string constant
     *
     */
    #[@test]
    public function stringConstant() {
      $this->assertEquals(
        array(new ClassConstantNode('GET', new TypeName('string'), new StringNode('GET'))),
        $this->parse('class HttpMethods { const string GET = "GET"; }')
      );
    }

    /**
     * Test int constant
     *
     */
    #[@test]
    public function intConstant() {
      $this->assertEquals(
        array(new ClassConstantNode('THRESHHOLD', new TypeName('int'), new IntegerNode('5'))),
        $this->parse('class Policy { const int THRESHHOLD = 5; }')
      );
    }

    /**
     * Test var constant
     *
     */
    #[@test]
    public function varConstant() {
      $this->assertEquals(
        array(new ClassConstantNode('EMPTYNESS', new TypeName('var'), new NullNode())),
        $this->parse('class Example { const var EMPTYNESS = null; }')
      );
    }

    /**
     * Test constant cannot be initialized to an object
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function constantsCanOnlyBePrimitives() {
      $this->parse('class Policy { const var THRESHHOLD = new Object(); }');
    }
 
    /**
     * Test constant cannot be initialized to an object
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noArraysAllowed() {
      $this->parse('class Numb3rs { const var[] FIRST_THREE = [1, 2, 3]; }');
    }

    /**
     * Test constant cannot be initialized to an object
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noMapsAllowed() {
      $this->parse('class Numb3rs { const [:var] FIRST_THREE = [1: "One", 2: "Two", 3: "Three"]; }');
    }
  }
?>
