<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Wrapper for MethodNotImplementedException
   *
   * This exception indicates a certain class method is not
   * implemented.
   */
  class MethodNotImplementedException extends XException {
    public
      $method= '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string method
     * @see     lang.Exception#construct
     */
    public function __construct($message, $method) {
      $this->method= $method;
      parent::__construct($message);
    }
    
    /**
     * Get string representation
     *
     * @access  public
     * @return  string stacktrace
     */
    public function toString() {
      return parent::toString()."\n  [method: {$this->method}]\n";
    }
  }
?>
