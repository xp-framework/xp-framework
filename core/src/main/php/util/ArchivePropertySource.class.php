<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.PropertySource',
    'lang.archive.ArchiveClassLoader'
  );

  /**
   * Loads INI files from a XAR (XP Archive) using ArchiveClassLoader
   *
   * @purpose  Load INI files from an archive
   * @see      xp://lang.ArchiveClassLoader
   */
  class ArchivePropertySource extends Object implements PropertySource {
    protected $path= NULL;
    protected $cl= NULL;

    /**
     * Constructor
     *
     * @param   string path
     */
    public function __construct($path) {
      $this->path= $path;
      $this->cl= new ArchiveClassLoader($this->path);
    }

    /**
     * Check whether source provides given properies
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name) {
      return $this->cl->providesResource($name.'.ini');
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
        throw new IllegalArgumentException('No properties '.$name.' found at '.$this->path);

      $p= new Properties($name);
      $p->load($this->cl->getResourceAsStream($name.'.ini')->getInputStream());
      
      return $p;
    }

    /**
     * Returns hashcode for this source
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->path);
    }

    /**
     * Check if this instance equals another
     *
     * @param   Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->path === $this->path;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->path.'>';
    }
  }
?>
