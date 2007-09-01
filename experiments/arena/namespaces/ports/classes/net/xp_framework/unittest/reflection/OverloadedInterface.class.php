<?php
/* This class is part of the XP framework
 *
 * $Id: OverloadedInterface.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::reflection;

  /**
   * Interface with overloaded methods
   *
   * @see      xp://lang.reflect.Proxy
   * @purpose  Test interface
   */
  interface OverloadedInterface {
    
    /**
     * Overloaded method.
     *
     */
    #[@overloaded(signatures= array(
    #  array('string'),
    #  array('string', 'string'),
    #  array('string', 'string')
    #))]
    public function overloaded();
  }
?>
