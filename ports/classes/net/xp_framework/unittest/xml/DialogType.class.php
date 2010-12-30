<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.xml.ButtonType', 'util.collections.Vector');

  /**
   * Test class for Marshaller / Unmarshaller tests
   *
   * @see      xp://net.xp_framework.unittest.xml.UnmarshallerTest
   * @see      xp://net.xp_framework.unittest.xml.MarshallerTest
   * @see      rfc://0040
   * @purpose  Test class
   */
  class DialogType extends Object {
    public
      $id       = '',
      $caption  = '',
      $buttons  = NULL,
      $flags    = array(),
      $options  = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->buttons= create('new util.collections.Vector<net.xp_framework.unittest.xml.ButtonType>()');
    }

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
    #[@xmlmapping(element= 'caption')]
    public function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get caption
     *
     * @param   string caption
     */
    #[@xmlfactory(element= 'caption')]
    public function getCaption() {
      return $this->caption;
    }
    
    /**
     * Add a button
     *
     * @param   net.xp_framework.unittest.xml.ButtonType button
     * @return  net.xp_framework.unittest.xml.ButtonType the added button
     */
    #[@xmlmapping(element= 'button', class= 'net.xp_framework.unittest.xml.ButtonType')]
    public function addButton($button) {
      $this->buttons->add($button);
      return $button;
    }
    
    /**
     * Returns number of buttons
     *
     * @return  int
     */
    public function numButtons() {
      return $this->buttons->size();
    }

    /**
     * Returns button at a given position
     *
     * @param   int
     * @return  net.xp_framework.unittest.xml.ButtonType 
     */
    public function buttonAt($offset) {
      return $this->buttons->get($offset);
    }

    /**
     * Returns whether buttons exist
     *
     * @return  int
     */
    public function hasButtons() {
      return !$this->buttons->isEmpty();
    }
    
    /**
     * Retrieve this dialog's buttons
     *
     * @return  util.collections.Vector<net.xp_framework.unittest.xml.ButtonType>
     */
    #[@xmlfactory(element= 'button')]
    public function getButtons() {
      return $this->buttons;
    }
    
    /**
     * Set flags
     *
     */
    #[@xmlmapping(element= 'flags', pass= array('substring-before(., "|")', 'substring-after(., "|")'))]
    public function setFlags($flag1, $flag2) {
      $this->flags= array($flag1, $flag2);
    }
    
    /**
     * Get flags
     *
     * @return array
     */
    #[@xmlfactory(element= 'flags')]
    public function getFlags() {
      return $this->flags;
    }
    
    /**
     * Set options
     *
     */
    #[@xmlmapping(element= 'options/option', pass= array('@name', '@value'))]
    public function setOptions($name, $value) {
      $this->options[$name]= $value;
    }
    
    /**
     * Get options
     *
     * @return array
     */
    #[@xmlfactory(element= 'options')]
    public function getOptions() {
      return $this->options;
    }
    
    /**
     * Returns whether another object is equal to this value object
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $this->id === $cmp->id &&
        $this->caption === $cmp->caption &&
        $this->options === $cmp->options &&
        $this->flags === $cmp->flags &&
        $this->buttons->equals($cmp->buttons)
      );
    }
  }
?>
