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
     * Tests concat operator (.)
     *
     * @access  public
     */
    #[@test]
    function concat() {
      $this->assertSourcecodeEquals(
        '$a.$b;',
        $this->emit('$a~$b;')
      );
    }

    /**
     * Tests addition operator (+)
     *
     * @access  public
     */
    #[@test]
    function addition() {
      $this->assertSourcecodeEquals(
        '$a+$b;',
        $this->emit('$a + $b;')
      );
    }

    /**
     * Tests addition operator (-)
     *
     * @access  public
     */
    #[@test]
    function subtraction() {
      $this->assertSourcecodeEquals(
        '$a-$b;',
        $this->emit('$a - $b;')
      );
    }

    /**
     * Tests multiplication operator (*)
     *
     * @access  public
     */
    #[@test]
    function multiplication() {
      $this->assertSourcecodeEquals(
        '$a*$b;',
        $this->emit('$a * $b;')
      );
    }

    /**
     * Tests division operator (/)
     *
     * @access  public
     */
    #[@test]
    function division() {
      $this->assertSourcecodeEquals(
        '$a/$b;',
        $this->emit('$a / $b;')
      );
    }

    /**
     * Tests modulo operator (%)
     *
     * @access  public
     */
    #[@test]
    function modulo() {
      $this->assertSourcecodeEquals(
        '$a%$b;',
        $this->emit('$a % $b;')
      );
    }

    /**
     * Tests concat operator (.)
     *
     * @access  public
     */
    #[@test]
    function concatAssign() {
      $this->assertSourcecodeEquals(
        '$a.= $b;',
        $this->emit('$a~= $b;')
      );
    }

    /**
     * Tests addition operator (+)
     *
     * @access  public
     */
    #[@test]
    function additionAssign() {
      $this->assertSourcecodeEquals(
        '$a+= $b;',
        $this->emit('$a+= $b;')
      );
    }

    /**
     * Tests addition operator (-)
     *
     * @access  public
     */
    #[@test]
    function subtractionAssign() {
      $this->assertSourcecodeEquals(
        '$a-= $b;',
        $this->emit('$a-= $b;')
      );
    }

    /**
     * Tests multiplication operator (*)
     *
     * @access  public
     */
    #[@test]
    function multiplicationAssign() {
      $this->assertSourcecodeEquals(
        '$a*= $b;',
        $this->emit('$a*= $b;')
      );
    }

    /**
     * Tests division operator (/)
     *
     * @access  public
     */
    #[@test]
    function divisionAssign() {
      $this->assertSourcecodeEquals(
        '$a/= $b;',
        $this->emit('$a/= $b;')
      );
    }

    /**
     * Tests modulo operator (%)
     *
     * @access  public
     */
    #[@test]
    function moduloAssign() {
      $this->assertSourcecodeEquals(
        '$a%= $b;',
        $this->emit('$a%= $b;')
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
