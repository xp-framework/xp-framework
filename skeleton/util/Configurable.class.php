<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Denotes a class is configurable - that is, an util.Properties object
   * can be passed to its instance.
   *
   * @see      xp://util.Properties
   * @purpose  Interface
   */
  class Configurable extends Interface {
  
    /**
     * Configure
     *
     * @access  public
     * @param   &util.Properties properties
     * @return  bool
     */
    function configure(&$properties) { }
  }
?>
