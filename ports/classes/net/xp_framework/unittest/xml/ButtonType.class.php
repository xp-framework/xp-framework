<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Test class for Marshaller / Unmarshaller tests. Used by
   * DialogType.
   *
   * @see      xp://net.xp_framework.unittest.xml.DialogType
   * @purpose  Test class
   */
  class ButtonType extends Object {
    public
      $id       = '',
      $caption  = '';

    /**
     * Set ID
     *
     * @param   string id
     */
    #[@xmlmapping(element= '@id')]
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get ID
     *
     * @return  string id
     */
    #[@xmlfactory(element= '@id')]
    public function getId() {
      return $this->id;
    }

    /**
     * Set caption
     *
     * @param   string caption
     */
    #[@xmlmapping(element= '.')]
    public function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get caption
     *
     * @param   string caption
     */
    #[@xmlfactory(element= '.')]
    public function getCaption() {
      return $this->caption;
    }  
  }
?>
