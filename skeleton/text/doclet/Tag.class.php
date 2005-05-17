<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a simple documentation tag, such as @since, @author, 
   * @version. Given a tag (e.g. "@since 1.2"), holds tag name (e.g. 
   * "@since") and tag text (e.g. "1.2"). Tags with structure or 
   * which require special processing are handled by subclasses such 
   * as ParamTag (for @param), SeeTag (for @see and {@link}), and 
   * ThrowsTag (for @throws). 
   *
   * @purpose  Base class
   */
  class Tag extends Object {
    var
      $name = '',
      $text = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string text
     */
    function __construct($name, $text) {
      $this->name= $name;
      $this->text= $text;
    }

    /**
     * Return the name of this tag.
     *
     * @access  public
     * @return  string
     */
    function name() {
      return $this->name;
    }  
    
    /**
     * Return the text of this tag, that is, portion beyond tag name.
     *
     * @access  public
     * @return  string
     */
    function text() {
      return $this->text;
    }  
  }
?>
