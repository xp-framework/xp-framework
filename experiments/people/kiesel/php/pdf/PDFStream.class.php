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
  class PDFStream extends PDFObject {
    var
      $data=    '',
      $filter=  NULL;
    
    function addFiler(&$filter) {
      // XXX TBI
    }

    function set($data) {
      $this->data= $data;
    }
    
    function append($data) {
      $this->data.= $data;
    }
    
    function toPDF() {
      $s=
        $this->getObjectDeclaration().
        "/Length ".strlen($data)."\n".
        ">>\n".
        "stream\n".
        $this->data."\n".
        "endstream\n".
        "endobj\n";
      
      return $s;
    }
  }
?>
