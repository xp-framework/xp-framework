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
     * @access  public
     * @param   string message
     * @param   string detail
     */
    public function __construct($message, $detail) {
      $this->detail= $detail;
      parent::__construct($message);
    }
  
    /**
     * Get string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString().'  ['.$this->detail."]\n";
    }
  }
?>
