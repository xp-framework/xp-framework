<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.Money'
  );

  /**
   * TestCase
   *
   * @see      xp://util.Money
   */
  class MoneyTest extends TestCase {
  
    /**
     * Test amount() method
     *
     */
    #[@test]
    public function tenUsDollarsFromInt() {
      $this->assertEquals(
        new Double(10.00), 
        create(new Money(10, Currency::$USD))->amount()
      );
    }

    /**
     * Test amount() method
     *
     */
    #[@test]
    public function tenUsDollarsFromFloat() {
      $this->assertEquals(
        new Double(10.00), 
        create(new Money(10.00, Currency::$USD))->amount()
      );
    }

    /**
     * Test amount() method
     *
     */
    #[@test]
    public function tenUsDollarsFromString() {
      $this->assertEquals(
        new Double(10.00), 
        create(new Money('10.00', Currency::$USD))->amount()
      );
    }

    /**
     * Test currency() method
     *
     */
    #[@test]
    public function currency() {
      $this->assertEquals(Currency::$USD, create(new Money('1.00', Currency::$USD))->currency());
    }

    /**
     * Test toString() method
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->assertEquals(
        '19.99 USD', 
        create(new Money('19.99', Currency::$USD))->toString()
      );
    }

    /**
     * Test add() method
     *
     */
    #[@test]
    public function add() {
      $this->assertEquals(
        new Money('20.00', Currency::$EUR),
        create(new Money('11.50', Currency::$EUR))->add(new Money('8.50', Currency::$EUR))
      );
    }

    /**
     * Test add() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotAddDifferentCurrencies() {
      create(new Money('11.50', Currency::$EUR))->add(new Money('8.50', Currency::$USD));
    }

    /**
     * Test subtract() method
     *
     */
    #[@test]
    public function subtract() {
      $this->assertEquals(
        new Money('3.00', Currency::$EUR),
        create(new Money('11.50', Currency::$EUR))->subtract(new Money('8.50', Currency::$EUR))
      );
    }

    /**
     * Test subtract() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotSubtractDifferentCurrencies() {
      create(new Money('11.50', Currency::$EUR))->subtract(new Money('8.50', Currency::$USD));
    }

    /**
     * Test multiplyBy() method
     *
     */
    #[@test]
    public function multiplyBy() {
      $this->assertEquals(
        new Money('2.98', Currency::$EUR),
        create(new Money('1.49', Currency::$EUR))->multiplyBy(2)
      );
    }

    /**
     * Test divideBy() method
     *
     */
    #[@test]
    public function divideBy() {
      $this->assertEquals(
        new Money('9.99', Currency::$EUR),
        create(new Money('19.98', Currency::$EUR))->divideBy(2)
      );
    }

    /**
     * Test compareTo() method
     *
     */
    #[@test]
    public function compareToReturnsZeroOnEquality() {
      $this->assertEquals(
        0,
        create(new Money('1.01', Currency::$EUR))->compareTo(new Money('1.01', Currency::$EUR))
      );
    }

    /**
     * Test compareTo() method
     *
     */
    #[@test]
    public function compareToReturnsNegativeOneIfArgumentIsLess() {
      $this->assertEquals(
        -1,
        create(new Money('1.01', Currency::$EUR))->compareTo(new Money('0.99', Currency::$EUR))
      );
    }

    /**
     * Test compareTo() method
     *
     */
    #[@test]
    public function compareToReturnsOneIfArgumentIsMore() {
      $this->assertEquals(
        1,
        create(new Money('0.99', Currency::$EUR))->compareTo(new Money('1.01', Currency::$EUR))
      );
    }

    /**
     * Test compareTo() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotCompareDifferentCurrencies() {
      create(new Money('1.01', Currency::$EUR))->compareTo(new Money('0.99', Currency::$USD));
    }
    
    /**
     * Test multiplyBy() method - example: ten gallons of "87"
     *
     */
    #[@test]
    public function tenGallonsOfRegular() {
      $this->assertEquals(
        new Money('32.99', Currency::$EUR),
        create(new Money('3.299', Currency::$EUR))->multiplyBy(10)
      );
    }

    /**
     * Test multiplyBy() method - example: currency exchange
     *
     */
    #[@test]
    public function aThousandEurosInDollars() {
      $this->assertEquals(
        new Money('1496.64', Currency::$EUR),
        create(new Money('1000.00', Currency::$EUR))->multiplyBy(1.49664)
      );
    }
  }
?>
