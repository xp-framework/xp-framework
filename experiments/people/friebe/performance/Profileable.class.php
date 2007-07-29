<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Denotes a class implementing this is profileable
   *
   * @purpose  Profiling
   */
  interface Profileable  {

    /**
     * Runs this test the specified number of times
     *
     * @param   int times
     */
    public function run($times);
    
  }
?>
