<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.PropertySource');

  /**
   * Memory-based property source
   *
   * @test     xp://net.xp_framework.unittest.MemoryPropertySourceTest
   */
  class MemoryPropertySource extends Object implements PropertySource {
    protected $props= array();

    /**
     * Check for properties
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name) {
      return isset($this->props[$name]);
    }

    /**
     * Retrieve properties
     *
     * @param   string name
     * @return  util.PropertyAccess
     */
    public function fetch($name) {
      return $this->props[$name];
    }

    /**
     * Register properties
     *
     * @param   string name
     * @param   util.PropertyAccess p
     */
    public function register($name, PropertyAccess $p) {
      $this->props[$name]= $p;
    }
  }
?>
