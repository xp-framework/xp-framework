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
    var
      $fullViewableName,
      $specialEncoding;

    /**
     * Retrieves fullViewableName
     *
     * @access  public
     * @return  string 
     */
    function getFullViewableName() {
      return $this->fullViewableName;
    }

    /**
     * Sets fullViewableName
     *
     * @access  public
     * @param   string fullViewableName
     */
    function setFullViewableName($fullViewableName) {
      $this->fullViewableName= $fullViewableName;
    }

    /**
     * Retrieves specialEncoding
     *
     * @access  public
     * @return  string 
     */
    function getSpecialEncoding() {
      return $this->specialEncoding;
    }

    /**
     * Sets specialEncoding
     *
     * @access  public
     * @param   string specialEncoding
     */
    function setSpecialEncoding($specialEncoding) {
      $this->specialEncoding= $specialEncoding;
    }
  }
?>
