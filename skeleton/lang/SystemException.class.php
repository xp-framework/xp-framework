<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Encapsulates the SystemException which contains an error-code
   * and the error message.
   *
   * @see Exception
   */
  class SystemException extends Exception {
    var $code= 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string message the error-message
     * @param   int code the error-code
     */
    function __construct($message, $code) {
      $this->code= $code;
      parent::__construct($message);
    }
  }
?>
