<?php namespace net\xp_framework\unittest\core;

/**
 * TestCase for enumerations
 *
 * @see   xp://net.xp_framework.unittest.core.Coin
 * @see   xp://net.xp_framework.unittest.core.Operation
 * @see   xp://net.xp_framework.unittest.core.Weekday
 * @see   xp://lang.Enum
 * @see   xp://lang.XPClass#isEnum
 * @see   http://xp-framework.net/rfc/0132
 */
class EnumTest extends \unittest\TestCase {

  /**
   * Asserts given modifiers contain abstract
   *
   * @param   int modifiers
   * @throws  unittest.AssertionFailedError
   */
  protected function assertAbstract($modifiers) {
    $this->assertTrue(
      \lang\reflect\Modifiers::isAbstract($modifiers), 
      implode(' | ', \lang\reflect\Modifiers::namesOf($modifiers))
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
      \lang\reflect\Modifiers::isAbstract($modifiers), 
      implode(' | ', \lang\reflect\Modifiers::namesOf($modifiers))
    );
  }

  #[@test]
  public function coinIsAnEnums() {
    $this->assertTrue(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin')->isEnum());
  }
  
  #[@test]
  public function operationIsAnEnums() {
    $this->assertTrue(\lang\XPClass::forName('net.xp_framework.unittest.core.Operation')->isEnum());
  }

  #[@test]
  public function thisIsNotAnEnum() {
    $this->assertFalse($this->getClass()->isEnum());
  }

  #[@test]
  public function enumBaseClassIsAbstract() {
    $this->assertAbstract(\lang\XPClass::forName('lang.Enum')->getModifiers());
  }

  #[@test]
  public function operationEnumIsAbstract() {
    $this->assertAbstract(\lang\XPClass::forName('net.xp_framework.unittest.core.Operation')->getModifiers());
  }

  #[@test]
  public function coinEnumIsNotAbstract() {
    $this->assertNotAbstract(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin')->getModifiers());
  }

  #[@test]
  public function coinMemberAreSameClass() {
    $this->assertInstanceOf('net.xp_framework.unittest.core.Coin', Coin::$penny);
  }

  #[@test]
  public function operationMembersAreSubclasses() {
    $this->assertInstanceOf('net.xp_framework.unittest.core.Operation', Operation::$plus);
  }

  #[@test]
  public function enumMembersAreNotAbstract() {
    $this->assertNotAbstract(Coin::$penny->getClass()->getModifiers());
    $this->assertNotAbstract(Operation::$plus->getClass()->getModifiers());
  }

  #[@test]
  public function coinValues() {
    $this->assertEquals(
      array(Coin::$penny, Coin::$nickel, Coin::$dime, Coin::$quarter),
      Coin::values()
    );
  }

  #[@test]
  public function operationValues() {
    $this->assertEquals(
      array(Operation::$plus, Operation::$minus, Operation::$times, Operation::$divided_by),
      Operation::values()
    );
  }

  #[@test]
  public function pennyCoinClass() {
    $this->assertInstanceOf('net.xp_framework.unittest.core.Coin', Coin::$penny);
  }

  #[@test]
  public function nickelCoinName() {
    $this->assertEquals('nickel', Coin::$nickel->name());
  }

  #[@test]
  public function nickelCoinValue() {
    $this->assertEquals(2, Coin::$nickel->value());
  }

  #[@test]
  public function stringRepresentation() {
    $this->assertEquals('dime', Coin::$dime->toString());
  }

  #[@test]
  public function sameCoinsAreEqual() {
    $this->assertEquals(Coin::$quarter, Coin::$quarter);
  }

  #[@test]
  public function differentCoinsAreNotEqual() {
    $this->assertNotEquals(Coin::$penny, Coin::$quarter);
  }

  #[@test, @expect('lang.CloneNotSupportedException')]
  public function enumMembersAreNotCloneable() {
    clone Coin::$penny;
  }

  #[@test]
  public function valueOf() {
    $this->assertEquals(
      Coin::$penny, 
      \lang\Enum::valueOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin'), 'penny')
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function valueOfNonExistant() {
    \lang\Enum::valueOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin'), '@@DOES_NOT_EXIST@@');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function valueOfNonEnum() {
    \lang\Enum::valueOf($this, 'irrelevant');
  }

  #[@test]
  public function valueOfAbstractEnum() {
    $this->assertEquals(
      Operation::$plus, 
      \lang\Enum::valueOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Operation'), 'plus')
    );
  }

  #[@test]
  public function valuesOf() {
    $this->assertEquals(
      array(Coin::$penny, Coin::$nickel, Coin::$dime, Coin::$quarter),
      \lang\Enum::valuesOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Coin'))
    );
  }

  #[@test]
  public function valuesOfAbstractEnum() {
    $this->assertEquals(
      array(Operation::$plus, Operation::$minus, Operation::$times, Operation::$divided_by),
      \lang\Enum::valuesOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Operation'))
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function valuesOfNonEnum() {
    \lang\Enum::valuesOf($this);
  }

  #[@test]
  public function plusOperation() {
    $this->assertEquals(2, Operation::$plus->evaluate(1, 1));
  }

  #[@test]
  public function minusOperation() {
    $this->assertEquals(0, Operation::$minus->evaluate(1, 1));
  }

  #[@test]
  public function timesOperation() {
    $this->assertEquals(21, Operation::$times->evaluate(7, 3));
  }

  #[@test]
  public function dividedByOperation() {
    $this->assertEquals(5, Operation::$divided_by->evaluate(10, 2));
  }
  
  #[@test]
  public function staticMemberNotInEnumValuesOf() {
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      \lang\Enum::valuesOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Profiling'))
    );
  }

  #[@test]
  public function staticMemberNotInValues() {
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      Profiling::values()
    );
  }
  
  #[@test, @expect('lang.IllegalArgumentException')]
  public function staticMemberNotWithEnumValueOf() {
    \lang\Enum::valueOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Profiling'), 'fixture');
  }

  #[@test]
  public function staticEnumMemberNotInEnumValuesOf() {
    Profiling::$fixture= Coin::$penny;
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      \lang\Enum::valuesOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Profiling'))
    );
    Profiling::$fixture= NULL;
  }

  #[@test]
  public function staticEnumMemberNotInValues() {
    Profiling::$fixture= Coin::$penny;
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      Profiling::values()
    );
    Profiling::$fixture= NULL;
  }

  #[@test]
  public function staticObjectMemberNotInEnumValuesOf() {
    Profiling::$fixture= $this;
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      \lang\Enum::valuesOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Profiling'))
    );
    Profiling::$fixture= NULL;
  }

  #[@test]
  public function staticObjectMemberNotInValues() {
    Profiling::$fixture= $this;
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      Profiling::values()
    );
    Profiling::$fixture= NULL;
  }

  #[@test]
  public function staticPrimitiveMemberNotInEnumValuesOf() {
    Profiling::$fixture= array($this, $this->name);
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      \lang\Enum::valuesOf(\lang\XPClass::forName('net.xp_framework.unittest.core.Profiling'))
    );
    Profiling::$fixture= NULL;
  }

  #[@test]
  public function staticPrimitiveMemberNotInValues() {
    Profiling::$fixture= array($this, $this->name);
    $this->assertEquals(
      array(Profiling::$INSTANCE, Profiling::$EXTENSION),
      Profiling::values()
    );
    Profiling::$fixture= NULL;
  }

  #[@test]
  public function enumValuesMethodProvided() {
    $this->assertEquals(
      array(Weekday::$MON, Weekday::$TUE, Weekday::$WED, Weekday::$THU, Weekday::$FRI, Weekday::$SAT, Weekday::$SUN),
      Weekday::values()
    );
  }

  #[@test]
  public function enumValueInitializedToDeclaration() {
    $this->assertEquals(1, Weekday::$MON->ordinal());
  }
}
