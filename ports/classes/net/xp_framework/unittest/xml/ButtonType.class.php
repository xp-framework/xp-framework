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
    var
      $id       = '',
      $caption  = '';


    /**
     * Set ID
     *
     * @access  public
     * @param   string id
     */
    #[@xmlmapping(element= '@id')]
    function setId($id) {
      $this->id= $id;
    }

    /**
     * Get ID
     *
     * @access  public
     * @return  string id
     */
    #[@xmlfactory(element= '@id')]
    function getId() {
      return $this->id;
    }

    /**
     * Set caption
     *
     * @access  public
     * @param   string caption
     */
    #[@xmlmapping(element= '.')]
    function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get caption
     *
     * @access  public
     * @param   string caption
     */
    #[@xmlfactory(element= '.')]
    function getCaption() {
      return $this->caption;
    }  
  }
?>
