<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.uska.scriptlet.state.UskaState', 'de.uska.scriptlet.handler.AttendEventHandler');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AttendEventState extends UskaState {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function requiresAuthentication() { return TRUE; }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function setup(&$request, &$response, &$context) {
      $this->addHandler(new AttendEventHandler());
      parent::setup($request, $response, $context);
    }
  }
?>
