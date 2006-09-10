<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.Context');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PxlContext extends Context {
    var
      $user   = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setUser($u) {
      $this->user= $u;
      $this->setChanged();
    }      
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setup(&$request) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function insertStatus(&$response) {
      $this->user && $response->addFormResult(Node::fromArray($this->user, 'user'));
    }    
  }
?>
