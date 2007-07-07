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
     * Asserts given modifiers contain abstract
     *
     * @param   int modifiers
     * @throws  unittest.AssertionFailedError
     */
    protected function assertAbstract($modifiers) {
      $this->assertTrue(
        Modifiers::isAbstract($modifiers), 
        implode(' | ', Modifiers::namesOf($modifiers))
      );
    }

    /**
     * Asserts given modifiers do not contain abstract
     *
     * @param   int modifiers
     * @throws  unittest.AssertionFailedError
     */
    protected function assertNotAbstract($modifiers) {
      $this->assertFalse(
        Modifiers::isAbstract($modifiers), 
        implode(' | ', Modifiers::namesOf($modifiers))
      );
    }

    /**
     * Test XPClass::isEnum()
     *
     */
    #[@test]
    public function areEnums() {
      $this->assertFalse(XPClass::forName('lang.Object')->isEnum());
      $this->assertTrue(XPClass::forName('examples.coin.Coin')->isEnum());
      $this->assertTrue(XPClass::forName('examples.operation.Operation')->isEnum());
    }

    /**
     * Test enum base class is abstract
     *
     */
    #[@test]
    public function enumBaseClassIsAbstract() {
      $this->assertAbstract(XPClass::forName('lang.Enum')->getModifiers());
    }

    /**
     * Test Operation enum is abstract
     *
     */
    #[@test]
    public function operationEnumIsAbstract() {
      $this->assertAbstract(XPClass::forName('examples.operation.Operation')->getModifiers());
    }

    /**
     * Test Coin enum is not abstract
     *
     */
    #[@test]
    public function coinEnumIsNotAbstract() {
      $this->assertNotAbstract(XPClass::forName('examples.coin.Coin')->getModifiers());
    }

    /**
     * Test coin members are of the class as their container
     *
     */
    #[@test]
    public function coinMemberAreSameClass() {
      $this->assertClass(Coin::$penny, 'examples.coin.Coin');
    }

    /**
     * Test operation members are subclasses as their container's class
     *
     */
    #[@test]
    public function operationMembersAreSubclasses() {
      $this->assertSubclass(Operation::$plus, 'examples.operation.Operation');
    }

    /**
     * Test enum members' classes are not abstract
     *
     */
    #[@test]
    public function enumMembersAreNotAbstract() {
      $this->assertNotAbstract(Coin::$penny->getClass()->getModifiers());
      $this->assertNotAbstract(Operation::$plus->getClass()->getModifiers());
    }

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

    /**
     * Test Operation::$plus
     *
     */
    #[@test]
    public function plusOperation() {
      $this->assertEquals(2, Operation::$plus->evaluate(1, 1));
    }

    /**
     * Test Operation::$minus
     *
     */
    #[@test]
    public function minusOperation() {
      $this->assertEquals(0, Operation::$minus->evaluate(1, 1));
    }

    /**
     * Test Operation::$times
     *
     */
    #[@test]
    public function timesOperation() {
      $this->assertEquals(21, Operation::$times->evaluate(7, 3));
    }

    /**
     * Test Operation::$divided_by
     *
     */
    #[@test]
    public function dividedByOperation() {
      $this->assertEquals(5, Operation::$divided_by->evaluate(10, 2));
    }
  }
?>
