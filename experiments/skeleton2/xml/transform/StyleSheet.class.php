<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.XMLFormatException');

  /**
   * Represents a stylesheet
   *
   * @purpose  Abstract base class
   */
  class StyleSheet extends Object {
  
    /**
     * Creates a new stylesheet from a file
     *
     * @model   abstract
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &xml.transform.StyleSheet
     * @throws  xml.XMLFormatException
     */  
    public static function fromFile(&$file) { }
    
    /**
     * Creates a new stylesheet from a tree
     *
     * @model   abstract
     * @model   static
     * @access  public
     * @param   &xml.Tree tree
     * @return  &xml.transform.StyleSheet
     * @throws  xml.XMLFormatException
     */  
    public static function fromTree(&$tree) { }
    
    /**
     * Creates a new stylesheet from a DOM document
     *
     * @model   abstract
     * @model   static
     * @access  public
     * @param   &php.DomDocument dom
     * @return  &xml.transform.StyleSheet
     * @throws  xml.XMLFormatException
     */  
    public static function fromDocument(&$dom) { }
    
    /**
     * Creates a new stylesheet from a string
     *
     * @model   abstract
     * @model   static
     * @access  public
     * @param   string string
     * @return  &xml.transform.StyleSheet
     * @throws  xml.XMLFormatException
     */  
    public static function fromString($string) { }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() { }
  }
?>
