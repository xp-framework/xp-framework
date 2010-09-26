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
  class net·xp_lang·tests·syntax·php·DynamicTest extends net·xp_lang·tests·syntax·php·ParserTestCase {

    /**
     * Test instanceof a variable
     *
     */
    #[@test]
    public function instanceOfVariable() {
      $this->assertEquals(array(new DynamicInstanceOfNode(array(
        'expression'    => new VariableNode('a'),
        'variable'      => 'type'
      ))), $this->parse('$a instanceof $type;'));
    }
  
    /**
     * Test new $type
     *
     */
    #[@test]
    public function instanceCreation() {
      $this->assertEquals(
        array(new DynamicInstanceCreationNode(array(
          'variable'    => 'type',
          'parameters'  => array()
        ))),
        $this->parse('new $type();')
      );
    }

    /**
     * Test $this->$name
     *
     */
    #[@test]
    public function variableMemberAccess() {
      $this->assertEquals(
        array(new DynamicVariableReferenceNode(new VariableNode('this'), new VariableNode('name'))),
        $this->parse('$this->$name;')
      );
    }

    /**
     * Test $this->$name
     *
     */
    #[@test]
    public function expressionMemberAccess() {
      $this->assertEquals(
        array(new DynamicVariableReferenceNode(new VariableNode('this'), new VariableNode('name'))),
        $this->parse('$this->{$name};')
      );
    }
  }
?>
