<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.transform.StyleSheet');

  /**
   * Represents a stylesheet
   *
   * @purpose  Stylesheet
   */
  class SaxStyleSheet extends StyleSheet {
    const
      SAX_FROM_FILE = 0x0000,
      SAX_FROM_STRING = 0x0001;

    public
      $origin = 0,
      $buffer = '';
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   int origin
     * @param   string buffer
     */
    protected function __construct($origin, $buffer) {
      $this->origin= $origin;
      $this->buffer= $buffer;
      parent::__construct();
    }
    
    /**
     * Creates a new stylesheet from a file
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &xml.transform.StyleSheet
     */  
    public static function fromFile(&$file) { 
      return new SaxStyleSheet(SAX_FROM_FILE, $file->getURI());
    }
    
    /**
     * Creates a new stylesheet from a tree
     *
     * @model   static
     * @access  public
     * @param   &xml.Tree tree
     * @return  &xml.transform.StyleSheet
     */  
    public static function fromTree(&$tree) { 
      return SaxStyleSheet::fromString($tree->getSource());
    }
    
    /**
     * Creates a new stylesheet from a DOM document
     *
     * @model   static
     * @access  public
     * @param   &php.DomDocument dom
     * @return  &xml.transform.StyleSheet
     */  
    public static function fromDocument(&$dom) { 
      if (!($d= domxml_xslt_stylesheet_doc($dom))) {
        throw (new XMLFormatException('Could not create XSLDOM from dom document'));
      }
      return SaxStyleSheet::fromString($d->dump_mem());
    }
    
    /**
     * Creates a new stylesheet from a string
     *
     * @model   static
     * @access  public
     * @param   string string
     * @return  &xml.transform.StyleSheet
     */  
    public static function fromString($string) { 
      return new SaxStyleSheet(SAX_FROM_STRING, $string);
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() { 
      if (SAX_FROM_STRING == $this->type) {
        return $this->buffer; 
      } else {
        return file_get_contents($this->buffer);
      }
    }
  }
?>
