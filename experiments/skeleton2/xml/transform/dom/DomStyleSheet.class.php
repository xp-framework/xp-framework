<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.transform.StyleSheet');

  /**
   * Represents a stylesheet
   *
   * @ext      domxml
   * @purpose  Stylesheet
   */
  class DomStyleSheet extends StyleSheet {
    public
      $dom  = NULL;
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   &php.DomDocument dom
     */
    protected function __construct(&$dom) {
      $this->dom= $dom;
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
      if (!($d= domxml_open_file($file->getURI()))) {
        throw (new XMLFormatException('Could not create XSLDOM from '.$file->getURI()));
      }
      return new DomStyleSheet($d);
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
      return DomStyleSheet::fromString($tree->getSource());
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
      return new DomStyleSheet($d);
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
      if (!($d= domxml_open_mem($string))) {
        throw (new XMLFormatException('Could not create XSLDOM from string'));
      }
      return new DomStyleSheet($d);
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() { 
      return $this->dom->dump_mem();
    }
  }
?>
