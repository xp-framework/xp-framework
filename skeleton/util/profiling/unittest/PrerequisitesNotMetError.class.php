<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('PREREQUISITE_LIBRARYMISSING', 'library.missing');
  define('PREREQUISITE_INITFAILED',     'initialization.failed');

  /**
   * Indicates prerequisites have not been met
   *
   * @purpose  Exception
   */
  class PrerequisitesNotMetError extends Exception {
    var
      $cause           = NULL,
      $prerequisites   = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   &lang.Exception cause 
     * @param   array prerequisites default array()
     * @param   string code
     */
    function __construct($message, &$cause, $prerequisites= array()) {
      $this->cause= &$cause;
      $this->prerequisites= $prerequisites;
      parent::__construct($message);
    }
    
    /**
     * Get Trace
     *
     * @access  public
     * @return  string
     */
    function getStackTrace() {
      return $this->cause ? $this->cause->getStackTrace() : $this->getStackTrace();
    }
  }
?>
