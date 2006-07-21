<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * CryptoException
   *
   * @see      xp://security.crypto.CryptoKey
   * @purpose  This exception indicates one of a variety of public/private key problems.
   */
  class CryptoException extends Exception {
    public
      $errors = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   string[] errors default array()
     */
    public function __construct($message, $errors= array()) {
      parent::__construct($message);
      $this->errors= $errors;
    }
  
    /**
     * Returns errors
     *
     * @access  public
     * @return  string[] errors
     */
    public function getErrors() {
      return $this->errors;
    }
    
    /**
     * Return formatted output of stacktrace
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString()."\n".implode("\n  @", $this->errors)."\n";
    }
  }
?>
