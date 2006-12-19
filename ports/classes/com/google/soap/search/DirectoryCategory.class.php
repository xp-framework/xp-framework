<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type wrapper
   *
   * @purpose  Specialized SOAP type
   */
  class DirectoryCategory extends Object {
    public
      $fullViewableName,
      $specialEncoding;

    /**
     * Retrieves fullViewableName
     *
     * @access  public
     * @return  string 
     */
    public function getFullViewableName() {
      return $this->fullViewableName;
    }

    /**
     * Sets fullViewableName
     *
     * @access  public
     * @param   string fullViewableName
     */
    public function setFullViewableName($fullViewableName) {
      $this->fullViewableName= $fullViewableName;
    }

    /**
     * Retrieves specialEncoding
     *
     * @access  public
     * @return  string 
     */
    public function getSpecialEncoding() {
      return $this->specialEncoding;
    }

    /**
     * Sets specialEncoding
     *
     * @access  public
     * @param   string specialEncoding
     */
    public function setSpecialEncoding($specialEncoding) {
      $this->specialEncoding= $specialEncoding;
    }
  }
?>
