<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
      
    function toPDF() {
      return 
        $this->number.' '.$this->generation." obj\n".
        "<< /Type /Info\n".
        "/Producer (" + $this->producer + ") >>\nendobj\n";
    }
  }
?>
