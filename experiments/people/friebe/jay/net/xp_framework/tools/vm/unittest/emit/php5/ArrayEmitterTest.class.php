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
     * Tests array access
     *
     * @access  public
     */
    #[@test]
    function arrayAccess() {
      $this->assertSourcecodeEquals(
        '$a[$key]++;',
        $this->emit('$a[$key]++;')
      );
    }

    /**
     * Tests array access
     *
     * @access  public
     */
    #[@test]
    function nestedArrayAccess() {
      $this->assertSourcecodeEquals(
        '$a[$key][$subkey]++;',
        $this->emit('$a[$key][$subkey]++;')
      );
    }

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

    /**
     * Tests array append operator "[]"
     *
     * @access  public
     */
    #[@test]
    function arrayAppendOperatorAfterArrayOffset() {
      $this->assertSourcecodeEquals(
        '$a[$key][]= 1;',
        $this->emit('$a[$key][]= 1;')
      );
    }
  }
?>
