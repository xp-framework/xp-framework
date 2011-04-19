<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

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
      $this->assertTrue(Modifiers::isPublic(0));
      $this->assertEquals('public', Modifiers::stringOf(0));
    }

    /**
     * Tests the public modifier
     *
     */
    #[@test]
    public function publicModifier() {
      $this->assertTrue(Modifiers::isPublic(MODIFIER_PUBLIC));
      $this->assertEquals('public', Modifiers::stringOf(MODIFIER_PUBLIC));
    }

    /**
     * Tests the private modifier
     *
     */
    #[@test]
    public function privateModifier() {
      $this->assertTrue(Modifiers::isPrivate(MODIFIER_PRIVATE));
      $this->assertEquals('private', Modifiers::stringOf(MODIFIER_PRIVATE));
    }

    /**
     * Tests the protected modifier
     *
     */
    #[@test]
    public function protectedModifier() {
      $this->assertTrue(Modifiers::isProtected(MODIFIER_PROTECTED));
      $this->assertEquals('protected', Modifiers::stringOf(MODIFIER_PROTECTED));
    }

    /**
     * Tests the abstract modifier
     *
     */
    #[@test]
    public function abstractModifier() {
      $this->assertTrue(Modifiers::isAbstract(MODIFIER_ABSTRACT));
      $this->assertEquals('public abstract', Modifiers::stringOf(MODIFIER_ABSTRACT));
    }

    /**
     * Tests the final modifier
     *
     */
    #[@test]
    public function finalModifier() {
      $this->assertTrue(Modifiers::isFinal(MODIFIER_FINAL));
      $this->assertEquals('public final', Modifiers::stringOf(MODIFIER_FINAL));
    }

    /**
     * Tests the static modifier
     *
     */
    #[@test]
    public function staticModifier() {
      $this->assertTrue(Modifiers::isStatic(MODIFIER_STATIC));
      $this->assertEquals('public static', Modifiers::stringOf(MODIFIER_STATIC));
    }
  }
?>
