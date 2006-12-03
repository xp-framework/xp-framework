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
      foreach (array('NULL', '1', '1.0', 'array()', '"Hello"', 'new lang.Object();') as $init) {
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

    /**
     * Tests an untyped member
     *
     * @access  public
     */
    #[@test]
    function untypedMember() {
      $this->emit('class Test { private $bar; public void test() { $this->bar= 1; } }');
    }

    /**
     * Tests a typed member
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function typedMemberMismatch() {
      $this->emit('class Test { private string $bar; public void test() { $this->bar= 1; } }');
    }

    /**
     * Tests binary assignment
     *
     * @access  public
     */
    #[@test]
    function binaryAssign() {
      $this->emit('$product= 0; $product+= 3;');
    }
 
     /**
     * Tests binary assignment
     *
     * @access  public
     */
    #[@test]
    function arrayReturn() {
      $this->emit('class Throwable { public lang.StackTraceElement[] getStackTrace() { return array(); } } ');
    }
 }
?>
