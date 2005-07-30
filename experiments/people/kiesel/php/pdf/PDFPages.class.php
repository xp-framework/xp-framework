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
      $parent=      NULL,
      $children=    array();
    
    function addChild(&$page) {
      $this->children[]= &$page;
    }
    
    function getCount() {
      return sizeof($this->children);
    }
    
    function toPDF() {
      $s= $this->getObjectDeclaration();
      $s.= "/Type /Pages\n";
      if ($parent) {
        $s.= "/Parent ".$this->parent->getReference()."\n";
      }
      
      $s.= "/Kids [\n";
      foreach (array_keys($this->children) as $c) {
        $s.= $this->children[$c]->getReference()."\n";
      }
      $s.= "]\n";
      
      // Count the number of leaf nodes (recursively)
      $s.= '/Count '.$this->getCount()."\n";

      $s.= $this->getObjectEndDeclaration();
      return $s;
    }
  }
?>
