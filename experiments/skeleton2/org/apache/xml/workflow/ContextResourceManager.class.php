<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.ContextResource');

  /**
   * ContextResourceManager
   *
   * @purpose  Resource Manager
   */
  class ContextResourceManager extends Object {
    public
      $crs      = array(),
      $hash     = array(),
      $storage  = NULL;

    /**
     * Set Storage
     *
     * @access  public
     * @param   &org.apache.HttpSession storage
     */
    public function setStorage(&$storage) {
      $this->storage= $storage;
    }

    /**
     * Get Storage
     *
     * @access  public
     * @return  &org.apache.HttpSession
     */
    public function getStorage() {
      return $this->storage;
    }
      
    /**
     * Called to initialize this resource manager
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    public function initialize(&$classloader) {
    }
    
    /**
     * Get a context resource by name, creating it if necessary.
     *
     * @see     xp://org.apache.xml.workflow.ContextResource
     * @access  public
     * @param   string name
     * @param   string class
     * @return  &org.apache.xml.workflow.ContextResource
     */
    public function getContextResource($name, $class= 'ContextResource') {
      if (!isset($this->hash[$name])) {
        $this->crs[]= XPClass::forName($class)->newInstance($name);
        $this->hash[$name]= sizeof($this->crs)- 1;
      } else if (!isset($this->crs[$this->hash[$name]])) {
        $this->crs[$this->hash[$name]]= $this->storage->getValue('contextresource.'.$name);
      }
      
      return $this->crs[$this->hash[$name]];
    }
    
    /**
     * Callback for serialize
     *
     * @access  magic
     * @return  string[]
     */
    public function __sleep() {
      foreach (array_keys($this->hash) as $name) {
        if (!isset($this->crs[$this->hash[$name]])) continue;   // was not loaded
        
        $this->storage->putValue('contextresource.'.$name, $this->crs[$this->hash[$name]]);
      }
      return array('hash');
    }
  }
?>
