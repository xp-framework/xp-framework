<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a target
   *
   * @see      xp://de.thekid.dialog.io.ImageProcessor
   * @purpose  Utility class for ImageProcessor
   */
  class ProcessorTarget extends Object {
    var
      $method      = '',
      $destination = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string method
     * @param   string destination
     */
    function __construct($method, $destination) {
      $this->method= $method;
      $this->destination= $destination;
    }

    /**
     * Set method
     *
     * @access  public
     * @param   string method
     */
    function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get method
     *
     * @access  public
     * @return  string
     */
    function getMethod() {
      return $this->method;
    }

    /**
     * Set destination
     *
     * @access  public
     * @param   string destination
     */
    function setDestination($destination) {
      $this->destination= $destination;
    }

    /**
     * Get destination
     *
     * @access  public
     * @return  string
     */
    function getDestination() {
      return $this->destination;
    }
  }
?>
