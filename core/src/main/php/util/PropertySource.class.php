<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * PropertySource interface
   *
   */
  interface PropertySource {

    /**
     * Check whether source provides given properies
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name);

    /**
     * Load properties by given name
     *
     * @param   string name
     * @return  util.Properties
     */
    public function fetch($name);
  }
?>
