<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.core';

  uses('lang.types.ArrayList');

  /**
   * Extensions for the ArrayList class
   *
   * @see      xp://net.xp_framework.unittest.core.ExtensionMethodTest
   */
  class net·xp_framework·unittest·core·ArrayListExtensions extends Object {

    static function __static() {
      xp::extensions('lang.types.ArrayList', __CLASS__);
    }

    /**
     * Find first element matching a given predicate
     *
     * @param   lang.types.ArrayList list
     * @param   var predicate
     * @return  var
     */
    #[@extension]
    public static function find(ArrayList $list, $predicate) {
      $r= array();
      foreach ($list as $value) {
        if ($predicate($value)) return $value;
      }
      return NULL;
    }
    
    /**
     * Find all elements matching a given predicate
     *
     * @param   lang.types.ArrayList list
     * @param   var predicate
     * @return  lang.types.ArrayList
     */
    #[@extension]
    public static function findAll(ArrayList $list, $predicate) {
      $r= array();
      foreach ($list as $value) {
        if ($predicate($value)) $r[]= $value;
      }
      return ArrayList::newInstance($r);
    }
  }
?>
