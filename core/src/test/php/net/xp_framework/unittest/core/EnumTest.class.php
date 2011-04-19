<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.Coin',
    'net.xp_framework.unittest.core.Profiling',
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
     * Test reflective check whether a given instance is an enum
     *
     * @see   xp://lang.XPClass#isEnum
     */
    #[@test]
    public function coinIsAnEnums() {
      $this->assertTrue(XPClass::forName('net.xp_framework.unittest.core.Coin')->isEnum());
    }
    
    /**
     * Test reflective check whether a given instance is an enum
     *
     * @see   xp://lang.XPClass#isEnum
     */
    #[@test]
    public function operationIsAnEnums() {
      $this->assertTrue(XPClass::forName('net.xp_framework.unittest.core.Operation')->isEnum());
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
      $this->assertAbstract(XPClass::forName('lang.Enum')->getModifiers());
    }

    /**
     * Test Operation enum is abstract
     *
     */
    #[@test]
    public function operationEnumIsAbstract() {
      $this->assertAbstract(XPClass::forName('net.xp_framework.unittest.core.Operation')->getModifiers());
    }

    /**
     * Test Coin enum is not abstract
     *
     */
    #[@test]
    public function coinEnumIsNotAbstract() {
      $this->assertNotAbstract(XPClass::forName('net.xp_framework.unittest.core.Coin')->getModifiers());
    }

    /**
     * Test coin members are of the class as their container
     *
     */
    #[@test]
    public function coinMemberAreSameClass() {
      $this->assertInstanceOf('net.xp_framework.unittest.core.Coin', Coin::$penny);
    }

    /**
     * Test operation members are subclasses as their container's class
     *
     */
    #[@test]
    public function operationMembersAreSubclasses() {
      $this->assertInstanceOf('net.xp_framework.unittest.core.Operation', Operation::$plus);
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
     * Test Operation::values() method
     *
     */
    #[@test]
    public function operationValues() {
      $this->assertEquals(
        array(Operation::$plus, Operation::$minus, Operation::$times, Operation::$divided_by),
        Operation::values()
      );
    }

    /**
     * Test class of penny coin
     *
     */
    #[@test]
    public function pennyCoinClass() {
      $this->assertInstanceOf('net.xp_framework.unittest.core.Coin', Coin::$penny);
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
     * Test name of nickel coin
     *
     */
    #[@test]
    public function nickelCoinValue() {
      $this->assertEquals(2, Coin::$nickel->value());
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
        Enum::valueOf(XPClass::forName('net.xp_framework.unittest.core.Coin'), 'penny')
      );
    }

    /**
     * Test Enum::valueOf() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function valueOfNonExistant() {
      Enum::valueOf(XPClass::forName('net.xp_framework.unittest.core.Coin'), '@@DOES_NOT_EXIST@@');
    }

    /**
     * Test Enum::valueOf() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function valueOfNonEnum() {
      Enum::valueOf($this, 'irrelevant');
    }

    /**
     * Test Enum::valueOf() method for an abstract enumeration
     *
     */
    #[@test]
    public function valueOfAbstractEnum() {
      $this->assertEquals(
        Operation::$plus, 
        Enum::valueOf(XPClass::forName('net.xp_framework.unittest.core.Operation'), 'plus')
      );
    }

    /**
     * Test Enum::valuesOf() method
     *
     */
    #[@test]
    public function valuesOf() {
      $this->assertEquals(
        array(Coin::$penny, Coin::$nickel, Coin::$dime, Coin::$quarter),
        Enum::valuesOf(XPClass::forName('net.xp_framework.unittest.core.Coin'))
      );
    }

    /**
     * Test Enum::valuesOf() method for an abstract enumeration
     *
     */
    #[@test]
    public function valuesOfAbstractEnum() {
      $this->assertEquals(
        array(Operation::$plus, Operation::$minus, Operation::$times, Operation::$divided_by),
        Enum::valuesOf(XPClass::forName('net.xp_framework.unittest.core.Operation'))
      );
    }

    /**
     * Test Enum::valuesOf() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function valuesOfNonEnum() {
      Enum::valuesOf($this);
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
    
    /**
     * Test Profiling::$fixture does not appear in Enum::valuesOf()
     *
     */
    #[@test]
    public function staticMemberNotInEnumValuesOf() {
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Enum::valuesOf(XPClass::forName('net.xp_framework.unittest.core.Profiling'))
      );
    }

    /**
     * Test Profiling::$fixture does not appear in Profiling::values()
     *
     */
    #[@test]
    public function staticMemberNotInValues() {
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Profiling::values()
      );
    }
    
    /**
     * Test Profiling::$fixture does not work with Enum::valueOf()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function staticMemberNotWithEnumValueOf() {
      Enum::valueOf(XPClass::forName('net.xp_framework.unittest.core.Profiling'), 'fixture');
    }

    /**
     * Test Profiling::$fixture does not appear in Enum::valuesOf()
     *
     */
    #[@test]
    public function staticEnumMemberNotInEnumValuesOf() {
      Profiling::$fixture= Coin::$penny;
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Enum::valuesOf(XPClass::forName('net.xp_framework.unittest.core.Profiling'))
      );
      Profiling::$fixture= NULL;
    }

    /**
     * Test Profiling::$fixture does not appear in Profiling::values()
     *
     */
    #[@test]
    public function staticEnumMemberNotInValues() {
      Profiling::$fixture= Coin::$penny;
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Profiling::values()
      );
      Profiling::$fixture= NULL;
    }

    /**
     * Test Profiling::$fixture does not appear in Enum::valuesOf()
     *
     */
    #[@test]
    public function staticObjectMemberNotInEnumValuesOf() {
      Profiling::$fixture= $this;
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Enum::valuesOf(XPClass::forName('net.xp_framework.unittest.core.Profiling'))
      );
      Profiling::$fixture= NULL;
    }

    /**
     * Test Profiling::$fixture does not appear in Profiling::values()
     *
     */
    #[@test]
    public function staticObjectMemberNotInValues() {
      Profiling::$fixture= $this;
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Profiling::values()
      );
      Profiling::$fixture= NULL;
    }

    /**
     * Test Profiling::$fixture does not appear in Enum::valuesOf()
     *
     */
    #[@test]
    public function staticPrimitiveMemberNotInEnumValuesOf() {
      Profiling::$fixture= array($this, $this->name);
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Enum::valuesOf(XPClass::forName('net.xp_framework.unittest.core.Profiling'))
      );
      Profiling::$fixture= NULL;
    }

    /**
     * Test Profiling::$fixture does not appear in Profiling::values()
     *
     */
    #[@test]
    public function staticPrimitiveMemberNotInValues() {
      Profiling::$fixture= array($this, $this->name);
      $this->assertEquals(
        array(Profiling::$INSTANCE, Profiling::$EXTENSION),
        Profiling::values()
      );
      Profiling::$fixture= NULL;
    }
  }
?>
