<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('PDFObject');
  
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PDFPages extends PDFObject {
    var
      $children=    array();
    
    function addChild(&$page) {
      $this->children[]= &$page;
    }
    
    function getCount() {
      return sizeof($this->children);
    }
  }
?>
