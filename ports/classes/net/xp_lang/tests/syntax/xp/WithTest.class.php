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
  class WithTest extends ParserTestCase {

    /**
     * Test statement with one assignment
     *
     */
    #[@test]
    public function oneAssignment() {
      $this->assertEquals(
        array(new WithNode(
          array(new AssignmentNode(array(
            'variable'   => new VariableNode('o'),
            'op'         => '=',
            'expression' => new InstanceCreationNode(array(
              'type'       => new TypeName('Object'),
              'parameters' => array(),
            ))
          ))),
          array()
        )), 
        $this->parse('with ($o= new Object()) { }')
      );
    }

    /**
     * Test statement with two assignments
     *
     */
    #[@test]
    public function twoAssignments() {
      $this->assertEquals(
        array(new WithNode(
          array(new AssignmentNode(array(
            'variable'   => new VariableNode('o1'),
            'op'         => '=',
            'expression' => new InstanceCreationNode(array(
              'type'       => new TypeName('Object'),
              'parameters' => array(),
            ))
          )), new AssignmentNode(array(
            'variable'   => new VariableNode('o2'),
            'op'         => '=',
            'expression' => new InstanceCreationNode(array(
              'type'       => new TypeName('Object'),
              'parameters' => array(),
            ))
          ))),
          array()
        )), 
        $this->parse('with ($o1= new Object(), $o2= new Object()) { }')
      );
    }

    /**
     * Test statement with block statements
     *
     */
    #[@test]
    public function statements() {
      $this->assertEquals(
        array(new WithNode(
          array(new AssignmentNode(array(
            'variable'   => new VariableNode('o'),
            'op'         => '=',
            'expression' => new InstanceCreationNode(array(
              'type'       => new TypeName('Object'),
              'parameters' => array(),
            ))
          ))),
          array(new AssignmentNode(array(
            'variable'   => new VariableNode('s'),
            'op'         => '=',
            'expression' => new MethodCallNode(new VariableNode('o'), 'toString', NULL)
          )))
        )), 
        $this->parse('with ($o= new Object()) { $s= $o.toString(); }')
      );
    }
  }
?>
