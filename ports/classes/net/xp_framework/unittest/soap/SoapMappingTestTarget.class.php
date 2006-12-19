<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Dummy class for testing purposes only.
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SoapMappingTestTarget extends Object {
    public
      $string     = '',
      $integer    = 0;  

    /**
     * Constructor.
     *
     * @access  public
     * @param   string string default ''
     * @param   integer integer default 0
     */
    public function __construct($string= '', $integer= 0) {
      $this->string= $string;
      $this->integer= $integer;
    }

    /**
     * Set String
     *
     * @access  public
     * @param   string string
     */
    public function setString($string) {
      $this->string= $string;
    }

    /**
     * Get String
     *
     * @access  public
     * @return  string
     */
    public function getString() {
      return $this->string;
    }

    /**
     * Set Integer
     *
     * @access  public
     * @param   int integer
     */
    public function setInteger($integer) {
      $this->integer= $integer;
    }

    /**
     * Get Integer
     *
     * @access  public
     * @return  int
     */
    public function getInteger() {
      return $this->integer;
    }
  }
?>
