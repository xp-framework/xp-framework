<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.PropertySource');

  /**
   * Filesystem-based property source
   *
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
     * @throws  io.IOException in case file does not exist
     */
    public function fetch($name) {
      if (!$this->provides($name))
        throw new IOException('No properties '.$name.' found at '.$this->root);

      if (!isset($this->cache[$name])) {
        $this->cache[$name]= new Properties($this->root.DIRECTORY_SEPARATOR.$name.'.ini');
      }
      
      return $this->cache[$name];
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
  }
?>
