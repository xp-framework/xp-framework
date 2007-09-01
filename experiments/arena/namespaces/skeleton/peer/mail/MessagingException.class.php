<?php
/* This class is part of the XP framework
 *
 * $Id: MessagingException.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::mail;

  /**
   * MessagingException
   *
   * @purpose  Indicate a general messaging error has occured
   */
  class MessagingException extends lang::XPException {
    public
      $detail = '';
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   string detail
     */
    public function __construct($message, $detail) {
      parent::__construct($message);
      $this->detail= $detail;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        'Exception %s (%s, %s)',
        $this->getClassName(),
        $this->message,
        $this->detail
      );
    }
  }
?>
