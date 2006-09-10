<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'name.kiesel.pxl.scriptlet.AbstractPxlState',
    'name.kiesel.pxl.scriptlet.handler.NewPageHandler'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AdminState extends AbstractPxlState {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->addHandler(new NewPageHandler());
    }  
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function requiresAuthentication() {
      return TRUE;
    }    
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function process(&$request, &$response, &$context) {
      
    }
  }
?>
