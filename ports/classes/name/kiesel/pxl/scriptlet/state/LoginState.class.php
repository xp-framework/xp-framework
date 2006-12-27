<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'name.kiesel.pxl.scriptlet.AbstractPxlState',
    'name.kiesel.pxl.scriptlet.handler.LoginHandler'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class LoginState extends AbstractPxlState {

    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->addHandler(new LoginHandler());
    }
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setup($request, $response, $context) {
      parent::setup($request, $response, $context);
    }
  }
?>
