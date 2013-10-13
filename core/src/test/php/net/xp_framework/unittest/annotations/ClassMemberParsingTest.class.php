<?php namespace net\xp_framework\unittest\annotations;

use net\xp_framework\unittest\annotations\fixture\Namespaced;

/**
 * Tests the XP Framework's annotation parsing implementation
 *
 * @see   https://github.com/xp-framework/xp-framework/pull/328
 */
class ClassMemberParsingTest extends \unittest\TestCase {
  const CONSTANT = 'local';

  #[@test, @values([self::CONSTANT])]
  public function class_constant_via_self($value) {
    $this->assertEquals('local', $value);
  }

  #[@test, @values([ClassMemberParsingTest::CONSTANT])]
  public function class_constant_via_unqualified_current($value) {
    $this->assertEquals('local', $value);
  }

  #[@test, @values([\net\xp_framework\unittest\annotations\ClassMemberParsingTest::CONSTANT])]
  public function class_constant_via_fully_qualified_current($value) {
    $this->assertEquals('local', $value);
  }

  #[@test, @values([Namespaced::CONSTANT])]
  public function class_constant_via_imported_classname($value) {
    $this->assertEquals('namespaced', $value);
  }

  #[@test, @values([\net\xp_framework\unittest\annotations\fixture\Namespaced::CONSTANT])]
  public function class_constant_via_fully_qualified($value) {
    $this->assertEquals('namespaced', $value);
  }
}
