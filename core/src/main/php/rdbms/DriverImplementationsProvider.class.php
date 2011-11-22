<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Driver implementations provider for driver manager
   *
   * @see   xp://rdbms.DriverPreferences
   */
  interface DriverImplementationsProvider {
    
    /**
     * Returns an array of class names implementing a given driver
     *
     * @param   string driver
     * @return  string[] implementations
     */
    public function implementationsFor($driver);
  }
?>
