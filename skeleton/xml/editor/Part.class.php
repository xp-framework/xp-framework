<?php
  /* Part
   *
   * $Id$
   */
   
  class Part {
    var 
      $product,
      $language,
      $href,
      $part;
      
    var
      $contents;
      
    function Part() {
      $this->product= $this->language= $this->href= $this->part= 'default';
    }
       
    function &contents() {
      if (!isset($this->contents)) {
      
        $this->contents= implode('', file($GLOBALS['XSLT_BASE'].'/src/'.$this->href.'.xsl'));
      }
      return $this->contents;
    }
    
    function destroy() {
      unset($this);
    }
  }
?>
