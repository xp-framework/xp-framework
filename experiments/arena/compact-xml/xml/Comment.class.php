<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a comment
   *
   * @purpose  purpose
   */
  class Comment extends Node {
    protected
      $text= '';
      
    /**
     * Constructor
     *
     * @param   string text
     */
    public function __construct($text) {
      $this->text= $text;
    }
    
    /**
     * Retrieve XML representation
     *
     * @param   int indent default INDENT_WRAPPED
     * @param   string inset default ''
     * @return  string XML
     */
    public function getSource($indent= INDENT_WRAPPED, $inset= '') {
      switch ($indent) {
        case INDENT_DEFAULT:
        case INDENT_WRAPPED:
          return $inset.'<!-- '.$this->text." -->\n";

        case INDENT_NONE:
          return '<!-- '.$this->text.' -->';
      }
    }
  }
?>
