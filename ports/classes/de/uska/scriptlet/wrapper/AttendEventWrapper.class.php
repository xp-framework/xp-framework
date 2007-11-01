<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.casters.ToBoolean',
    'scriptlet.xml.workflow.checkers.IntegerRangeChecker',
    'scriptlet.xml.workflow.checkers.IntegerRangeChecker'    
  );

  /**
   * Wrapper for AttendEventHandler
   * Handler
   * 
   * @see      xp://de.uska.scriptlet.handler.AttendEventHandler
   * @purpose  Wrapper
   */
  class AttendEventWrapper extends Wrapper {

    /**
     * Constructor
     *
     */  
    public function __construct() {
      $this->registerParamInfo(
        'event_id',
        OCCURRENCE_PASSBEHIND,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'player_id',
        OCCURRENCE_PASSBEHIND,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'firstname',
        OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'lastname',
        OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'attend',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        NULL,
        array('scriptlet.xml.workflow.checkers.IntegerRangeChecker', 0, 2)
      );
      $this->registerParamInfo(
        'needs_seat',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToBoolean'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'offers_seats',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        NULL,
        array('scriptlet.xml.workflow.checkers.IntegerRangeChecker', 0, 10)
      );
    }

    /**
     * Returns the value of the parameter event_id
     *
     * @return  int
     */
    public function getEvent_id() {
      return $this->getValue('event_id');
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
     * Returns the value of the parameter attend
     *
     * @return  int
     */
    public function getAttend() {
      return $this->getValue('attend');
    }

    /**
     * Returns the value of the parameter needs_seat
     *
     * @return  boolean
     */
    public function getNeeds_seat() {
      return $this->getValue('needs_seat');
    }

    /**
     * Returns the value of the parameter offers_seats
     *
     * @return  int
     */
    public function getOffers_seats() {
      return $this->getValue('offers_seats');
    }

  }
?>
