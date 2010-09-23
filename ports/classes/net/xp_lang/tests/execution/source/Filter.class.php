<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  /**
   * Generic filter
   *
   * @see      xp://net.xp_lang.tests.execution.source.InstanceCreationTest
   */
  #[@generic(self= 'T')]
  interface net·xp_lang·tests·execution·source·Filter {
    
    /**
     * Returns whether this element should be accepted
     *
     * @param   T element
     * @return  bool
     */
    #[@generic(params= 'T')]
    public function accept($element);
  }
?>
