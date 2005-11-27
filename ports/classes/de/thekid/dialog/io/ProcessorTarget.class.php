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
      $method       = '',
      $destination  = '';
      $applyFilters = TRUE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string method
     * @param   string destination
     * @param   bool applyFilters default TRUE
     */
    function __construct($method, $destination, $applyFilters= TRUE) {
      $this->method= $method;
      $this->destination= $destination;
      $this->applyFilters= $applyFilters;
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

    /**
     * Set whether to apply filters on this target
     *
     * @access  public
     * @param   bool applyFilters
     */
    function setApplyFilters($applyFilters) {
      $this->applyFilters= $applyFilters;
    }

    /**
     * Get whether to apply filters on this target
     *
     * @access  public
     * @return  bool
     */
    function getApplyFilters() {
      return $this->applyFilters;
    }
  }
?>
