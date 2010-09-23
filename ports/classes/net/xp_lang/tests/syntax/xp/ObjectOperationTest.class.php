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
  class ObjectOperationTest extends ParserTestCase {

    /**
     * Test new
     *
     */
    #[@test]
    public function instanceCreation() {
      $this->assertEquals(
        array(new InstanceCreationNode(array(
          'type'       => new TypeName('XPClass'),
          'parameters' => NULL
        ))),
        $this->parse('new XPClass();')
      );
    }

    /**
     * Test new
     *
     */
    #[@test]
    public function anonymousInstanceCreation() {
      $this->assertEquals(
        array(new InstanceCreationNode(array(
          'type'       => new TypeName('Runnable'),
          'parameters' => NULL,
          'body'       => array(
            new MethodNode(array(
              'modifiers'   => MODIFIER_PUBLIC,
              'annotations' => NULL,
              'name'        => 'run',
              'returns'     => TypeName::$VOID,
              'parameters'  => NULL,
              'throws'      => NULL,
              'body'        => array(),
              'comment'     => NULL,
              'extension'   => NULL,
            ))
          )
        ))),
        $this->parse('new Runnable() {
          public void run() {
            // TBI
          }
        };')
      );
    }
  
    /**
     * Test clone
     *
     */
    #[@test]
    public function cloningOperation() {
      $this->assertEquals(
        array(new CloneNode(new VariableNode('b'))),
        $this->parse('clone $b;')
      );
    }

    /**
     * Test instanceof
     *
     */
    #[@test]
    public function instanceOfTest() {
      $this->assertEquals(
        array(new InstanceOfNode(array(
          'expression' => new VariableNode('b'), 
          'type'       => new TypeName('XPClass')
        ))),
        $this->parse('$b instanceof XPClass;')
      );
    }

    /**
     * Test "new Object;" is not syntactically legal, this works in PHP
     * but is bascially the same as "new Object();"
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function newWithoutBraces() {
      $this->parse('new Object;');
    }
  }
?>
