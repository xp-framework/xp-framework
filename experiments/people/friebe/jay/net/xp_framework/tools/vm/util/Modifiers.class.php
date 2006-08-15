<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Modifiers
   *
   * @purpose  Utility
   */
  class Modifiers extends Object {

    /**
     * Retrieves modified names
     *
     * @access  protected
     * @param   int m modifier bitfield
     * @return  string[]
     */
    function namesOf($m) {
      $names= array();
      if ($m & MODIFIER_ABSTRACT) $names[]= 'abstract';
      if ($m & MODIFIER_FINAL) $names[]= 'final';
      switch ($m & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
        case MODIFIER_PRIVATE: $names[]= 'private'; break;
        case MODIFIER_PROTECTED: $names[]= 'protected'; break;
        case MODIFIER_PUBLIC:
        default: $names[]= 'public'; break;
      }
      if ($m & MODIFIER_STATIC) $names[]= 'static';
      return $names;
    }
  }
?>
