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
    public
      $user   = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setUser($u) {
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
    public function setup(&$request) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function insertStatus(&$response) {
      $this->user && $response->addFormResult(Node::fromArray($this->user, 'user'));
    }    
  }
?>
