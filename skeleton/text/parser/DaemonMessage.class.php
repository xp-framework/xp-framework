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
  define('DAEMON_RELAYING',     'relayingdenied');
  define('DAEMON_NOROUTE',      'noroutetohost');
  define('DAEMON_SMTPCONN',     'smtpconnfailure');
  define('DAEMON_UNROUTEABLE',  'unrouteable');
  define('DAEMON_DELAYED',      'delayed');
  
  define('DAEMON_TYPE_POSTFIX',  'inline/postfix');
  define('DAEMON_TYPE_TONLINE',  'inline/t-online');
  define('DAEMON_TYPE_QMAIL',    'inline/qmail');
  define('DAEMON_TYPE_EXIM',     'inline/exim');
  define('DAEMON_TYPE_SENDMAIL', 'inline/sendmail');
  define('DAEMON_TYPE_MULTIPART','multipart/delivery');
  
  /**
   * DaemonMessage
   *
   * @purpose  Mailer daemom message
   */
  class DaemonMessage extends Message {
    var 
      $failed=   NULL,
      $reason=   '',
      $details=  array(),
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
