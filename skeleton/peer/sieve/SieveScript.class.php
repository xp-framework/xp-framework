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
     * @param   bool active
     */
    public function setActive($active) {
      $this->active= $active;
    }

    /**
     * Get Active
     *
     * @return  bool
     */
    public function isActive() {
      return $this->active;
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Set Code
     *
     * @param   string code
     */
    public function setCode($code) {
      $this->code= $code;
    }

    /**
     * Get Code
     *
     * @return  string
     */
    public function getCode() {
      return $this->code;
    }

    /**
     * Get length of code
     *
     * @return  int
     */
    public function getLength() {
      return strlen($this->code);
    }
  }
?>
