<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.casters.ToBoolean',
    'scriptlet.xml.workflow.casters.ToDate',
    'scriptlet.xml.workflow.casters.ToInteger',
    'scriptlet.xml.workflow.casters.ToTrimmedString',
    'scriptlet.xml.workflow.checkers.LengthChecker',
    'scriptlet.xml.workflow.checkers.RegexpChecker'
  );

  /**
   * Wrapper for EditEventHandler
   * Handler class
   * 
   * @see      xp://de.uska.scriptlet.handler.EditEventHandler
   * @purpose  Wrapper
   */
  class EditEventWrapper extends Wrapper {

    /**
     * Constructor
     *
     * @access  public
     */  
    public function __construct() {
      $this->registerParamInfo(
        'event_id',
        OCCURRENCE_PASSBEHIND | OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'team',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToInteger'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'event_type',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToInteger'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'name',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.LengthChecker', 4, 40)
      );
      $this->registerParamInfo(
        'description',
        OCCURRENCE_OPTIONAL,
        NULL,
        array('scriptlet.xml.workflow.casters.ToTrimmedString'),
        NULL,
        array('scriptlet.xml.workflow.checkers.LengthChecker', 15, 200)
      );
      $this->registerParamInfo(
        'target_date',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToDate'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'target_time',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        array('scriptlet.xml.workflow.checkers.RegexpChecker', '/^\d{1,2}[:\.\-]\d{1,2}$/'),
        NULL
      );
      $this->registerParamInfo(
        'deadline_date',
        OCCURRENCE_OPTIONAL,
        NULL,
        array('scriptlet.xml.workflow.casters.ToDate'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'deadline_time',
        OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        array('scriptlet.xml.workflow.checkers.RegexpChecker', '/^\d{1,2}[:\.\-]\d{1,2}$/'),
        NULL
      );
      $this->registerParamInfo(
        'max',
        OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'req',
        OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'guests',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToBoolean'),
        NULL,
        NULL
      );
    }

    /**
     * Returns the value of the parameter event_id
     *
     * @access  public
     * @return  int
     */
    public function getEvent_id() {
      return $this->getValue('event_id');
    }

    /**
     * Returns the value of the parameter team
     *
     * @access  public
     * @return  int
     */
    public function getTeam() {
      return $this->getValue('team');
    }

    /**
     * Returns the value of the parameter event_type
     *
     * @access  public
     * @return  int
     */
    public function getEvent_type() {
      return $this->getValue('event_type');
    }

    /**
     * Returns the value of the parameter name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->getValue('name');
    }

    /**
     * Returns the value of the parameter description
     *
     * @access  public
     * @return  string
     */
    public function getDescription() {
      return $this->getValue('description');
    }

    /**
     * Returns the value of the parameter target_date
     *
     * @access  public
     * @return  string
     */
    public function getTarget_date() {
      return $this->getValue('target_date');
    }

    /**
     * Returns the value of the parameter target_time
     *
     * @access  public
     * @return  string
     */
    public function getTarget_time() {
      return $this->getValue('target_time');
    }

    /**
     * Returns the value of the parameter deadline_date
     *
     * @access  public
     * @return  string
     */
    public function getDeadline_date() {
      return $this->getValue('deadline_date');
    }

    /**
     * Returns the value of the parameter deadline_time
     *
     * @access  public
     * @return  string
     */
    public function getDeadline_time() {
      return $this->getValue('deadline_time');
    }

    /**
     * Returns the value of the parameter max
     *
     * @access  public
     * @return  int
     */
    public function getMax() {
      return $this->getValue('max');
    }

    /**
     * Returns the value of the parameter req
     *
     * @access  public
     * @return  int
     */
    public function getReq() {
      return $this->getValue('req');
    }

    /**
     * Returns the value of the parameter guests
     *
     * @access  public
     * @return  boolean
     */
    public function getGuests() {
      return $this->getValue('guests');
    }

  }
?>
