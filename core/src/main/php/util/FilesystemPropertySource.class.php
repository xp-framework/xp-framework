<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.PropertySource');

  /**
   * Filesystem-based property source
   *
   * @test  xp://net.xp_framework.unittest.util.FilesystemPropertySourceTest
   */
  class FilesystemPropertySource extends Object implements PropertySource {
    protected $cache= array();

    /**
     * Constructor
     *
     * @param   string path
     */
    public function __construct($path) {
      $this->root= realpath($path);
    }

    /**
     * Check whether source provides given properies
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name) {
      if (isset($this->cache[$name])) return TRUE;
      return file_exists($this->root.DIRECTORY_SEPARATOR.$name.'.ini');
    }

    /**
     * Load properties by given name
     *
     * @param   string name
     * @return  util.Properties
     * @throws  lang.IllegalArgumentException if property requested is not available
     */
    public function fetch($name) {
      if (!$this->provides($name))
        throw new IllegalArgumentException('No properties '.$name.' found at '.$this->root);

      if (!isset($this->cache[$name])) {
        $this->cache[$name]= new Properties($this->root.DIRECTORY_SEPARATOR.$name.'.ini');
      }
      
      return $this->cache[$name];
    }

    /**
     * Returns hashcode for this source
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->root);
    }

    /**
     * Check if this instance equals another
     *
     * @param   Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->root === $this->root;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->root.'>';
    }
  }
?>
