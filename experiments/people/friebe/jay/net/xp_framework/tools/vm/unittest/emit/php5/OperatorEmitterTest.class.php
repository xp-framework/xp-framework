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

    /**
     * Tests equality operator
     *
     * @access  public
     */
    #[@test]
    function equality() {
      $this->assertSourcecodeEquals(
        '$a==$b;',
        $this->emit('$a == $b;')
      );
    }

    /**
     * Tests identity operator
     *
     * @access  public
     */
    #[@test]
    function identity() {
      $this->assertSourcecodeEquals(
        '$a===$b;',
        $this->emit('$a === $b;')
      );
    }

    /**
     * Tests greater-than operator
     *
     * @access  public
     */
    #[@test]
    function greaterThan() {
      $this->assertSourcecodeEquals(
        '$a>=$b;',
        $this->emit('$a >= $b;')
      );
    }

    /**
     * Tests greater operator
     *
     * @access  public
     */
    #[@test]
    function greater() {
      $this->assertSourcecodeEquals(
        '$a>$b;',
        $this->emit('$a > $b;')
      );
    }

    /**
     * Tests less-than operator
     *
     * @access  public
     */
    #[@test]
    function lessThan() {
      $this->assertSourcecodeEquals(
        '$a<=$b;',
        $this->emit('$a <= $b;')
      );
    }

    /**
     * Tests less operator
     *
     * @access  public
     */
    #[@test]
    function less() {
      $this->assertSourcecodeEquals(
        '$a<$b;',
        $this->emit('$a < $b;')
      );
    }

    /**
     * Tests greater-than operator in conjunction with a pre-dec operator
     *
     * @access  public
     */
    #[@test]
    function greaterThanWithPreDec() {
      $this->assertSourcecodeEquals(
        '--$year>=0;',
        $this->emit('--$year >= 0;')
      );
    }
  }
?>
