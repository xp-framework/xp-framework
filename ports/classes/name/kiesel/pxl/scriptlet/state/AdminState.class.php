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
     * @param   
     * @return  
     */
    public function __construct() {
      $this->addHandler(new NewPageHandler());
    }  
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function requiresAuthentication() {
      return TRUE;
    }    
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function process($request, $response, $context) {
      parent::process($request, $response, $context);
    }
  }
?>
