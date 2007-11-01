<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.casters.ToEmailAddress',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
    'scriptlet.xml.workflow.checkers.LengthChecker',
    'scriptlet.xml.workflow.checkers.LengthChecker',
    'scriptlet.xml.workflow.checkers.LengthChecker',
    'scriptlet.xml.workflow.checkers.RegexpChecker'    
  );

  /**
   * Wrapper for EditPlayerHandler
   * Handler class
   * 
   * @see      xp://de.uska.scriptlet.handler.EditPlayerHandler
   * @purpose  Wrapper
   */
  class EditPlayerWrapper extends Wrapper {

    /**
     * Constructor
     *
     */  
    public function __construct() {
      $this->registerParamInfo(
        'player_id',
        OCCURRENCE_OPTIONAL | OCCURRENCE_PASSBEHIND,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'firstname',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.LengthChecker', 2)
      );
      $this->registerParamInfo(
        'lastname',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.LengthChecker', 3)
      );
      $this->registerParamInfo(
        'username',
        OCCURRENCE_OPTIONAL,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.LengthChecker', 3)
      );
      $this->registerParamInfo(
        'password',
        OCCURRENCE_OPTIONAL,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.RegexpChecker', '/^[A-Za-z0-9\.\_]{3,}$/')
      );
      $this->registerParamInfo(
        'email',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToEmailAddress'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'team_id',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'position',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'mailinglist',
        OCCURRENCE_MULTIPLE | OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
    }

    /**
     * Returns the value of the parameter player_id
     *
     * @return  int
     */
    public function getPlayer_id() {
      return $this->getValue('player_id');
    }

    /**
     * Returns the value of the parameter firstname
     *
     * @return  string
     */
    public function getFirstname() {
      return $this->getValue('firstname');
    }

    /**
     * Returns the value of the parameter lastname
     *
     * @return  string
     */
    public function getLastname() {
      return $this->getValue('lastname');
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

    /**
     * Returns the value of the parameter email
     *
     * @return  string
     */
    public function getEmail() {
      return $this->getValue('email');
    }

    /**
     * Returns the value of the parameter team_id
     *
     * @return  int
     */
    public function getTeam_id() {
      return $this->getValue('team_id');
    }

    /**
     * Returns the value of the parameter position
     *
     * @return  int
     */
    public function getPosition() {
      return $this->getValue('position');
    }

    /**
     * Returns the value of the parameter mailinglist
     *
     * @return  int[]
     */
    public function getMailinglist() {
      return $this->getValue('mailinglist');
    }

  }
?>
