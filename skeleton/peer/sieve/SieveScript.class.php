<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent a single SIEVE script
   *
   * @see      xp://peer.sieve.SieveClient
   * @purpose  Wrappper
   */
  class SieveScript extends Object {
    var
      $name     = '',
      $code     = '',
      $active   = FALSE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string code
     */
    function __construct($name, $code= NULL) {
      $this->name= $name;
      $this->code= $code;
    }

    /**
     * Set Active
     *
     * @access  public
     * @param   bool active
     */
    function setActive($active) {
      $this->active= $active;
    }

    /**
     * Get Active
     *
     * @access  public
     * @return  bool
     */
    function isActive() {
      return $this->active;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Set Code
     *
     * @access  public
     * @param   string code
     */
    function setCode($code) {
      $this->code= $code;
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  string
     */
    function getCode() {
      return $this->code;
    }

    /**
     * Get length of code
     *
     * @access  public
     * @return  int
     */
    function getLength() {
      return strlen($this->code);
    }
  }
?>
