<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates an assertion failed
   *
   * @purpose  Exception
   */
  class AssertionFailedError extends Exception {
    var
      $actual   = NULL,
      $code     = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   mixed actual
     * @param   string code
     */
    function __construct($message, $actual, $code) {
      $this->actual= $actual;
      $this->code= $code;
      parent::__construct($message);
    }
  }
?>
