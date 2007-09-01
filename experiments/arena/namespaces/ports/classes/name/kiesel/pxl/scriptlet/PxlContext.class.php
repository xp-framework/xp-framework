<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::scriptlet;

  ::uses('scriptlet.xml.workflow.Context');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PxlContext extends scriptlet::xml::workflow::Context {
    public
      $user   = NULL;
      
    /**
     * (Insert method's description here)
     *
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
     * @param   
     * @return  
     */
    public function setup($request) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function insertStatus($response) {
      $this->user && $response->addFormResult(::fromArray($this->user, 'user'));
    }    
  }
?>
