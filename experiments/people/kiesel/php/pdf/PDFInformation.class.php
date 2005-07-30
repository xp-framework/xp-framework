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
  class PDFInformation extends PDFObject {
    var
      $producer=    '';
    
    function setProducer($p) {
      $this->producer= $p;
    }
      
    function toPDF() {
      $s= 
        $this->getObjectDeclaration().
        "/Type /Info\n".
        "/Producer (".$this->producer.")\n".
        $this->getObjectEndDeclaration();
      
      return $s;
    }
  }
?>
