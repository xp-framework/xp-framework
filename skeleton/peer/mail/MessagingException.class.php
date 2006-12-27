<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * MessagingException
   *
   * @purpose  Indicate a general messaging error has occured
   */
  class MessagingException extends XPException {
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
