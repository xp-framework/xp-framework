<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * FPDF-Font-Klasse (Anpassung an XP-Framework)
   *
   * @see http://fpdf.org/
   */
  class FPDFFont extends Object {
    var 
      $name     = NULL,
      $index    = 0;
    var
      $family,
      $style,
      $cw,
      $type,
      $name,
      $desc,
      $up,
      $ut,
      $cw,
      $enc,
      $file,
      $originalsize;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($name) {
      $this->name= $name;
      parent::__construct();
    }
    
    function isUnderline() {
      return (FALSE !== strpos($this->style, 'U'));
    }
    
    function isBold() {
      return (FALSE !== strpos($this->style, 'B'));
    }

    function isItalic() {
      return (FALSE !== strpos($this->style, 'I'));
    }
    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function configure(&$p) {
      if (NULL == $this->name) return throw(new IllegalStateException('no name defined'));
      
      $this->cw= $p->readArray($this->name, 'cw', array());
      $this->fontname= $p->readString($this->name, 'name', $this->name);
      $this->type= $p->readString($this->name, 'type');
      $this->family= $p->readString($this->name, 'family', $this->name);
      $this->style= strtoupper($p->readString($this->name, 'style'));
      $this->enc= $p->readString($this->name, 'enc');
      $this->diff= $p->readString($this->name, 'diff');
      $this->file= $p->readString($this->name, 'file');
      $this->ut= $p->readInteger($this->name, 'ut');
      $this->up= $p->readInteger($this->name, 'up');
      $this->originalsize= $p->readInteger($this->name, 'originalsize');
      $this->desc= $p->readSection($this->name, 'desc', array());
      
      if (empty($this->type)) $this->type= '__CORE__';
    }
      
    /**
     * Die Breite eines Zeichens zurückgeben
     *
     * @access  public
     * @param   char c Zeichen
     * @return  int Breite
     */
    function getCharWidth($c) {
      return $this->charwidths[ord($c)];
    }
  }
?>
