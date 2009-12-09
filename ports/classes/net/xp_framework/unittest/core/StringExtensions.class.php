<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core';

  uses('lang.types.String', 'text.regex.Pattern');

  /**
   * Extensions for the String class
   *
   * @see      xp://net.xp_framework.unittest.core.ExtensionMethodTest
   */
  class net·xp_framework·unittest·core·StringExtensions extends Object {

    static function __static() {
      xp::extensions('lang.types.String', __CLASS__);
    }

    /**
     * Find first element matching a given predicate
     *
     * @param   lang.types.String s
     * @param   text.regex.Pattern p
     * @return  bool
     */
    #[@extension]
    public static function matches(String $s, Pattern $p) {
      return $p->matches($s);
    }
  }
?>
