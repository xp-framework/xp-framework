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
    }

    /**
     * Tests the public modifier should be the default
     *
     */
    #[@test]
    public function defaultModifierString() {
      $this->assertEquals('public', Modifiers::stringOf(0));
    }

    /**
     * Tests the public modifier should be the default
     *
     */
    #[@test]
    public function defaultModifierNames() {
      $this->assertEquals(array('public'), Modifiers::namesOf(0));
    }

    /**
     * Tests the public modifier
     *
     */
    #[@test]
    public function publicModifier() {
      $this->assertTrue(Modifiers::isPublic(MODIFIER_PUBLIC));
    }

    /**
     * Tests the public modifier
     *
     */
    #[@test]
    public function publicModifierString() {
      $this->assertEquals('public', Modifiers::stringOf(MODIFIER_PUBLIC));
    }

    /**
     * Tests the public modifier
     *
     */
    #[@test]
    public function publicModifierNames() {
      $this->assertEquals(array('public'), Modifiers::namesOf(MODIFIER_PUBLIC));
    }

    /**
     * Tests the private modifier
     *
     */
    #[@test]
    public function privateModifier() {
      $this->assertTrue(Modifiers::isPrivate(MODIFIER_PRIVATE));
    }

    /**
     * Tests the private modifier
     *
     */
    #[@test]
    public function privateModifierString() {
      $this->assertEquals('private', Modifiers::stringOf(MODIFIER_PRIVATE));
    }

    /**
     * Tests the private modifier
     *
     */
    #[@test]
    public function privateModifierNames() {
      $this->assertEquals(array('private'), Modifiers::namesOf(MODIFIER_PRIVATE));
    }

    /**
     * Tests the protected modifier
     *
     */
    #[@test]
    public function protectedModifier() {
      $this->assertTrue(Modifiers::isProtected(MODIFIER_PROTECTED));
    }

    /**
     * Tests the protected modifier
     *
     */
    #[@test]
    public function protectedModifierString() {
      $this->assertEquals('protected', Modifiers::stringOf(MODIFIER_PROTECTED));
    }

    /**
     * Tests the protected modifier
     *
     */
    #[@test]
    public function protectedModifierNames() {
      $this->assertEquals(array('protected'), Modifiers::namesOf(MODIFIER_PROTECTED));
    }

    /**
     * Tests the abstract modifier
     *
     */
    #[@test]
    public function abstractModifier() {
      $this->assertTrue(Modifiers::isAbstract(MODIFIER_ABSTRACT));
    }

    /**
     * Tests the abstract modifier
     *
     */
    #[@test]
    public function abstractModifierString() {
      $this->assertEquals('public abstract', Modifiers::stringOf(MODIFIER_ABSTRACT));
    }

    /**
     * Tests the abstract modifier
     *
     */
    #[@test]
    public function abstractModifierNames() {
      $this->assertEquals(array('public', 'abstract'), Modifiers::namesOf(MODIFIER_ABSTRACT));
    }

    /**
     * Tests the final modifier
     *
     */
    #[@test]
    public function finalModifier() {
      $this->assertTrue(Modifiers::isFinal(MODIFIER_FINAL));
    }

    /**
     * Tests the final modifier
     *
     */
    #[@test]
    public function finalModifierString() {
      $this->assertEquals('public final', Modifiers::stringOf(MODIFIER_FINAL));
    }

    /**
     * Tests the final modifier
     *
     */
    #[@test]
    public function finalModifierNames() {
      $this->assertEquals(array('public', 'final'), Modifiers::namesOf(MODIFIER_FINAL));
    }

    /**
     * Tests the static modifier
     *
     */
    #[@test]
    public function staticModifier() {
      $this->assertTrue(Modifiers::isStatic(MODIFIER_STATIC));
    }

    /**
     * Tests the static modifier
     *
     */
    #[@test]
    public function staticModifierString() {
      $this->assertEquals('public static', Modifiers::stringOf(MODIFIER_STATIC));
    }

    /**
     * Tests the static modifier
     *
     */
    #[@test]
    public function staticModifierNames() {
      $this->assertEquals(array('public', 'static'), Modifiers::namesOf(MODIFIER_STATIC));
    }
  }
?>
