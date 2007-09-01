<?php
/* This class is part of the XP framework
 *
 * $Id: EnumTest.class.php 10908 2007-07-31 12:06:15Z friebe $ 
 */

  namespace net::xp_framework::unittest::core;

  ::uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.Coin',
    'net.xp_framework.unittest.core.Operation'
  );

  /**
   * TestCase for enumerations
   *
   * @see      xp://net.xp_framework.unittest.core.Coin
   * @see      xp://net.xp_framework.unittest.core.Operation
   * @see      xp://lang.Enum
   * @see      http://xp-framework.net/rfc/0132
   * @purpose  Unittest
   */
  class EnumTest extends unittest::TestCase {

    /**
     * Asserts given modifiers contain abstract
     *
     * @param   int modifiers
     * @throws  unittest.AssertionFailedError
     */
    protected function assertAbstract($modifiers) {
      $this->assertTrue(
        lang::reflect::Modifiers::isAbstract($modifiers), 
        implode(' | ', lang::reflect::Modifiers::namesOf($modifiers))
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
        lang::reflect::Modifiers::isAbstract($modifiers), 
        implode(' | ', lang::reflect::Modifiers::namesOf($modifiers))
      );
    }

    /**
     * Test reflective check whether a given instance is an enum
     *
     * @see   xp://lang.XPClass#isEnum
     */
    #[@test]
    public function coinIsAnEnums() {
      $this->assertTrue(lang::XPClass::forName('net.xp_framework.unittest.core.Coin')->isEnum());
    }
    
    /**
     * Test reflective check whether a given instance is an enum
     *
     * @see   xp://lang.XPClass#isEnum
     */
    #[@test]
    public function operationIsAnEnums() {
      $this->assertTrue(lang::XPClass::forName('net.xp_framework.unittest.core.Operation')->isEnum());
    }

    /**
     * Test reflective check whether a given instance is an enum
     *
     * @see   xp://lang.XPClass#isEnum
     */
    #[@test]
    public function thisIsNotAnEnum() {
      $this->assertFalse($this->getClass()->isEnum());
    }

    /**
     * Test enum base class is abstract
     *
     */
    #[@test]
    public function enumBaseClassIsAbstract() {
      $this->assertAbstract(lang::XPClass::forName('lang.Enum')->getModifiers());
    }

    /**
     * Test Operation enum is abstract
     *
     */
    #[@test]
    public function operationEnumIsAbstract() {
      $this->assertAbstract(lang::XPClass::forName('net.xp_framework.unittest.core.Operation')->getModifiers());
    }

    /**
     * Test Coin enum is not abstract
     *
     */
    #[@test]
    public function coinEnumIsNotAbstract() {
      $this->assertNotAbstract(lang::XPClass::forName('net.xp_framework.unittest.core.Coin')->getModifiers());
    }

    /**
     * Test coin members are of the class as their container
     *
     */
    #[@test]
    public function coinMemberAreSameClass() {
      $this->assertClass(Coin::$penny, 'net.xp_framework.unittest.core.Coin');
    }

    /**
     * Test operation members are subclasses as their container's class
     *
     */
    #[@test]
    public function operationMembersAreSubclasses() {
      $this->assertSubclass(Operation::$plus, 'net.xp_framework.unittest.core.Operation');
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
      $this->assertEquals(
        array(Coin::$penny, Coin::$nickel, Coin::$dime, Coin::$quarter),
        Coin::values()
      );
    }

    /**
     * Test class of penny coin
     *
     */
    #[@test]
    public function pennyCoinClass() {
      $this->assertClass(Coin::$penny, 'net.xp_framework.unittest.core.Coin');
    }

    /**
     * Test name of nickel coin
     *
     */
    #[@test]
    public function nickelCoinName() {
      $this->assertEquals('nickel', Coin::$nickel->name());
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
        lang::Enum::valueOf(lang::XPClass::forName('net.xp_framework.unittest.core.Coin'), 'penny')
      );
    }

    /**
     * Test Enum::valueOf() method
     *
     */
    #[@test]
    public function valuseOf() {
      $this->assertEquals(
        array(Coin::$penny, Coin::$nickel, Coin::$dime, Coin::$quarter),
        lang::Enum::valuesOf(lang::XPClass::forName('net.xp_framework.unittest.core.Coin'))
      );
    }

    /**
     * Test Enum::valueOf() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function valueOfNonExistant() {
      lang::Enum::valueOf(lang::XPClass::forName('net.xp_framework.unittest.core.Coin'), '@@DOES_NOT_EXIST@@');
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
