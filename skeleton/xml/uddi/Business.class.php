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
    var
      $names        = array(),
      $description  = '',
      $businessKey  = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string key
     */
    function __construct($key) {
      $this->businessKey= $key;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->names[0];
    }


    /**
     * Set Description
     *
     * @access  public
     * @param   string description
     */
    function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get Description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
    }

    /**
     * Retrieve whether this item has a description
     *
     * @access  public
     * @return  bool
     */
    function hasDescription() {
      return !empty($this->description);
    }

    /**
     * Set BusinessKey
     *
     * @access  public
     * @param   string businessKey
     */
    function setBusinessKey($businessKey) {
      $this->businessKey= $businessKey;
    }

    /**
     * Get BusinessKey
     *
     * @access  public
     * @return  string
     */
    function getBusinessKey() {
      return $this->businessKey;
    }

    
  }
?>
