<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  namespace name::kiesel::pxl::scriptlet::wrapper;

  ::uses(
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
    'scriptlet.xml.workflow.checkers.RegexpChecker'    
  );

  /**
   * Wrapper for LoginHandler
   * Handler
   * 
   * @see      xp://name.kiesel.pxl.scriptlet.handler.LoginHandler
   * @purpose  Wrapper
   */
  class LoginWrapper extends scriptlet::xml::workflow::Wrapper {

    /**
     * Constructor
     *
     */  
    public function __construct() {
      $this->registerParamInfo(
        'username',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.RegexpChecker', '/^[a-z0-9\.\_]{3,}$/')
      );
      $this->registerParamInfo(
        'password',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.RegexpChecker', '/^[A-Za-z0-9\.\_]{3,}$/')
      );
    }

    /**
     * Returns the value of the parameter username
     *
     * @return  string
     */
    public function getUsername() {
      return $this->getValue('username');
    }

    /**
     * Returns the value of the parameter password
     *
     * @return  string
     */
    public function getPassword() {
      return $this->getValue('password');
    }

  }
?>
