<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.mail.Message');

  /**
   * DaemonMessage
   *
   * @purpose  Mailer daemom message
   */
  class DaemonMessage extends Message {
    const
      DAEMON_UNKNOWN = 'unknownfailure',
      DAEMON_GENERIC = 'genericfailure',
      DAEMON_LOCALPART = 'localpartunknown',
      DAEMON_QUOTA = 'quotaexceeded',
      DAEMON_RELAYING = 'relayingdenied',
      DAEMON_NOROUTE = 'noroutetohost',
      DAEMON_SMTPCONN = 'smtpconnfailure',
      DAEMON_UNROUTEABLE = 'unrouteable',
      DAEMON_DELAYED = 'delayed',
      DAEMON_TYPE_POSTFIX = 'inline/postfix',
      DAEMON_TYPE_TONLINE = 'inline/t-online',
      DAEMON_TYPE_QMAIL = 'inline/qmail',
      DAEMON_TYPE_EXIM = 'inline/exim',
      DAEMON_TYPE_SENDMAIL = 'inline/sendmail',
      DAEMON_TYPE_MULTIPART = 'multipart/delivery';

    public 
      $failed=   NULL,
      $reason=   '',
      $details=  array(),
      $status=   DAEMON_UNKNOWN;
      
    /**
     * Set the recipient the message was destined for
     *
     * @access  public
     * @param   &peer.mail.InternetAddress r
     */
    public function setFailedRecipient(&$r) {
      $this->failed= $r;
    }
    
    /**
     * Get the recipient the message was destined for
     *
     * @access  public
     * @return  peer.mail.InternetAddress
     */
    public function getFailedRecipient() {
      return $this->failed;
    }
    
    /**
     * Set the reason the message failed for
     *
     * @access  public
     * @param   string reason
     */
    public function setReason($reason) {
      $this->reason= $reason;
    }
    
    /**
     * Get the reason the message failed for
     *
     * @access  public
     * @return  string
     */
    public function getReason() {
      return $this->reason;
    }
  }
?>
