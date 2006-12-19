<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Wrapper'
  );

  /**
   * Wrapper for AccountEventHandler
   * Handler class
   * 
   * @see      xp://de.uska.scriptlet.handler.AccountEventHandler
   * @purpose  Wrapper
   */
  class AccountEventWrapper extends Wrapper {

    /**
     * Constructor
     *
     * @access  public
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
        'points',
        OCCURRENCE_MULTIPLE,
        NULL,
        NULL,
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
     * Returns the value of the parameter points
     *
     * @access  public
     * @return  int[]
     */
    public function getPoints() {
      return $this->getValue('points');
    }

  }
?>
