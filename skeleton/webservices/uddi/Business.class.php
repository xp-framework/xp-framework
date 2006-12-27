<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Business extends Object {
    public
      $names        = array(),
      $description  = '',
      $businessKey  = '';

    /**
     * Constructor
     *
     * @param   string key
     */
    public function __construct($key) {
      $this->businessKey= $key;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->names[0];
    }


    /**
     * Set Description
     *
     * @param   string description
     */
    public function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get Description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }

    /**
     * Retrieve whether this item has a description
     *
     * @return  bool
     */
    public function hasDescription() {
      return !empty($this->description);
    }

    /**
     * Set BusinessKey
     *
     * @param   string businessKey
     */
    public function setBusinessKey($businessKey) {
      $this->businessKey= $businessKey;
    }

    /**
     * Get BusinessKey
     *
     * @return  string
     */
    public function getBusinessKey() {
      return $this->businessKey;
    }

    
  }
?>
