<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Dependency tracker
   *
   * @purpose  Save dependencies
   * @deprecated
   */
  class DependencyTracker extends Object {
    var
      $deps=    array();
      
    /**
     * Initializes this dependency tracker
     *
     * @access  public
     */
    function initialize() { 
    }
    
    /**
     * Finalizes this dependency tracker
     *
     * @access  public
     */
    function finalize() { 
    }
    
    /**
     * Add a dependency
     *
     * @access  public
     * @param   string target
     * @param   &org.apache.xml.generator.Dependency dep
     * @return  &org.apache.xml.generator.Dependency the added dependency
     */
    function &addDependency($target, &$dep) {
    
      if (!isset($this->deps[$target])) {
        $this->deps[$target]= array();
      }

      // New dependency
      $this->deps[$target][$dep->name]= &$dep;
      return $dep;
    }
  
    /**
     * Update the dependency
     *
     * @access  public
     * @param   string target
     * @param   int time
     * @return  bool success
     */
    function updateDependency($target, $time) {
      foreach (array_keys($this->deps) as $key) {
        if (isset($this->deps[$key][$target])) {
          $this->deps[$key][$target]->touch($time);
          return TRUE;
        }
      }
      return FALSE;
    }

    /**
     * Get all dependencies by a given target
     *
     * @access  public
     * @param   string target
     * @return  &org.apache.xml.generator.Dependency[]
     */
    function getDependencies($target) {
      return (isset($this->deps[$target])
        ? $this->deps[$target] 
        : array()
      );
    }
  }
?>
