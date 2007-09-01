<?php
/* This class is part of the XP framework
 *
 * $Id: SystemException.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace lang;
 
  /**
   * Encapsulates the SystemException which contains an error-code
   * and the error message.
   *
   * @see Exception
   */
  class SystemException extends XPException {
    public $code= 0;
    
    /**
     * Constructor
     *
     * @param   string message the error-message
     * @param   int code the error-code
     */
    public function __construct($message, $code) {
      $this->code= $code;
      parent::__construct($message);
    }
  }
?>
