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
      $data=    '';
    
    function set($data) {
      $this->data= $data;
    }
    
    function append($data) {
      $this->data.= $data;
    }
  }
?>
