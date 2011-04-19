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
     * @param   string string
     */
    public function setString($string) {
      $this->string= $string;
    }

    /**
     * Get String
     *
     * @return  string
     */
    public function getString() {
      return $this->string;
    }

    /**
     * Set Integer
     *
     * @param   int integer
     */
    public function setInteger($integer) {
      $this->integer= $integer;
    }

    /**
     * Get Integer
     *
     * @return  int
     */
    public function getInteger() {
      return $this->integer;
    }
  }
?>
