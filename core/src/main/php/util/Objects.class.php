<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Objects utility methods
   *
   * @test  xp://net.xp_framework.unittest.util.ObjectsTest
   */
  abstract class Objects extends Object {

    /**
     * Returns whether to objects are equal
     *
     * @param   var a
     * @param   var b
     * @return  bool
     */
    public static function equal($a, $b) {
      if ($a instanceof Generic) {
        return $a->equals($b);
      } else if (is_array($a)) {
        if (!is_array($b) || sizeof($a) !== sizeof($b)) return FALSE;
        foreach ($a as $key => $val) {
          if (!array_key_exists($key, $b) || !self::equal($val, $b[$key])) return FALSE;
        }
        return TRUE;
      } else {
        return $a === $b;
      }
    }

    /**
     * Returns a string representation
     *
     * @param  var val
     * @param  string default the value to use for NULL
     * @return string
     */
    public static function stringOf($val, $default= '') {
      return NULL === $val ?
        $default :
        ($val instanceof Generic ? $val->toString() : xp::stringOf($val))
      ;
    }

    /**
     * Returns a hash code
     *
     * @param  var val
     * @return string
     */
    public static function hashOf($val) {
      return NULL === $val ?
        'N;' :
        ($val instanceof Generic ? $val->hashCode() : serialize($val))
      ;
    }
  }
?>