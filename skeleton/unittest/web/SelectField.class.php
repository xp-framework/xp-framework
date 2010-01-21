<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'unittest.web';
  
  uses('unittest.web.Field', 'unittest.web.SelectOption');

  /**
   * Represents a HTML Select field
   *
   * @see      http://www.w3schools.com/TAGS/tag_Select.asp
   * @see      xp://unittest.web.Field
   * @purpose  Value object
   */
  class unittest·web·SelectField extends unittest·web·Field {

    /**
     * Get this field's value
     *
     * @return  string
     */
    public function getValue() {
      if (!$this->node->hasChildNodes()) return NULL;

      // Find selected
      foreach ($this->node->childNodes as $child) {
        if ('option' != $child->tagName || !$child->hasAttribute('selected')) continue;
        return utf8_decode($child->getAttribute('value'));
      }
      
      // Use first child's value
      return utf8_decode($this->node->childNodes->item(0)->getAttribute('value'));
    }
    
    /**
     * Returns options
     *
     * @return  unittest.web.SelectOption[]
     */
    public function getOptions() {
      $r= array();
      foreach ($this->node->childNodes as $child) {
        if ('option' != $child->tagName) continue;
        $r[]= new unittest·web·SelectOption($this->form, $child);
      }
      return $r;
    }

    /**
     * Returns selected option (or NULL if no option is selected)
     *
     * @return  unittest.web.SelectOption[]
     */
    public function getSelectedOptions() {
      $r= array();
      foreach ($this->node->childNodes as $child) {
        if ('option' != $child->tagName || !$child->hasAttribute('selected')) continue;
        $r[]= new unittest·web·SelectOption($this->form, $child);
      }
      return $r;
    }

    /**
     * Set this field's value
     *
     * @param   string value
     */
    public function setValue($value) {
      $found= FALSE;
      $search= utf8_encode($value);
      foreach ($this->node->childNodes as $child) {
        if ($search !== $child->getAttribute('value')) {
          $update[]= $child;
          continue;
        }
        $child->setAttribute('selected', 'selected');
        $found= TRUE;
      }
      
      if (!$found) throw new IllegalArgumentException('Cannot set value');
      
      foreach ($update as $child) {
        $child->removeAttribute('selected');
      }
    }
  }
?>
