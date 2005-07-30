<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'PDFObject',
    'util.Hashmap'
  );
  
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
  }
?>
