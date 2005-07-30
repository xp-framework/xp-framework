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
  class PDFPage extends PDFObject {
    var
      $parent=      NULL,
      $width=       0,
      $height=      0,
      $contents=    NULL;
    
    function __construct($number, $width, $height) {
      parent::__construct($number);
      $this->width= $width;
      $this->height= $height;
    }
    
    function &create($number, $width, $height, &$parent, &$resources) {
      $page= &new PDFPage($number, $width, $height);
      $page->setParent($parent);
      $page->setResources($resources);
      return $page;
    }
    
    function setParent(&$parent) {
      $this->parent= &$parent;
    }
    
    function setResources(&$resources) {
      $this->resources= &$resources;
    }
    
    function setContent(&$pdfstream) {
      $this->content= &$pdfstream;
    }
    
    function toPDF() {
      $s.= 
        $this->getObjectDeclaration().
        "/Type /Page\n".
        "/Parent ".$this->parent->getReference()."\n".
        "/MediaBox [ 0 0 ".$this->width." ".$this->height." ]\n".
        "/Resources ".$this->resources->getReference()."\n".
        "/Contents ".$this->contents->getReference()."\n".
        $this->getObjectEndDeclaration();
      
      return $s;
    }
  }
?>
