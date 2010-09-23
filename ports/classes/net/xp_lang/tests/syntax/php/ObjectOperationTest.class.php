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
  class net·xp_lang·tests·syntax·php·ObjectOperationTest extends net·xp_lang·tests·syntax·php·ParserTestCase {

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
