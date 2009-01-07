<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Encapsulates the SystemException which contains an error-code
   * and the error message.
   *
   * @see   xp://lang.XPException
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
      parent::__construct($message);
      $this->code= $code;
    }
  }
?>
