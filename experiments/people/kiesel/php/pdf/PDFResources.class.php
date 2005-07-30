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
  class PDFResources extends PDFObject {
    var
      $fonts=       NULL;
    
    function __construct() {
      parent::__construct();
      $this->fonts= &new Hashmap();
    }
    
    function addFont(&$font) {
      $this->fonts->put($font->getName(), $font);
    }
    
    function toPDF() {
      $s=
        $this->getObjectDeclaration().
        $this->getObjectEndDeclaration();
      
      return $s;
    }
  }
?>
