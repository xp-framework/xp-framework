<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;


/**
 * Test the XP reflection API's Modifiers utility class
 *
 * @see      xp://lang.reflect.Modifiers
 * @purpose  Testcase
 */
class ModifiersTest extends TestCase {

  /**
   * Tests the public modifier should be the default
   *
   */
  #[@test]
  public function defaultModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isPublic(0));
  }

  /**
   * Tests the public modifier should be the default
   *
   */
  #[@test]
  public function defaultModifierString() {
    $this->assertEquals('public', \lang\reflect\Modifiers::stringOf(0));
  }

  /**
   * Tests the public modifier should be the default
   *
   */
  #[@test]
  public function defaultModifierNames() {
    $this->assertEquals(array('public'), \lang\reflect\Modifiers::namesOf(0));
  }

  /**
   * Tests the public modifier
   *
   */
  #[@test]
  public function publicModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isPublic(MODIFIER_PUBLIC));
  }

  /**
   * Tests the public modifier
   *
   */
  #[@test]
  public function publicModifierString() {
    $this->assertEquals('public', \lang\reflect\Modifiers::stringOf(MODIFIER_PUBLIC));
  }

  /**
   * Tests the public modifier
   *
   */
  #[@test]
  public function publicModifierNames() {
    $this->assertEquals(array('public'), \lang\reflect\Modifiers::namesOf(MODIFIER_PUBLIC));
  }

  /**
   * Tests the private modifier
   *
   */
  #[@test]
  public function privateModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isPrivate(MODIFIER_PRIVATE));
  }

  /**
   * Tests the private modifier
   *
   */
  #[@test]
  public function privateModifierString() {
    $this->assertEquals('private', \lang\reflect\Modifiers::stringOf(MODIFIER_PRIVATE));
  }

  /**
   * Tests the private modifier
   *
   */
  #[@test]
  public function privateModifierNames() {
    $this->assertEquals(array('private'), \lang\reflect\Modifiers::namesOf(MODIFIER_PRIVATE));
  }

  /**
   * Tests the protected modifier
   *
   */
  #[@test]
  public function protectedModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isProtected(MODIFIER_PROTECTED));
  }

  /**
   * Tests the protected modifier
   *
   */
  #[@test]
  public function protectedModifierString() {
    $this->assertEquals('protected', \lang\reflect\Modifiers::stringOf(MODIFIER_PROTECTED));
  }

  /**
   * Tests the protected modifier
   *
   */
  #[@test]
  public function protectedModifierNames() {
    $this->assertEquals(array('protected'), \lang\reflect\Modifiers::namesOf(MODIFIER_PROTECTED));
  }

  /**
   * Tests the abstract modifier
   *
   */
  #[@test]
  public function abstractModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isAbstract(MODIFIER_ABSTRACT));
  }

  /**
   * Tests the abstract modifier
   *
   */
  #[@test]
  public function abstractModifierString() {
    $this->assertEquals('public abstract', \lang\reflect\Modifiers::stringOf(MODIFIER_ABSTRACT));
  }

  /**
   * Tests the abstract modifier
   *
   */
  #[@test]
  public function abstractModifierNames() {
    $this->assertEquals(array('public', 'abstract'), \lang\reflect\Modifiers::namesOf(MODIFIER_ABSTRACT));
  }

  /**
   * Tests the final modifier
   *
   */
  #[@test]
  public function finalModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isFinal(MODIFIER_FINAL));
  }

  /**
   * Tests the final modifier
   *
   */
  #[@test]
  public function finalModifierString() {
    $this->assertEquals('public final', \lang\reflect\Modifiers::stringOf(MODIFIER_FINAL));
  }

  /**
   * Tests the final modifier
   *
   */
  #[@test]
  public function finalModifierNames() {
    $this->assertEquals(array('public', 'final'), \lang\reflect\Modifiers::namesOf(MODIFIER_FINAL));
  }

  /**
   * Tests the static modifier
   *
   */
  #[@test]
  public function staticModifier() {
    $this->assertTrue(\lang\reflect\Modifiers::isStatic(MODIFIER_STATIC));
  }

  /**
   * Tests the static modifier
   *
   */
  #[@test]
  public function staticModifierString() {
    $this->assertEquals('public static', \lang\reflect\Modifiers::stringOf(MODIFIER_STATIC));
  }

  /**
   * Tests the static modifier
   *
   */
  #[@test]
  public function staticModifierNames() {
    $this->assertEquals(array('public', 'static'), \lang\reflect\Modifiers::namesOf(MODIFIER_STATIC));
  }
}
