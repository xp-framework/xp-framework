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
    public
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
    public function __construct($name, $code= NULL) {
      $this->name= $name;
      $this->code= $code;
    }

    /**
     * Set Active
     *
     * @access  public
     * @param   bool active
     */
    public function setActive($active) {
      $this->active= $active;
    }

    /**
     * Get Active
     *
     * @access  public
     * @return  bool
     */
    public function isActive() {
      return $this->active;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Set Code
     *
     * @access  public
     * @param   string code
     */
    public function setCode($code) {
      $this->code= $code;
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  string
     */
    public function getCode() {
      return $this->code;
    }

    /**
     * Get length of code
     *
     * @access  public
     * @return  int
     */
    public function getLength() {
      return strlen($this->code);
    }
  }
?>
