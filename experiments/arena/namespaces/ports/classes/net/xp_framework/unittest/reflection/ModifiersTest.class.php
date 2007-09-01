<?php
/* This class is part of the XP framework
 *
 * $Id: ModifiersTest.class.php 9081 2007-01-03 12:08:40Z friebe $ 
 */

  namespace net::xp_framework::unittest::reflection;

  ::uses('unittest.TestCase');

  /**
   * Test the XP reflection API's Modifiers utility class
   *
   * @see      xp://lang.reflect.Modifiers
   * @purpose  Testcase
   */
  class ModifiersTest extends unittest::TestCase {

    /**
     * Tests the public modifier should be the default
     *
     */
    #[@test]
    public function defaultModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isPublic(0));
      $this->assertEquals('public', lang::reflect::Modifiers::stringOf(0));
    }

    /**
     * Tests the public modifier
     *
     */
    #[@test]
    public function publicModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isPublic(MODIFIER_PUBLIC));
      $this->assertEquals('public', lang::reflect::Modifiers::stringOf(MODIFIER_PUBLIC));
    }

    /**
     * Tests the private modifier
     *
     */
    #[@test]
    public function privateModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isPrivate(MODIFIER_PRIVATE));
      $this->assertEquals('private', lang::reflect::Modifiers::stringOf(MODIFIER_PRIVATE));
    }

    /**
     * Tests the protected modifier
     *
     */
    #[@test]
    public function protectedModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isProtected(MODIFIER_PROTECTED));
      $this->assertEquals('protected', lang::reflect::Modifiers::stringOf(MODIFIER_PROTECTED));
    }

    /**
     * Tests the abstract modifier
     *
     */
    #[@test]
    public function abstractModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isAbstract(MODIFIER_ABSTRACT));
      $this->assertEquals('public abstract', lang::reflect::Modifiers::stringOf(MODIFIER_ABSTRACT));
    }

    /**
     * Tests the final modifier
     *
     */
    #[@test]
    public function finalModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isFinal(MODIFIER_FINAL));
      $this->assertEquals('public final', lang::reflect::Modifiers::stringOf(MODIFIER_FINAL));
    }

    /**
     * Tests the static modifier
     *
     */
    #[@test]
    public function staticModifier() {
      $this->assertTrue(lang::reflect::Modifiers::isStatic(MODIFIER_STATIC));
      $this->assertEquals('public static', lang::reflect::Modifiers::stringOf(MODIFIER_STATIC));
    }
  }
?>
