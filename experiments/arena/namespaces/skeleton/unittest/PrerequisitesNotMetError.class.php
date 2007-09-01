<?php
/* This class is part of the XP framework
 *
 * $Id: PrerequisitesNotMetError.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace unittest;

  define('PREREQUISITE_LIBRARYMISSING', 'library.missing');
  define('PREREQUISITE_INITFAILED',     'initialization.failed');

  /**
   * Indicates prerequisites have not been met
   *
   * @purpose  Exception
   */
  class PrerequisitesNotMetError extends lang::XPException {
    public
      $cause           = NULL,
      $prerequisites   = array();
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   lang.XPException cause 
     * @param   array prerequisites default array()
     * @param   string code
     */
    public function __construct($message, $cause, $prerequisites= array()) {
      $this->cause= $cause;
      $this->prerequisites= $prerequisites;
      parent::__construct($message);
    }
    
    /**
     * Get Trace
     *
     * @return  string
     */
    public function getStackTrace() {
      return $this->cause ? $this->cause->getStackTrace() : $this->getStackTrace();
    }
  }
?>
