<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'unittest.web';
  
  uses('unittest.web.Field');

  /**
   * Represents a HTML input field
   *
   * @see      http://www.w3schools.com/TAGS/tag_input.asp
   * @see      xp://unittest.web.Field
   * @purpose  Value object
   */
  class unittest·web·InputField extends unittest·web·Field {

    /**
     * Get this field's value
     *
     * @return  string
     */
    public function getValue() {
      return $this->node->hasAttribute('value') ? utf8_decode($this->node->getAttribute('value')) : NULL;
    }

    /**
     * Set this field's value
     *
     * @param   string value
     */
    public function setValue($value) {
      $this->node->setAttribute('value', utf8_encode($value));
    }
  }
?>
