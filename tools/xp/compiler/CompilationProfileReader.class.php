<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties', 'xp.compiler.CompilationProfile');

  /**
   * Reads compilation profiles from one or more property file
   *
   */
  class CompilationProfileReader extends Object {
    protected $sources= array();
    
    /**
     * Add source properties
     *
     * @param   util.Properties source
     */
    public function addSource(Properties $source) {
      $this->sources[]= $source;
    }
    
    /**
     * Get class names from a section
     *
     * @param   util.Properties source
     * @param   string section
     * @return  string[] class names
     */
    protected function getClasses($source, $section) {
      $list= $source->readSection($section);
      return isset($list['class']) ? $list['class'] : array();
    }
    
    /**
     * Get profile
     *
     * @return  xp.compiler.CompilationProfile
     */
    public function getProfile() {
      $profile= new CompilationProfile();
      foreach ($this->sources as $source) {
        foreach ($this->getClasses($source, 'warnings') as $class) {
          $profile->addWarning(XPClass::forName($class)->newInstance());
        }
        foreach ($this->getClasses($source, 'errors') as $class) {
          $profile->addError(XPClass::forName($class)->newInstance(), TRUE);
        }
        foreach ($this->getClasses($source, 'optimizations') as $class) {
          $profile->addOptimization(XPClass::forName($class)->newInstance());
        }
      }
      return $profile;
    }
  }
?>
