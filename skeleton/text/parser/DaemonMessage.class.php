<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('peer.mail.Message');

  define('DAEMON_UNKNOWN',       'unknownfailure');
  define('DAEMON_GENERIC',       'genericfailure');
  define('DAEMON_LOCALPART',     'localpartunknown');
  define('DAEMON_QUOTA',         'quotaexceeded');
  define('DAEMON_RELAYING',      'relayingdenied');
  define('DAEMON_NOROUTE',       'noroutetohost');
  define('DAEMON_SMTPCONN',      'smtpconnfailure');
  define('DAEMON_UNROUTEABLE',   'unrouteable');
  define('DAEMON_DELAYED',       'delayed');
  
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
    public 
      $failed=   NULL,
      $reason=   '',
      $details=  array(),
      $status=   DAEMON_UNKNOWN;
      
    /**
     * Set the recipient the message was destined for
     *
     * @param   peer.mail.InternetAddress r
     */
    public function setFailedRecipient($r) {
      $this->failed= $r;
    }
    
    /**
     * Get the recipient the message was destined for
     *
     * @return  peer.mail.InternetAddress
     */
    public function getFailedRecipient() {
      return $this->failed;
    }
    
    /**
     * Set the reason the message failed for
     *
     * @param   string reason
     */
    public function setReason($reason) {
      $this->reason= $reason;
    }
    
    /**
     * Get the reason the message failed for
     *
     * @return  string
     */
    public function getReason() {
      return $this->reason;
    }
  }
?>
