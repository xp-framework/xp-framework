<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Encapsulates the SystemException which contains an error-code
   * and the error message.
   *
   * @see      xp://lang.XPException
   * @purpose  Exception
   */
  class SystemException extends XPException {
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string message the error message
     * @param   int code the error code
     */
    public function __construct($message, $code) {
      parent::__construct($message);
      $this->code= $code;
    }
  }
?>
