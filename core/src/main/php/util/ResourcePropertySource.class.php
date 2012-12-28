<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.PropertySource');

  /**
   * Resource-based property source
   *
   * // Read properties from the following resource: /path/to/inidirectory/test.ini provided by
   * // the default class loader
   * $src= new ResourcePropertySource('/path/to/inidirectory');
   * $p= $src->fetch('test');
   *
   * @test     xp://net.xp_framework.unittest.util.ResourcePropertySourceTest
   */
  class ResourcePropertySource extends Object implements PropertySource {
    protected $cache= array();
    protected $classLoader= NULL;
    protected $root= NULL;

    /**
     * Constructor
     *
     * @param   string path
     * @param   lang.IClassLoader cl If null, use default class loader
     */
    public function __construct($path, $cl= NULL) {
      $this->root= '/'.trim($path, '/').'/';
      $this->classLoader= (NULL === $cl ? ClassLoader::getDefault() : $cl);
    }

    /**
     * Check whether source provides given properies
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name) {
      if (isset($this->cache[$name])) return TRUE;
      return $this->classLoader->providesResource($this->root.$name.'.ini');
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
        $this->cache[$name]= Properties::fromString($this->classLoader->getResource($this->root.$name.'.ini'));
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
