<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'unittest.web';
  
  uses('unittest.web.Field');

  /**
   * Represents a HTML Select field
   *
   * @see      http://www.w3schools.com/TAGS/tag_Select.asp
   * @see      http://www.w3schools.com/TAGS/tag_option.asp
   * @see      xp://unitform.web.Field
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
        return $child->getAttribute('value');
      }
      
      // Use first child's value
      return $this->node->childNodes->item(0)->getAttribute('value');
    }

    /**
     * Set this field's value
     *
     * @param   string value
     */
    public function setValue($value) {
      $found= FALSE;
      foreach ($this->node->childNodes as $child) {
        if ($value !== $child->getAttribute('value')) {
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
