<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicate an error has occured
   *
   * @see      xp://org.apache.xml.workflow.Context
   * @purpose  Exception
   */
  class ContextFailedException extends XPException {
    public
      $cause    = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   &lang.Exception cause
     */
    public function __construct($message, &$cause) {
      $this->cause= $cause;
      parent::__construct($message);
    }
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString()."\n  [caused by ".$this->cause->toString().']';
    }
  }
?>
