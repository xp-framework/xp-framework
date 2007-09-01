<?php
/* This class is part of the XP framework
 *
 * $Id: Configurable.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace util;

  /**
   * Denotes a class is configurable - that is, an util.Properties object
   * can be passed to its instance.
   *
   * @see      xp://util.Properties
   * @purpose  Interface
   */
  interface Configurable {
  
    /**
     * Configure
     *
     * @param   util.Properties properties
     * @return  bool
     */
    public function configure($properties);
  }
?>
