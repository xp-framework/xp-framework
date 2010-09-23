<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  /**
   * Helper for lambda tests
   *
   * @see      xp://tests.execution.source.LambdaTest
   */
  class net·xp_lang·tests·execution·source·Functions extends Object {
    
    /**
     * Apply a function to each element in an array
     *
     * @param   var[] in
     * @param   var func
     * @return  var[]
     */
    public static function apply($in, $func) {
      return array_map($func, $in);
    }
  }
?>
