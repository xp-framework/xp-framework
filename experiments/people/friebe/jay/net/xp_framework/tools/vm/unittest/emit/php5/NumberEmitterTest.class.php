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
     * @access  public
     */
    #[@test]
    function floatNumbers() {
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
     * @access  public
     */
    #[@test]
    function intNumbers() {
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
     * @access  public
     */
    #[@test]
    function hexNumbers() {
      foreach (array('0x1', '0x0', '-0x5') as $declared) {
        $this->assertSourcecodeEquals(
          '$x= '.eval('return '.$declared.';').';',
          $this->emit('$x= '.$declared.';')
        );
      }
    }
  }
?>
