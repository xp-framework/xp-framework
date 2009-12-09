<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.core';

  uses('util.collections.IList');

  /**
   * Extensions for the IList interface
   *
   * @see      xp://net.xp_framework.unittest.core.ExtensionMethodTest
   */
  class net·xp_framework·unittest·core·IListExtensions extends Object {

    static function __static() {
      xp::extensions('util.collections.IList', __CLASS__);
    }

    /**
     * Find first element matching a given predicate
     *
     * @param   util.collections.IList list
     * @param   var predicate
     * @return  var
     */
    #[@extension]
    public static function find(IList $list, $predicate) {
      $r= array();
      foreach ($list as $value) {
        if ($predicate($value)) return $value;
      }
      return NULL;
    }
    
    /**
     * Find all elements matching a given predicate
     *
     * @param   util.collections.IList list
     * @param   var predicate
     * @return  util.collections.IList
     */
    #[@extension]
    public static function findAll(IList $list, $predicate) {
      $r= $list->getClass()->newInstance();
      foreach ($list as $value) {
        if ($predicate($value)) $r[]= $value;
      }
      return $r;
    }
  }
?>
