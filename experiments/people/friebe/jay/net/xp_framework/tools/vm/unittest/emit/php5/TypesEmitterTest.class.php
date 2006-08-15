<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class TypesEmitterTest extends AbstractEmitterTest {

    /**
     * Tests 
     *
     * @access  public
     */
    #[@test]
    function unassignedVariableCanBecomeAnything() {
      foreach (array('NULL', '1', '1.0', 'array()', '"Hello"', 'new xp~lang~Object();') as $init) {
        $this->emit('$x= '.$init.';');
      }
    }

    /**
     * Tests an untyped argument
     *
     * @access  public
     */
    #[@test]
    function untypedArgument() {
      $this->emit('class Test { public void test($bar) { $bar= 1; } }');
    }

    /**
     * Tests a typed argument
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function typedArgumentMismatch() {
      $this->emit('class Test { public void test(string $bar) { $bar= 1; } }');
    }
  }
?>
