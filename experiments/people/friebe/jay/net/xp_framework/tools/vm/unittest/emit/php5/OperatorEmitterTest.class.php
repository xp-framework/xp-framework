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
     */
    #[@test]
    public function postInc() {
      $this->assertSourcecodeEquals(
        '$i++;',
        $this->emit('$i++;')
      );
    }

    /**
     * Tests post-dec operator (expr--)
     *
     */
    #[@test]
    public function postDec() {
      $this->assertSourcecodeEquals(
        '$i--;',
        $this->emit('$i--;')
      );
    }

    /**
     * Tests pre-inc operator (++expr)
     *
     */
    #[@test]
    public function preInc() {
      $this->assertSourcecodeEquals(
        '++$i;',
        $this->emit('++$i;')
      );
    }

    /**
     * Tests pre-dec operator (--expr)
     *
     */
    #[@test]
    public function preDec() {
      $this->assertSourcecodeEquals(
        '--$i;',
        $this->emit('--$i;')
      );
    }

    /**
     * Tests concat operator (.)
     *
     */
    #[@test]
    public function concat() {
      $this->assertSourcecodeEquals(
        '$a.$b;',
        $this->emit('$a~$b;')
      );
    }

    /**
     * Tests addition operator (+)
     *
     */
    #[@test]
    public function addition() {
      $this->assertSourcecodeEquals(
        '$a+$b;',
        $this->emit('$a + $b;')
      );
    }

    /**
     * Tests addition operator (-)
     *
     */
    #[@test]
    public function subtraction() {
      $this->assertSourcecodeEquals(
        '$a-$b;',
        $this->emit('$a - $b;')
      );
    }

    /**
     * Tests multiplication operator (*)
     *
     */
    #[@test]
    public function multiplication() {
      $this->assertSourcecodeEquals(
        '$a*$b;',
        $this->emit('$a * $b;')
      );
    }

    /**
     * Tests division operator (/)
     *
     */
    #[@test]
    public function division() {
      $this->assertSourcecodeEquals(
        '$a/$b;',
        $this->emit('$a / $b;')
      );
    }

    /**
     * Tests modulo operator (%)
     *
     */
    #[@test]
    public function modulo() {
      $this->assertSourcecodeEquals(
        '$a%$b;',
        $this->emit('$a % $b;')
      );
    }

    /**
     * Tests concat operator (.)
     *
     */
    #[@test]
    public function concatAssign() {
      $this->assertSourcecodeEquals(
        '$a.= $b;',
        $this->emit('$a~= $b;')
      );
    }

    /**
     * Tests addition operator (+)
     *
     */
    #[@test]
    public function additionAssign() {
      $this->assertSourcecodeEquals(
        '$a+= $b;',
        $this->emit('$a+= $b;')
      );
    }

    /**
     * Tests addition operator (-)
     *
     */
    #[@test]
    public function subtractionAssign() {
      $this->assertSourcecodeEquals(
        '$a-= $b;',
        $this->emit('$a-= $b;')
      );
    }

    /**
     * Tests multiplication operator (*)
     *
     */
    #[@test]
    public function multiplicationAssign() {
      $this->assertSourcecodeEquals(
        '$a*= $b;',
        $this->emit('$a*= $b;')
      );
    }

    /**
     * Tests division operator (/)
     *
     */
    #[@test]
    public function divisionAssign() {
      $this->assertSourcecodeEquals(
        '$a/= $b;',
        $this->emit('$a/= $b;')
      );
    }

    /**
     * Tests modulo operator (%)
     *
     */
    #[@test]
    public function moduloAssign() {
      $this->assertSourcecodeEquals(
        '$a%= $b;',
        $this->emit('$a%= $b;')
      );
    }

    /**
     * Tests equality operator
     *
     */
    #[@test]
    public function equality() {
      $this->assertSourcecodeEquals(
        '$a==$b;',
        $this->emit('$a == $b;')
      );
    }

    /**
     * Tests identity operator
     *
     */
    #[@test]
    public function identity() {
      $this->assertSourcecodeEquals(
        '$a===$b;',
        $this->emit('$a === $b;')
      );
    }

    /**
     * Tests greater-than operator
     *
     */
    #[@test]
    public function greaterThan() {
      $this->assertSourcecodeEquals(
        '$a>=$b;',
        $this->emit('$a >= $b;')
      );
    }

    /**
     * Tests greater operator
     *
     */
    #[@test]
    public function greater() {
      $this->assertSourcecodeEquals(
        '$a>$b;',
        $this->emit('$a > $b;')
      );
    }

    /**
     * Tests less-than operator
     *
     */
    #[@test]
    public function lessThan() {
      $this->assertSourcecodeEquals(
        '$a<=$b;',
        $this->emit('$a <= $b;')
      );
    }

    /**
     * Tests less operator
     *
     */
    #[@test]
    public function less() {
      $this->assertSourcecodeEquals(
        '$a<$b;',
        $this->emit('$a < $b;')
      );
    }

    /**
     * Tests greater-than operator in conjunction with a pre-dec operator
     *
     */
    #[@test]
    public function greaterThanWithPreDec() {
      $this->assertSourcecodeEquals(
        '--$year>=0;',
        $this->emit('--$year >= 0;')
      );
    }
  }
?>
