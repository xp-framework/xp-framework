<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.casters.ToEmailAddress',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
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
     * @access  public
     */  
    function __construct() {
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
     * @access  public
     * @return  int
     */
    function getPlayer_id() {
      return $this->getValue('player_id');
    }

    /**
     * Returns the value of the parameter firstname
     *
     * @access  public
     * @return  string
     */
    function getFirstname() {
      return $this->getValue('firstname');
    }

    /**
     * Returns the value of the parameter lastname
     *
     * @access  public
     * @return  string
     */
    function getLastname() {
      return $this->getValue('lastname');
    }

    /**
     * Returns the value of the parameter username
     *
     * @access  public
     * @return  string
     */
    function getUsername() {
      return $this->getValue('username');
    }

    /**
     * Returns the value of the parameter password
     *
     * @access  public
     * @return  string
     */
    function getPassword() {
      return $this->getValue('password');
    }

    /**
     * Returns the value of the parameter email
     *
     * @access  public
     * @return  string
     */
    function getEmail() {
      return $this->getValue('email');
    }

    /**
     * Returns the value of the parameter team_id
     *
     * @access  public
     * @return  int
     */
    function getTeam_id() {
      return $this->getValue('team_id');
    }

    /**
     * Returns the value of the parameter position
     *
     * @access  public
     * @return  int
     */
    function getPosition() {
      return $this->getValue('position');
    }

    /**
     * Returns the value of the parameter mailinglist
     *
     * @access  public
     * @return  int[]
     */
    function getMailinglist() {
      return $this->getValue('mailinglist');
    }

  }
?>
