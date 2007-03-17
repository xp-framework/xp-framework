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
     */
    #[@test]
    public function unassignedVariableCanBecomeAnything() {
      foreach (array('NULL', '1', '1.0', 'array()', '"Hello"', 'new lang.Object();') as $init) {
        $this->emit('$x= '.$init.';');
      }
    }

    /**
     * Tests an untyped argument
     *
     */
    #[@test]
    public function untypedArgument() {
      $this->emit('class Test { public void test($bar) { $bar= 1; } }');
    }

    /**
     * Tests a typed argument
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function typedArgumentMismatch() {
      $this->emit('class Test { public void test(string $bar) { $bar= 1; } }');
    }

    /**
     * Tests an untyped member
     *
     */
    #[@test]
    public function untypedMember() {
      $this->emit('class Test { private $bar; public void test() { $this->bar= 1; } }');
    }

    /**
     * Tests a typed member
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function typedMemberMismatch() {
      $this->emit('class Test { private string $bar; public void test() { $this->bar= 1; } }');
    }

    /**
     * Tests binary assignment
     *
     */
    #[@test]
    public function binaryAssign() {
      $this->emit('$product= 0; $product+= 3;');
    }

    /**
     * Tests array assignment via []=
     *
     */
    #[@test]
    public function arrayAssign() {
      $this->emit('$args= array(); $args[]= "Hello";');
    } 


    /**
     * Tests array assignment via [X]=
     *
     */
    #[@test]
    public function arrayOffsetAssign() {
      $this->emit('$args= array(); $args[0]= "Hello";');
    } 

    /**
     * Tests array offset return [X]
     *
     */
    #[@test]
    public function arrayOffsetReturn() {
      $this->emit('class Throwable { 
        public lang.StackTraceElement elementAt(int $o) { 
          $a= array();
          return $a[$o]; 
        } 
      }');
    } 

    /**
     * Tests binary assignment
     *
     */
    #[@test]
    public function arrayReturn() {
      $this->emit('class Throwable { public lang.StackTraceElement[] getStackTrace() { return array(); } } ');
    }
 }
?>
