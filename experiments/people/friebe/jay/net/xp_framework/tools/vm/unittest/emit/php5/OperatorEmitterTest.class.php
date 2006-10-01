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
  class OperatorEmitterTest extends AbstractEmitterTest {

    /**
     * Tests post-inc operator (expr++)
     *
     * @access  public
     */
    #[@test]
    function postInc() {
      $this->assertSourcecodeEquals(
        '$i++;',
        $this->emit('$i++;')
      );
    }

    /**
     * Tests post-dec operator (expr--)
     *
     * @access  public
     */
    #[@test]
    function postDec() {
      $this->assertSourcecodeEquals(
        '$i--;',
        $this->emit('$i--;')
      );
    }

    /**
     * Tests pre-inc operator (++expr)
     *
     * @access  public
     */
    #[@test]
    function preInc() {
      $this->assertSourcecodeEquals(
        '++$i;',
        $this->emit('++$i;')
      );
    }

    /**
     * Tests pre-dec operator (--expr)
     *
     * @access  public
     */
    #[@test]
    function preDec() {
      $this->assertSourcecodeEquals(
        '--$i;',
        $this->emit('--$i;')
      );
    }
  }
?>
