<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.workflow.ContextResource'
  );

  /**
   * (Insert class' description here)
   *
   * @see      reference
   * @purpose  purpose
   */
  class ContextResourceManager extends Object {
    var
      $crs  = array();
      
    /**
     * Called to initialize this resource manager
     *
     * @access  public
     * @param   
     * @return  
     */
    function initialize() {
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getContextResource($name) {
      if (!isset($this->crs[$name])) {
        $this->crs[$name]= &new ContextResource($name);
      }
      
      return $this->crs[$name];
    }
  }
?>
