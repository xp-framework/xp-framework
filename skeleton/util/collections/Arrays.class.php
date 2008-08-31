<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.Vector', 'lang.types.ArrayList', 'util.Comparator');

  /**
   * Various methods for working with arrays.
   *
   * @test     xp://net.xp_framework.unittest.util.collection.ArraysTest
   * @see      xp://util.collections.IList
   * @see      xp://lang.types.ArrayList
   * @purpose  Extension methods for arrays
   */
  abstract class Arrays extends Object {
    public static $EMPTY= NULL;

    static function __static() {
      self::$EMPTY= new ArrayList();
    }
    
    /**
     * Returns an IList instance for a given array
     *
     * @param   lang.types.ArrayList array
     * @return  util.collections.IList
     */
    public static function asList(ArrayList $array) {
      $v= new Vector();
      $v->addAll($array->values);
      return $v;
    }
    
    /**
     * Sorts an array
     *
     * @see     php://sort
     * @param   lang.types.ArrayList array
     * @param   * method
     */
    public static function sort(ArrayList $array, $method= SORT_REGULAR) {
      if ($method instanceof Comparator) {
        usort($array->values, array($method, 'compare'));
      } else {
        sort($array->values, $method);
      }
    }

    /**
     * Returns a sorted array
     *
     * @see     php://sort
     * @param   lang.types.ArrayList array
     * @param   * method
     * @return  lang.types.ArrayList the sorted array
     */
    public static function sorted(ArrayList $array, $method= SORT_REGULAR) {
      $copy= clone $array;
      if ($method instanceof Comparator) {
        usort($copy->values, array($method, 'compare'));
      } else {
        sort($copy->values, $method);
      }
      return $copy;
    }

    /**
     * Checks whether a certain value is contained in an array
     *
     * @param   lang.types.ArrayList array
     * @param   * search
     * @return  bool
     */
    public static function contains(ArrayList $array, $search) {
      if ($search instanceof Generic) {
        foreach ($array->values as $value) {
          if ($search->equals($value)) return TRUE;
        }
      } else {
        foreach ($array->values as $value) {
          if ($search === $value) return TRUE;
        }
      }
      return FALSE;
    }
  }
?>
