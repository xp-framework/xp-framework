<?php
/* This class is part of the XP framework
 *
 * $Id: DirectoryCategory.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::google::soap::search;

  /**
   * Type wrapper
   *
   * @purpose  Specialized SOAP type
   */
  class DirectoryCategory extends lang::Object {
    public
      $fullViewableName,
      $specialEncoding;

    /**
     * Retrieves fullViewableName
     *
     * @return  string 
     */
    public function getFullViewableName() {
      return $this->fullViewableName;
    }

    /**
     * Sets fullViewableName
     *
     * @param   string fullViewableName
     */
    public function setFullViewableName($fullViewableName) {
      $this->fullViewableName= $fullViewableName;
    }

    /**
     * Retrieves specialEncoding
     *
     * @return  string 
     */
    public function getSpecialEncoding() {
      return $this->specialEncoding;
    }

    /**
     * Sets specialEncoding
     *
     * @param   string specialEncoding
     */
    public function setSpecialEncoding($specialEncoding) {
      $this->specialEncoding= $specialEncoding;
    }
  }
?>
