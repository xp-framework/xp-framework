<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('peer.mail.Message');

  define('DAEMON_UNKNOWN',      'unknownfailure');
  define('DAEMON_GENERIC',      'genericfailure');
  define('DAEMON_LOCALPART',    'localpartunknown');
  define('DAEMON_QUOTA',        'quotaexceeded');

  /**
   *
   *
   */
  class DaemonMessage extends Message {
    var 
      $failed=   NULL,
      $reason=   '',
      $status=   DAEMON_UNKNOWN;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     */
    function setFailedRecipient(&$r) {
      $this->failed= &$r;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @return  
     */
    function &getFailedRecipient() {
      return $this->failed;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     */
    function setReason($reason) {
      $this->reason= $reason;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @return  
     */
    function getReason() {
      return $this->reason;
    }
  }
?>
