<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.Vector');

  /**
   * Fixture
   *
   */
  class GenericArrays extends Object {
    
    /**
     * Return value of a given type
     *
     * @param   <T>
     * @param   T... args
     * @return  util.collections.Vector<T> result
     */
    #[@generic(self= 'T', return= 'T')]
    public static function asList«»($T) {
      $list= XPClass::forName('util.collections.Vector')->newGenericType(array($T))->newInstance();
      $args= func_get_args();
      $list->addAll(array_slice($args, 1));
      return $list;
    }
  }
?>
