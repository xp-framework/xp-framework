<?php
/* This class is part of the XP framework
 *
 * $Id: ProcessorTarget.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog::io;

  /**
   * Represents a target
   *
   * @see      xp://de.thekid.dialog.io.ImageProcessor
   * @purpose  Utility class for ImageProcessor
   */
  class ProcessorTarget extends lang::Object {
    public
      $method       = '',
      $destination  = '',
      $applyFilters = TRUE;

    /**
     * Constructor
     *
     * @param   string method
     * @param   string destination
     * @param   bool applyFilters default TRUE
     */
    public function __construct($method, $destination, $applyFilters= TRUE) {
      $this->method= $method;
      $this->destination= $destination;
      $this->applyFilters= $applyFilters;
    }

    /**
     * Set method
     *
     * @param   string method
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get method
     *
     * @return  string
     */
    public function getMethod() {
      return $this->method;
    }

    /**
     * Set destination
     *
     * @param   string destination
     */
    public function setDestination($destination) {
      $this->destination= $destination;
    }

    /**
     * Get destination
     *
     * @return  string
     */
    public function getDestination() {
      return $this->destination;
    }

    /**
     * Set whether to apply filters on this target
     *
     * @param   bool applyFilters
     */
    public function setApplyFilters($applyFilters) {
      $this->applyFilters= $applyFilters;
    }

    /**
     * Get whether to apply filters on this target
     *
     * @return  bool
     */
    public function getApplyFilters() {
      return $this->applyFilters;
    }
  }
?>
