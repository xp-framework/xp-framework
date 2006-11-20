<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.xml.ButtonType', 'lang.Collection');

  /**
   * Test class for Marshaller / Unmarshaller tests
   *
   * @see      xp://net.xp_framework.unittest.xml.UnmarshallerTest
   * @see      xp://net.xp_framework.unittest.xml.MarshallerTest
   * @see      rfc://0040
   * @purpose  Test class
   */
  class DialogType extends Object {
    var
      $id       = '',
      $caption  = '',
      $buttons  = NULL;

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->buttons= &Collection::forClass('net.xp_framework.unittest.xml.ButtonType');
    }

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
    #[@xmlmapping(element= 'caption')]
    function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get caption
     *
     * @access  public
     * @param   string caption
     */
    #[@xmlfactory(element= 'caption')]
    function getCaption() {
      return $this->caption;
    }
    
    /**
     * Add a button
     *
     * @access  public
     * @param   &net.xp_framework.unittest.xml.ButtonType button
     * @return  &net.xp_framework.unittest.xml.ButtonType the added button
     */
    #[@xmlmapping(element= 'button', class= 'net.xp_framework.unittest.xml.ButtonType')]
    function &addButton(&$button) {
      $this->buttons->add($button);
      return $button;
    }
    
    /**
     * Returns number of buttons
     *
     * @access  public
     * @return  int
     */
    function numButtons() {
      return $this->buttons->size();
    }

    /**
     * Returns button at a given position
     *
     * @access  public
     * @param   int
     * @return  &net.xp_framework.unittest.xml.ButtonType 
     */
    function &buttonAt($offset) {
      return $this->buttons->get($offset);
    }

    /**
     * Returns whether buttons exist
     *
     * @access  public
     * @return  int
     */
    function hasButtons() {
      return !$this->buttons->isEmpty();
    }
    
    /**
     * Retrieve this dialog's buttons
     *
     * @access  public
     * @return  &lang.Collection<net.xp_framework.unittest.xml.ButtonType>
     */
    #[@xmlfactory(element= 'button')]
    function getButtons() {
      return $this->buttons;
    }
  }
?>
