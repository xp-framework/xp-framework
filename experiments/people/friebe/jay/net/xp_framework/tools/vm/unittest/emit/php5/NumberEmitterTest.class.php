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
  class NumberEmitterTest extends AbstractEmitterTest {

    /**
     * Tests float numbers
     *
     */
    #[@test]
    public function floatNumbers() {
      foreach (array('1.0', '0.0', '0.5', '-5.0', '+5.0') as $declared) {
        $this->assertSourcecodeEquals(
          '$x= '.$declared.';',
          $this->emit('$x= '.$declared.';')
        );
      }
    }

    /**
     * Tests int numbers
     *
     */
    #[@test]
    public function intNumbers() {
      foreach (array('1', '0', '-5', '+5') as $declared) {
        $this->assertSourcecodeEquals(
          '$x= '.$declared.';',
          $this->emit('$x= '.$declared.';')
        );
      }
    }

    /**
     * Tests hex numbers
     *
     */
    #[@test]
    public function hexNumbers() {
      foreach (array('0x1', '0x0', '-0x5', '0xFFFF') as $declared) {
        $this->assertSourcecodeEquals(
          '$x= '.eval('return '.$declared.';').';',
          $this->emit('$x= '.$declared.';')
        );
      }
    }

    /**
     * Tests octal numbers
     *
     */
    #[@test]
    public function octalNumbers() {
      foreach (array('01', '0123') as $declared) {
        $this->assertSourcecodeEquals(
          '$x= '.eval('return '.$declared.';').';',
          $this->emit('$x= '.$declared.';')
        );
      }
    }

    /**
     * Tests octal numbers
     *
     */
    #[@test]
    public function exponentNumbers() {
      foreach (array('1e3', '1.2e3', '-1E4', '-1E+4') as $declared) {
        $this->assertSourcecodeEquals(
          '$x= '.$declared.';',
          $this->emit('$x= '.$declared.';')
        );
      }
    }
  }
?>
