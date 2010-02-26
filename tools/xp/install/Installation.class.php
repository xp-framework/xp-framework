<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.Folder'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Installation extends Object {
    protected $base;
    protected $cl= array();
    
    /**
     * Gets base folder
     *
     * @param   io.Folder
     */
    public function setBase(Folder $base) {
      $this->base= new Folder($base, '..');
      
      // Scan base path. FIXME: Refactor this to use lang.ClassLoader
      // instead of duplication its sourcecode here
      foreach (array_filter(explode(PATH_SEPARATOR, scanpath(array($base->getURI()), getenv('HOME')))) as $element) {
        $resolved= realpath($element);
        if (is_dir($resolved)) {
          $this->cl[]= FileSystemClassLoader::instanceFor($resolved, FALSE);
        } else if (is_file($resolved)) {
          $this->cl[]= ArchiveClassLoader::instanceFor($resolved, FALSE);
        }
      }
    }
    
    /**
     * Fetches a resource
     *
     * @param   string name
     * @return  string
     */
    protected function getResource($name) {
      foreach ($this->cl as $loader) {
        if ($loader->providesResource($name)) return $loader->getResource($name);
      }
      raise('lang.ElementNotFoundException', $name);
    }
    
    /**
     * Returns base folder
     *
     * @return  io.Folder
     */
    public function getBase() {
      return $this->base;
    }
    
    /**
     * Gets this installation's version number
     *
     * @return  string
     */
    public function getVersion() {
      return trim($this->getResource('VERSION'));
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@<'.xp::stringOf($this->base).'>';
    }
  }
?>
