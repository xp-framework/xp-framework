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
    var
      $crs  = array();
      
    /**
     * Called to initialize this resource manager
     *
     * @access  public
     */
    function initialize() {
    }
    
    /**
     * Get a context resource by name, creating it if necessary.
     *
     * @see     xp://org.apache.xml.workflow.ContextResource
     * @access  public
     * @param   string name
     * @return  &org.apache.xml.workflow.ContextResource
     */
    function &getContextResource($name) {
      if (!isset($this->crs[$name])) {
        $this->crs[$name]= &new ContextResource($name);
      }
      
      return $this->crs[$name];
    }
  }
?>
