<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.tools.vm.util.Modifiers'
  );

  /**
   * Tests Modifiers utility class
   *
   * @purpose  Unit Test
   */
  class ModifiersTest extends TestCase {

    /**
     * Helper method
     *
     * @access  protected
     * @param   string expected a space-separated list of names
     * @param   int modifiers a modifier bitfield
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertModifiers($string, $modifiers) {
      $names= Modifiers::namesOf($modifiers);
      $expect= explode(' ', $string);
      sort($names);
      sort($expect);
      $this->assertEquals($expect, $names);
    }

    /**
     * Helper method
     *
     * @access  protected
     * @param   array<string, list> list
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertModifierList($list) {
      foreach ($list as $expect => $modifiers) {
        $this->assertModifiers($expect, $modifiers);
      }
    }

    /**
     * Tests *P*ublic / *P*rivate / *P*rotected
     *
     * @access  public
     */
    #[@test]
    function pppModifiers() {
      $this->assertModifierList(array(
        'public'      => MODIFIER_PUBLIC,
        'private'     => MODIFIER_PRIVATE,
        'protected'   => MODIFIER_PROTECTED,
      ));
    }

    /**
     * Tests public is the default if no modifier is given
     *
     * @access  public
     */
    #[@test]
    function publicIsDefault() {
      $this->assertModifiers('public', 0);
    }

    /**
     * Tests abstract / final / static are public per default
     *
     * @access  public
     */
    #[@test]
    function publicDefaultForSingleModifiers() {
      $this->assertModifierList(array(
        'public final'       => MODIFIER_FINAL,
        'public abstract'    => MODIFIER_ABSTRACT,
        'public static'      => MODIFIER_STATIC,
      ));
    }

    /**
     * Tests PPP combined with static
     *
     * @access  public
     */
    #[@test]
    function pppModifiersWithStatic() {
      $this->assertModifierList(array(
        'public static'      => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'private static'     => MODIFIER_PRIVATE | MODIFIER_STATIC,
        'protected static'   => MODIFIER_PROTECTED | MODIFIER_STATIC,
      ));
    }
  }
?>
