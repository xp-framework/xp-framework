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
  class net·xp_lang·tests·syntax·php·ClassConstantsTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Parse class net·xp_lang·tests·syntax·php·source and return statements inside field declaration
     *
     * @param   string src
     * @return  xp.compiler.Node[]
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·php·Parser())->parse(new xp·compiler·syntax·php·Lexer('<?php '.$src.' ?>', '<string:'.$this->name.'>'))->declaration->body;
    }

    /**
     * Test string constant
     *
     */
    #[@test]
    public function stringConstant() {
      $this->assertEquals(
        array(new ClassConstantNode('GET', new TypeName('var'), new StringNode('GET'))),
        $this->parse('class HttpMethods { const GET = "GET"; }')
      );
    }

    /**
     * Test int constant
     *
     */
    #[@test]
    public function intConstant() {
      $this->assertEquals(
        array(new ClassConstantNode('THRESHHOLD', new TypeName('var'), new IntegerNode('5'))),
        $this->parse('class Policy { const THRESHHOLD = 5; }')
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
        $this->parse('class Example { const EMPTYNESS = null; }')
      );
    }

    /**
     * Test constant cannot be initialized to an object
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function constantsCanOnlyBePrimitives() {
      $this->parse('class Policy { const THRESHHOLD = new Object(); }');
    }
 
    /**
     * Test constant cannot be initialized to an object
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noArraysAllowed() {
      $this->parse('class Numb3rs { const FIRST_THREE = array(1, 2, 3); }');
    }

    /**
     * Test constant cannot be initialized to an object
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noMapsAllowed() {
      $this->parse('class Numb3rs { const FIRST_THREE = array(1 => "One", 2 => "Two", 3 => "Three"); }');
    }
  }
?>
