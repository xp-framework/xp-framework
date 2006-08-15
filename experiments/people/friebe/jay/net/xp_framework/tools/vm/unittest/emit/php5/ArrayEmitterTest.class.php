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
  class ArrayEmitterTest extends AbstractEmitterTest {

    /**
     * Tests array append operator "[]"
     *
     * @access  public
     */
    #[@test]
    function arrayAppendOperator() {
      $this->assertSourcecodeEquals(
        '$a[]= 1;',
        $this->emit('$a[]= 1;')
      );
    }
  }
?>
