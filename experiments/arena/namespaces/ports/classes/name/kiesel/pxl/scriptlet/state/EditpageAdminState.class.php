<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::scriptlet::state;

  ::uses(
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
  class EditpageAdminState extends name::kiesel::pxl::scriptlet::AbstractPxlState {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->addHandler(new name::kiesel::pxl::scriptlet::handler::NewPageHandler());
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
