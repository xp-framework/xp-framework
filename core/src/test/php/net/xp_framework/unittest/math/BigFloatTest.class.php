<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'math.BigFloat',
    'math.BigInt'
  );

  /**
   * TestCase
   *
   * @see     xp://math.BigFloat
   */
  class BigFloatTest extends TestCase {

    /**
     * Test creating a float from an int
     *
     */
    #[@test]
    public function floatFromInt() {
      $this->assertEquals(new BigFloat(2.0), new BigFloat(new BigInt(2)));
    }
    
    /**
     * Test string casting
     *
     */
    #[@test]
    public function castableToString() {
      $this->assertEquals('6100', (string)new BigFloat(6100.0));
    }

    /**
     * Test string casting
     *
     */
    #[@test]
    public function castableToStringNegative() {
      $this->assertEquals('-6100', (string)new BigFloat(-6100.0));
    }

    /**
     * Test string casting
     *
     */
    #[@test]
    public function castableToStringHalf() {
      $this->assertEquals('0.5', (string)new BigFloat(0.5));
    }
  
    /**
     * Test intValue()
     *
     */
    #[@test]
    public function intValue() {
      $this->assertEquals(6100, create(new BigFloat(6100.0))->intValue());
    }

    /**
     * Test intValue()
     *
     */
    #[@test]
    public function intValueNegative() {
      $this->assertEquals(-6100, create(new BigFloat(-6100.0))->intValue());
    }

    /**
     * Test doubleValue()
     *
     */
    #[@test]
    public function doubleValue() {
      $this->assertEquals(6100.0, create(new BigFloat(6100.0))->doubleValue());
    }

    /**
     * Test doubleValue()
     *
     */
    #[@test]
    public function doubleValueNegative() {
      $this->assertEquals(-6100.0, create(new BigFloat(-6100.0))->doubleValue());
    }
  
    /**
     * Test +
     *
     */
    #[@test]
    public function addition() {
      $this->assertEquals(new BigFloat(2.0), create(new BigFloat(1.0))->add(new BigFloat(1.0)));
    }

    /**
     * Test +
     *
     */
    #[@test]
    public function additionOneNegative() {
      $this->assertEquals(new BigFloat(0.0), create(new BigFloat(-1.0))->add(new BigFloat(1.0)));
    }

    /**
     * Test +
     *
     */
    #[@test]
    public function additionBothNegative() {
      $this->assertEquals(new BigFloat(-2), create(new BigFloat(-1.0))->add(new BigFloat(-1.0)));
    }
 
    /**
     * Test +
     *
     */
    #[@test]
    public function additionLarge() {
      $a= new BigFloat('3648686172031547129462783484965308369824430041997653001183827180347.1');
      $b= new BigFloat('1067825251034421530837885294271156039110655362253362224471523.9');
      $r= new BigFloat('3648687239856798163884314322850602640980469152653015254546051651871');
      $this->assertEquals($r, $a->add($b));
    }

    /**
     * Test +
     *
     */
    #[@test]
    public function additionWithPrimitive() {
      $this->assertEquals(new BigFloat(6100.0), create(new BigFloat(1.0))->add(6099.0));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtraction() {
      $this->assertEquals(new BigFloat(0.0), create(new BigFloat(1.0))->subtract(new BigFloat(1.0)));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionOneNegative() {
      $this->assertEquals(new BigFloat(-2.0), create(new BigFloat(-1.0))->subtract(new BigFloat(1.0)));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionBothNegative() {
      $this->assertEquals(new BigFloat(0.0), create(new BigFloat(-1.0))->subtract(new BigFloat(-1.0)));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionLarge() {
      $a= new BigFloat('3648687239856798163884314322850602640980469152653015254546051651871');
      $b= new BigFloat('1067825251034421530837885294271156039110655362253362224471523.9');
      $r= new BigFloat('3648686172031547129462783484965308369824430041997653001183827180347.1');
      $this->assertEquals($r, $a->subtract($b));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionWithPrimitive() {
      $this->assertEquals(new BigFloat(-6100.0), create(new BigFloat(-1.0))->subtract(6099.0));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplication() {
      $this->assertEquals(new BigFloat(1.0), create(new BigFloat(1.0))->multiply(new BigFloat(1.0)));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationOneNegative() {
      $this->assertEquals(new BigFloat(-1.0), create(new BigFloat(-1.0))->multiply(new BigFloat(1.0)));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationBothNegative() {
      $this->assertEquals(new BigFloat(1.0), create(new BigFloat(-1.0))->multiply(new BigFloat(-1.0)));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationLarge() {
      $a= new BigFloat('36486872398567981638843143228254546051651870.2');
      $b= new BigFloat('50602640980469152653015.1');
      $r= new BigFloat('1846332104484924953979623193074019910923757259978350589582121583840.02');
      $this->assertEquals($r, $a->multiply($b));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationWithPrimitive() {
      $this->assertEquals(new BigFloat(6100.0), create(new BigFloat(-1.0))->multiply(-6100.0));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function division() {
      $this->assertEquals(new BigFloat(2.0), create(new BigFloat(4.0))->divide(new BigFloat(2.0)));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionOneNegative() {
      $this->assertEquals(new BigFloat(-2.0), create(new BigFloat(-4.0))->divide(new BigFloat(2.0)));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionBothNegative() {
      $this->assertEquals(new BigFloat(2.0), create(new BigFloat(-4.0))->divide(new BigFloat(-2.0)));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionLarge() {
      $a= new BigFloat('1846332104484924953979619544386780054125593365543499568033685888050.0');
      $b= new BigFloat('36486872398567981638843143228254546051651870.0');
      $r= new BigFloat('50602640980469152653015.0');
      $this->assertEquals($r, $a->divide($b));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionWithPrimitive() {
      $this->assertEquals(new BigFloat(6100.0), create(new BigFloat(37210000.0))->divide(6100.0));
    }

    /**
     * Test /
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function divisionByZero() {
      create(new BigFloat(5.0))->divide(new BigFloat(0.0));
    }

    /**
     * Test **
     *
     */
    #[@test]
    public function power() {
      $this->assertEquals(new BigFloat(16.0), create(new BigFloat(2.0))->power(new BigFloat(4.0)));
    }

    /**
     * Test ** -1
     *
     */
    #[@test]
    public function powerNegativeOne() {
      $this->assertEquals(new BigFloat('0.5'), create(new BigFloat(2.0))->power(new BigFloat(-1.0)));
    }

    /**
     * Test 0 ** 2
     *
     */
    #[@test]
    public function powerOfZero() {
      $this->assertEquals(new BigFloat(0.0), create(new BigFloat(0.0))->power(new BigFloat(2.0)));
    }

    /**
     * Test 0 ** 0
     *
     */
    #[@test]
    public function powerOfZeroZero() {
      $this->assertEquals(new BigFloat(1.0), create(new BigFloat(0.0))->power(new BigFloat(0.0)));
    }

    /**
     * Test 0 ** -2
     *
     */
    #[@test]
    public function powerOfZeroNegative() {
      $this->assertEquals(new BigFloat(0.0), create(new BigFloat(0.0))->power(new BigFloat(-2)));
    }

    /**
     * Test **
     *
     */
    #[@test]
    public function powerOfNegativeNumberEven() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(-2.0))->power(new BigFloat(2.0)));
    }

    /**
     * Test **
     *
     */
    #[@test]
    public function powerOfNegativeNumberOdd() {
      $this->assertEquals(new BigFloat(-8.0), create(new BigFloat(-2.0))->power(new BigFloat(3.0)));
    }

    /**
     * Test ** 1
     *
     */
    #[@test]
    public function powerOne() {
      $this->assertEquals(new BigFloat(2.0), create(new BigFloat(2.0))->power(new BigFloat(1.0)));
    }

    /**
     * Test ** 0
     *
     */
    #[@test]
    public function powerZero() {
      $this->assertEquals(new BigFloat(1.0), create(new BigFloat(2.0))->power(new BigFloat(0.0)));
    }

    /**
     * Test 2 ** 2
     *
     */
    #[@test]
    public function powerWithPrimitive() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(2.0))->power(2.0));
    }

    /**
     * Test ceil()
     *
     */
    #[@test]
    public function ceil() {
      $this->assertEquals(new BigFloat(5.0), create(new BigFloat(4.5))->ceil());
    }

    /**
     * Test ceil()
     *
     */
    #[@test]
    public function ceilInt() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(4.0))->ceil());
    }

    /**
     * Test ceil()
     *
     */
    #[@test]
    public function ceilClose() {
      $this->assertEquals(new BigFloat(10.0), create(new BigFloat(9.999))->ceil());
    }

    /**
     * Test ceil()
     *
     */
    #[@test]
    public function ceilNegative() {
      $this->assertEquals(new BigFloat(-4.0), create(new BigFloat(-4.5))->ceil());
    }

    /**
     * Test floor()
     *
     */
    #[@test]
    public function floor() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(4.5))->floor());
    }

    /**
     * Test floor()
     *
     */
    #[@test]
    public function floorInt() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(4.0))->floor());
    }

    /**
     * Test floor()
     *
     */
    #[@test]
    public function floorClose() {
      $this->assertEquals(new BigFloat(9.0), create(new BigFloat(9.999))->floor());
    }

    /**
     * Test floor()
     *
     */
    #[@test]
    public function floorNegative() {
      $this->assertEquals(new BigFloat(-5.0), create(new BigFloat(-4.5))->floor());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundWhole() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(4.0))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function round() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(4.4))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundNegative() {
      $this->assertEquals(new BigFloat(-4.0), create(new BigFloat(-4.4))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundHalf() {
      $this->assertEquals(new BigFloat(5.0), create(new BigFloat(4.5))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundNegativeHalf() {
      $this->assertEquals(new BigFloat(-5.0), create(new BigFloat(-4.5))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundAlmostHalf() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat(4.499999))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundNegativeAlmostHalf() {
      $this->assertEquals(new BigFloat(-4.0), create(new BigFloat(-4.499999))->round());
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundEuroToDeutscheMarkExchangeRateToTwoDigits() {
      $this->assertEquals(new BigFloat(1.96), create(new BigFloat(1.95583))->round(2));
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundSample1() {
      $this->assertEquals(new BigFloat(323.35), create(new BigFloat(323.346))->round(2));
    }

    /**
     * Test round()
     *
     */
    #[@test]
    public function roundSample2() {
      $this->assertEquals(new BigFloat(323.01), create(new BigFloat(323.006))->round(2));
    }

    /**
     * Test ceil()
     *
     */
    #[@test]
    public function ceilLotsOfZeros() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat('4.00000000000000000'))->ceil());
    }

    /**
     * Test floor()
     *
     */
    #[@test]
    public function floorLotsOfZeros() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat('4.00000000000000000'))->floor());
    }

    /**
     * Test floor()
     *
     */
    #[@test]
    public function roundLotsOfZeros() {
      $this->assertEquals(new BigFloat(4.0), create(new BigFloat('4.00000000000000000'))->round());
    }
  }
?>
