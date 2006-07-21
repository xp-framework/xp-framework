<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Base class for comments
   *
   * @deprecated
   * @see      xp://text.apidoc.FileComment
   * @see      xp://text.apidoc.ClassComment
   * @see      xp://text.apidoc.FunctionComment
   * @purpose  Base class
   */
  class Comment extends Object {
    public 
      $text;
    
    /**
     * Handles tags - override!
     *
     * @access  private
     * @param   string tag Tag (without leading "@")
     * @param   string line Line of text
     * @return  bool handled
     */
    public function _handleTag($tag, $line) {
      return FALSE;
    }
    
    /**
     * Sets text
     *
     * @access  public
     * @param   string text
     */
    public function setText($text) {
      $this->text= $text;
    }
    
    /**
     * Gets text
     *
     * @access  public
     * @return  strung
     */
    public function getText() {
      return $this->text;
    }
  
    /**
     * Create a comment from a string
     *
     * @access  public
     * @param   string str
     * @return  text.apidoc.Comment a comment object
     */
    public function fromString($str) {
    
      // Ignore annotations
      if ('#' === substr($str, 0, 1)) return;
      
      if ('/*' !== substr($str, 0, 2)) {
        throw(new FormatException('Comment format unrecognized ['.$str.']'));
      }
      
      $tag= '';
      foreach (preg_split('/[\r\n]([\s\t]*\* ?)?/', $str) as $line) {
        if (empty($line)) continue;
        switch ($line{0}) {
          case '/':
            continue 2;
            
          case '@':
            $args= preg_split('/[\s\t]+/', substr($line, 1), 2);
            $tag= $line= '';
            switch (sizeof($args)) {
              case 1: $tag= $args[0]; break;
              case 2: list($tag, $line)= $args; break;
            }
            break;
            
          case "\t":
          case ' ':
            if (!empty($tag) && (NULL != $descr)) {
              $descr.= "\n".trim($line); 
              continue 2;
            }
            break; 
        }
        
        if (FALSE === ($descr= &$this->_handleTag(strtolower($tag), $line))) {
          $this->text.= $line."\n";
        }
      }
      
      $this->text= trim(chop($this->text));
    }
  }
?>
