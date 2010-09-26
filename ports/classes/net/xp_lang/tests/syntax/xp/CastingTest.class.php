<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  $package= 'net.xp_lang.tests.syntax.xp';

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·xp·CastingTest extends ParserTestCase {

    /**
     * Test prefix notation
     *
     */
    #[@test, @ignore('Prefix casting unsupported')]
    public function prefixIntCast() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('int'),
            'expression'  => new VariableNode('b')
          )),
          'op'          => '='
        ))),
        $this->parse('$a= (int)$b;')
      );
    }

    /**
     * Test prefix notation
     *
     */
    #[@test, @ignore('Prefix casting unsupported')]
    public function prefixIntCastBracketedLiteral() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('int'),
            'expression'  => new BooleanNode(TRUE)
          )),
          'op'          => '='
        ))),
        $this->parse('$a= (int)(true);')
      );
    }

    /**
     * Test prefix notation
     *
     */
    #[@test, @ignore('Prefix casting unsupported')]
    public function prefixIntArrayCast() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('int[]'),
            'expression'  => new VariableNode('b')
          )),
          'op'          => '='
        ))),
        $this->parse('$a= (int[])$b;')
      );
    }

    /**
     * Test prefix notation
     *
     */
    #[@test, @ignore('Prefix casting unsupported')]
    public function prefixGenericCast() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('List', array(new TypeName('String'))),
            'expression'  => new VariableNode('b')
          )),
          'op'          => '='
        ))),
        $this->parse('$a= (List<String>)$b;')
      );
    }

    /**
     * Test prefix notation
     *
     */
    #[@test, @ignore('Prefix casting unsupported')]
    public function prefixQualifiedCast() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('com.example.bank.Account'),
            'expression'  => new VariableNode('b')
          )),
          'op'          => '='
        ))),
        $this->parse('$a= (com.example.bank.Account)$b;')
      );
    }

    /**
     * Test "as" notation
     *
     */
    #[@test]
    public function postfixQualifiedCast() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('com.example.bank.Account'),
            'check'       => TRUE,
            'expression'  => new VariableNode('b')
          )),
          'op'          => '='
        ))),
        $this->parse('$a= $b as com.example.bank.Account;')
      );
    }

    /**
     * Test "as" notation
     *
     */
    #[@test]
    public function postfixQualifiedNonEnforcedCast() {
      $this->assertEquals(
        array(new AssignmentNode(array(
          'variable'    => new VariableNode('a'),
          'expression'  => new CastNode(array(
            'type'        => new TypeName('com.example.bank.Account'),
            'check'       => FALSE,
            'expression'  => new VariableNode('b')
          )),
          'op'          => '='
        ))),
        $this->parse('$a= $b as com.example.bank.Account?;')
      );
    }
  }
?>
