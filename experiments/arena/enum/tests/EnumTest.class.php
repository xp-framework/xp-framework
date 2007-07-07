<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'examples.coin.Coin',
    'examples.operation.Operation'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.Enum
   */
  class EnumTest extends TestCase {
  
    /**
     * Test Coin::values() method
     *
     */
    #[@test]
    public function coinValues() {
      $v= Coin::values();
      $this->assertArray($v);
      $this->assertEquals(4, sizeof($v));
    }

    /**
     * Test class of penny coin
     *
     */
    #[@test]
    public function pennyCoinClass() {
      $this->assertClass(Coin::$penny, 'examples.coin.Coin');
    }

    /**
     * Test name of nickel coin
     *
     */
    #[@test]
    public function nickelCoinName() {
      $this->assertEquals('nickel', Coin::$nickel->name);
    }

    /**
     * Test an enum member's string representation
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->assertEquals('dime', Coin::$dime->toString());
    }

    /**
     * Tests equality
     *
     */
    #[@test]
    public function sameCoinsAreEqual() {
      $this->assertEquals(Coin::$quarter, Coin::$quarter);
    }

    /**
     * Test equality
     *
     */
    #[@test]
    public function differentCoinsAreNotEqual() {
      $this->assertNotEquals(Coin::$penny, Coin::$quarter);
    }

    /**
     * Test enum members are not cloneable
     *
     */
    #[@test, @expect('lang.CloneNotSupportedException')]
    public function enumMembersAreNotCloneable() {
      clone Coin::$penny;
    }

    /**
     * Test Enum::valueOf() method
     *
     */
    #[@test]
    public function valueOf() {
      $this->assertEquals(
        Coin::$penny, 
        Enum::valueOf(XPClass::forName('examples.coin.Coin'), 'penny')
      );
    }

    /**
     * Test Enum::valueOf() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function valueOfNonExistant() {
      Enum::valueOf(XPClass::forName('examples.coin.Coin'), '@@DOES_NOT_EXIST@@');
    }
  }
?>
