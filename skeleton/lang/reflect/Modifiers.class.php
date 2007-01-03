<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('MODIFIER_STATIC',       1);
  define('MODIFIER_ABSTRACT',     2);
  define('MODIFIER_FINAL',        4);
  define('MODIFIER_PUBLIC',     256);
  define('MODIFIER_PROTECTED',  512);
  define('MODIFIER_PRIVATE',   1024);

  /**
   * This class provides static methods to convert the numerical value
   * access modifiers (public, private, protected, final, abstract, 
   * static) are encoded in (as a bitfield) into strings.
   *
   * @see      xp://lang.reflect.Routine#getModifiers
   * @see      xp://lang.reflect.Field#getModifiers
   * @purpose  Reflection utility
   */
  class Modifiers extends Object {

    /**
     * Retrieves modifier names as an array. The order in which the 
     * modifiers are returned is the following:
     *
     * <pre>
     *   [access] static abstract final
     * </pre>
     * [access] is one on public, private or protected.
     *
     * @param   int m modifier bitfield
     * @return  string[]
     */
    public static function namesOf($m) {
      $names= array();
      switch ($m & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
        case MODIFIER_PRIVATE: $names[]= 'private'; break;
        case MODIFIER_PROTECTED: $names[]= 'protected'; break;
        case MODIFIER_PUBLIC: default: $names[]= 'public'; break;
      }
      if ($m & MODIFIER_STATIC) $names[]= 'static';
      if ($m & MODIFIER_ABSTRACT) $names[]= 'abstract';
      if ($m & MODIFIER_FINAL) $names[]= 'final';
      return $names;
    }

    /**
     * Retrieves modifier names as a string
     *
     * @param   int m modifier bitfield
     * @return  string
     */
    public static function stringOf($m) {
      return implode(' ', self::namesOf($m));
    }
  }
?>
