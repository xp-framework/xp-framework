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
  class PDFCatalogue extends PDFObject {
    var
      $rootPages=   NULL;
    
    function setRootPages(&$pages) {
      $this->rootPages= &$pages;
    }
    
    function toPDF() {
      $s= $this->getObjectDeclaration();
      $s.= "/Type /Catalog\n";
      $s.= "/Pages ".$this->rootPages->getReference()."\n";
      
      $s.= $this->getObjectEndDeclaration();
      return $s;
    }
  }
?>
