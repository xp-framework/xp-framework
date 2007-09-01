<?php
/* This class is part of the XP framework
 *
 * $Id: AttendEventState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses('de.uska.scriptlet.state.UskaState', 'de.uska.scriptlet.handler.AttendEventHandler');

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
     * @param   
     * @return  
     */
    public function requiresAuthentication() { return TRUE; }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setup($request, $response, $context) {
      $this->addHandler(new de::uska::scriptlet::handler::AttendEventHandler());
      parent::setup($request, $response, $context);
    }
  }
?>
