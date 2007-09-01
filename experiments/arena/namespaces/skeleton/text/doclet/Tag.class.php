<?php
/* This class is part of the XP framework
 *
 * $Id: Tag.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::doclet;

  /**
   * Represents a simple documentation tag, such as since, author, 
   * version. Given a tag (e.g. "since 1.2"), holds tag name (e.g. 
   * "since") and tag text (e.g. "1.2"). Tags with structure or 
   * which require special processing are handled by subclasses such 
   * as ParamTag (for param), SeeTag (for see and {link}), and 
   * ThrowsTag (for throws). 
   *
   * @purpose  Base class
   */
  class Tag extends lang::Object {
    public
      $name = '',
      $text = '';
    
    /**
     * Constructor
     *
     * @param   string name
     * @param   string text
     */
    public function __construct($name, $text) {
      $this->name= $name;
      $this->text= $text;
    }

    /**
     * Return the name of this tag.
     *
     * @return  string
     */
    public function name() {
      return $this->name;
    }  
    
    /**
     * Return the text of this tag, that is, portion beyond tag name.
     *
     * @return  string
     */
    public function text() {
      return $this->text;
    }  
  }
?>
